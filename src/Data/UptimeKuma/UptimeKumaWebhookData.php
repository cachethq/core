<?php

namespace Cachet\Data\UptimeKuma;

use Cachet\Data\BaseData;

/**
 * Data transfer object for Uptime Kuma webhook payloads.
 * Status codes:
 * 0 = DOWN
 * 1 = UP
 * 2 = PENDING
 * 3 = MAINTENANCE
 */
final class UptimeKumaWebhookData extends BaseData
{
    public const STATUS_DOWN = 0;
    public const STATUS_UP = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_MAINTENANCE = 3;

    public function __construct(
        public readonly ?array $heartbeat = null,
        public readonly ?array $monitor = null,
        public readonly ?string $msg = null,
    ) {}

    /**
     * Get the monitor ID from the payload.
     */
    public function getMonitorId(): ?int
    {
        return $this->monitor['id'] ?? null;
    }

    /**
     * Get the monitor name from the payload.
     */
    public function getMonitorName(): ?string
    {
        return $this->monitor['name'] ?? null;
    }

    /**
     * Get the monitor URL from the payload.
     */
    public function getMonitorUrl(): ?string
    {
        return $this->monitor['url'] ?? null;
    }

    /**
     * Get the monitor type from the payload.
     */
    public function getMonitorType(): ?string
    {
        return $this->monitor['type'] ?? null;
    }

    /**
     * Get the heartbeat status.
     */
    public function getStatus(): ?int
    {
        return $this->heartbeat['status'] ?? null;
    }

    /**
     * Get the heartbeat message.
     */
    public function getHeartbeatMessage(): ?string
    {
        return $this->heartbeat['msg'] ?? $this->msg;
    }

    /**
     * Get the heartbeat time.
     */
    public function getHeartbeatTime(): ?string
    {
        return $this->heartbeat['time'] ?? null;
    }

    /**
     * Get the ping/latency value.
     */
    public function getPing(): ?float
    {
        return $this->heartbeat['ping'] ?? null;
    }

    /**
     * Check if the monitor is down.
     */
    public function isDown(): bool
    {
        return $this->getStatus() === self::STATUS_DOWN;
    }

    /**
     * Check if the monitor is up.
     */
    public function isUp(): bool
    {
        return $this->getStatus() === self::STATUS_UP;
    }

    /**
     * Check if the monitor is pending.
     */
    public function isPending(): bool
    {
        return $this->getStatus() === self::STATUS_PENDING;
    }

    /**
     * Check if the monitor is under maintenance.
     */
    public function isMaintenance(): bool
    {
        return $this->getStatus() === self::STATUS_MAINTENANCE;
    }

    /**
     * Check if this is a valid Uptime Kuma webhook payload.
     */
    public function isValid(): bool
    {
        return $this->monitor !== null
            && $this->heartbeat !== null
            && $this->getMonitorId() !== null
            && $this->getStatus() !== null;
    }
}
