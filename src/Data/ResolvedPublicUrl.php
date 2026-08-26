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
        $host = str_contains($this->host, ':') ? "[{$this->host}]" : $this->host;
        $address = str_contains($this->address, ':') ? "[{$this->address}]" : $this->address;

        return sprintf('%s:%d:%s', $host, $this->port, $address);
    }
}
