<?php

namespace App\Http\Middleware;

use App\Services\PublicApi\VisitorIdentity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolvePublicVisitor
{
    public function __construct(
        private VisitorIdentity $visitors,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $context = $this->visitors->resolve($request);
        $request->attributes->set('visitor_context', $context);

        /** @var Response $response */
        $response = $next($request);

        if ($context->issuedCookie) {
            $response->headers->setCookie(Cookie::create(
                name: (string) config('portfolio.visitor.cookie', 'portfolio_visitor'),
                value: $context->cookieValue,
                expire: now()->addMinutes((int) config('portfolio.visitor.lifetime_minutes', 525600))->getTimestamp(),
                path: '/',
                domain: config('portfolio.visitor.domain') ?: null,
                secure: app()->isProduction(),
                httpOnly: true,
                raw: false,
                sameSite: Cookie::SAMESITE_LAX,
            ));
        }

        return $response;
    }
}
