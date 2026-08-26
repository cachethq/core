<?php

namespace Cachet\Data;

final readonly class ResolvedPublicUrl
{
    public function __construct(
        public string $url,
        public string $host,
        public int $port,
        public string $address,
    ) {}

    /**
     * Format the validated DNS result for cURL address pinning.
     */
    public function curlResolve(): string
    {
        return sprintf('%s:%d:%s', $this->host, $this->port, $this->address);
    }
}
