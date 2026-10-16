<?php

namespace Cachet\Actions\Integrations;

use Cachet\Data\ResolvedPublicUrl;
use InvalidArgumentException;

class ResolvePublicUrl
{
    /** @var list<string> */
    private const BLOCKED_IPV4_RANGES = [
        '0.0.0.0/8',
        '10.0.0.0/8',
        '100.64.0.0/10',
        '127.0.0.0/8',
        '169.254.0.0/16',
        '172.16.0.0/12',
        '192.0.0.0/24',
        '192.0.2.0/24',
        '192.88.99.0/24',
        '192.168.0.0/16',
        '198.18.0.0/15',
        '198.51.100.0/24',
        '203.0.113.0/24',
        '224.0.0.0/4',
        '240.0.0.0/4',
    ];

    /** @var list<string> */
    private const BLOCKED_IPV6_RANGES = [
        '::/8',
        '2001::/23',
        '2001:db8::/32',
        '2002::/16',
        '3fff::/20',
        '5f00::/16',
        'fc00::/7',
        'fe80::/10',
        'fec0::/10',
        'ff00::/8',
    ];

    /**
     * Resolve and validate a URL before making an outbound request.
     */
    public function handle(string $url): ResolvedPublicUrl
    {
        $parts = parse_url($url);

        if (! is_array($parts) || ! isset($parts['scheme'], $parts['host'])) {
            throw new InvalidArgumentException('The URL is invalid.');
        }

        $scheme = strtolower($parts['scheme']);

        if (! in_array($scheme, ['http', 'https'], true) || isset($parts['user'], $parts['pass'], $parts['query'], $parts['fragment'])) {
            throw new InvalidArgumentException('The URL is invalid.');
        }

        $host = trim($parts['host'], '[]');
        $addresses = $this->resolveAddresses($host);

        if ($addresses === [] || collect($addresses)->contains(fn (string $address): bool => ! $this->isPublicAddress($address))) {
            throw new InvalidArgumentException('The URL must resolve to a public address.');
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        $authority = str_contains($host, ':') ? "[{$host}]" : $host;

        if (isset($parts['port'])) {
            $authority .= ":{$port}";
        }

        $path = rtrim($parts['path'] ?? '', '/');

        return new ResolvedPublicUrl(
            url: "{$scheme}://{$authority}{$path}/json",
            host: $host,
            port: $port,
            address: $addresses[0],
        );
    }

    /**
     * Resolve every address so mixed public/private DNS answers are rejected.
     *
     * @return list<string>
     */
    private function resolveAddresses(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        if ($records === false) {
            return [];
        }

        $addresses = [];

        foreach ($records as $record) {
            $address = $record['ip'] ?? $record['ipv6'] ?? null;

            if (is_string($address)) {
                $addresses[] = $address;
            }
        }

        return array_values(array_unique($addresses));
    }

    private function isPublicAddress(string $address): bool
    {
        $ranges = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            ? self::BLOCKED_IPV4_RANGES
            : self::BLOCKED_IPV6_RANGES;

        if (filter_var($address, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        return ! collect($ranges)->contains(fn (string $range): bool => $this->isInRange($address, $range));
    }

    /**
     * Compare packed addresses to avoid platform-dependent integer handling.
     */
    private function isInRange(string $address, string $range): bool
    {
        [$network, $prefixLength] = explode('/', $range);
        $addressBytes = inet_pton($address);
        $networkBytes = inet_pton($network);

        if ($addressBytes === false || $networkBytes === false || strlen($addressBytes) !== strlen($networkBytes)) {
            return false;
        }

        $prefixLength = (int) $prefixLength;
        $wholeBytes = intdiv($prefixLength, 8);
        $remainingBits = $prefixLength % 8;

        if (substr($addressBytes, 0, $wholeBytes) !== substr($networkBytes, 0, $wholeBytes)) {
            return false;
        }

        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainingBits)) & 0xFF;

        return (ord($addressBytes[$wholeBytes]) & $mask) === (ord($networkBytes[$wholeBytes]) & $mask);
    }
}
