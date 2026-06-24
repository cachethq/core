<?php

namespace Cachet\Data\Checks;

use Cachet\Data\BaseData;
use Cachet\Enums\ComponentStatusEnum;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

final class CheckResult extends BaseData
{
    public function __construct(
        public readonly ComponentStatusEnum $status,
        public readonly bool $successful,
        public readonly ?int $responseCode = null,
        public readonly ?int $responseTime = null,
        public readonly ?string $error = null,
    ) {}

    /**
     * Build a check result from a successful HTTP exchange.
     */
    public static function fromResponse(Response $response, int $attempts, ?float $transferTime = null): self
    {
        $status = match (true) {
            $response->successful() && $attempts === 1 => ComponentStatusEnum::operational,
            $response->successful() => ComponentStatusEnum::performance_issues,
            $response->status() >= 500 => ComponentStatusEnum::major_outage,
            $response->status() >= 400 => ComponentStatusEnum::partial_outage,
            default => ComponentStatusEnum::operational,
        };

        return new self(
            status: $status,
            successful: $response->successful(),
            responseCode: $response->status(),
            responseTime: $transferTime !== null ? (int) round($transferTime * 1000) : null,
        );
    }

    /**
     * Build a check result from a failed HTTP request.
     */
    public static function fromException(RequestException|ConnectionException $e): self
    {
        $status = match (true) {
            $e->getCode() >= 500 => ComponentStatusEnum::major_outage,
            default => ComponentStatusEnum::partial_outage,
        };

        return new self(
            status: $status,
            successful: false,
            responseCode: $e->getCode() ?: null,
            error: $e->getMessage(),
        );
    }

    /**
     * The attributes used to persist this result as a component check.
     *
     * @return array<string, mixed>
     */
    public function toCheckAttributes(): array
    {
        return [
            'status' => $this->status,
            'successful' => $this->successful,
            'response_code' => $this->responseCode,
            'response_time' => $this->responseTime,
        ];
    }
}
