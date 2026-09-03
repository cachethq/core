<?php

namespace Cachet\Data;

final class ResolvedPublicUrl extends BaseData
{
    public function __construct(
        public readonly string $url,
        public readonly string $host,
        public readonly int $port,
        public readonly string $address,
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
