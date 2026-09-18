<?php

namespace App\Services\PublicApi;

use App\Data\PublicApi\VisitorContext;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RuntimeException;

final class VisitorIdentity
{
    public function resolve(Request $request): VisitorContext
    {
        $cookieName = (string) config('portfolio.visitor.cookie', 'portfolio_visitor');
        $cookieValue = $request->cookies->get($cookieName);
        $visitorId = is_string($cookieValue) ? $this->decryptCookieValue($cookieValue) : null;
        $issuedCookie = false;

        if (! is_string($visitorId) && is_string($cookieValue) && Str::isUuid($cookieValue)) {
            $visitorId = $cookieValue;
            $cookieValue = Crypt::encryptString($visitorId);
            $issuedCookie = true;
        }

        if (! is_string($visitorId) || ! Str::isUuid($visitorId)) {
            $visitorId = (string) Str::uuid();
            $cookieValue = Crypt::encryptString($visitorId);
            $issuedCookie = true;
        }

        $visitorIdHash = $this->hmac($visitorId);
        $ipHash = $this->nullableHmac($request->ip());
        $userAgentHash = $this->nullableHmac($this->normalizeUserAgent($request->userAgent()));

        return new VisitorContext(
            visitorIdHash: $visitorIdHash,
            ipHash: $ipHash,
            userAgentHash: $userAgentHash,
            issuedCookie: $issuedCookie,
            rateLimitKey: hash_hmac('sha256', implode('|', array_filter([$visitorIdHash, $ipHash])), $this->secret()),
            cookieValue: (string) $cookieValue,
        );
    }

    public function rateLimitKey(Request $request): string
    {
        $context = $request->attributes->get('visitor_context');

        if ($context instanceof VisitorContext) {
            return $context->rateLimitKey;
        }

        return $this->resolve($request)->rateLimitKey;
    }

    private function nullableHmac(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === null || $value === '' ? null : $this->hmac($value);
    }

    private function hmac(string $value): string
    {
        return hash_hmac('sha256', $value, $this->secret());
    }

    private function secret(): string
    {
        $secret = config('portfolio.visitor.hash_secret');

        if (! is_string($secret) || $secret === '') {
            throw new RuntimeException('Visitor hash secret is not configured.');
        }

        return $secret;
    }

    private function decryptCookieValue(string $cookieValue): ?string
    {
        try {
            return Crypt::decryptString($cookieValue);
        } catch (DecryptException) {
            return null;
        }
    }

    private function normalizeUserAgent(?string $userAgent): ?string
    {
        $userAgent = $userAgent === null ? null : trim($userAgent);

        return $userAgent === '' ? null : Str::limit($userAgent, 255, '');
    }
}
