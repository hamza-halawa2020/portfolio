<?php

namespace App\Data\PublicApi;

final readonly class VisitorContext
{
    public function __construct(
        public string $visitorIdHash,
        public ?string $ipHash,
        public ?string $userAgentHash,
        public bool $issuedCookie,
        public string $rateLimitKey,
        public string $cookieValue,
    ) {}
}
