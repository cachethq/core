<?php

namespace Cachet\Jobs;

use Cachet\Actions\Component\ChangeComponentStatus;
use Cachet\Cachet;
use Cachet\Data\Checks\CheckResult;
use Cachet\Enums\ComponentStatusSourceEnum;
use Cachet\Models\Component;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckComponent implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 300;

    public bool $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(public Component $component) {}

    public function uniqueId(): string
    {
        return (string) $this->component->getKey();
    }

    /**
     * Execute the job.
     *
     * The check is always recorded, but the component's baseline status is left
     * alone while a maintenance window is in progress: downtime is expected
     * then, and reporting it as an outage is exactly what the window exists to
     * prevent.
     */
    public function handle(): void
    {
        $this->component->refresh();

        if (! $this->component->enabled || ! $this->component->checked || blank($this->component->link)) {
            return;
        }

        $attempts = 1;

        try {
            $response = Http::withUserAgent(Cachet::USER_AGENT)
                ->retry(3, function (int $attempt) use (&$attempts): int {
                    $attempts = $attempt;

                    return $attempt * 100;
                })
                ->connectTimeout(2)
                ->timeout(3)
                ->get((string) $this->component->link);

            $result = CheckResult::fromResponse(
                $response,
                $attempts,
                $response->transferStats?->getTransferTime(),
            );
        } catch (RequestException|ConnectionException $e) {
            $result = CheckResult::fromException($e);
        }

        $checkedAt = now();

        $this->component->checks()->create([
            ...$result->toCheckAttributes(),
            'checked_at' => $checkedAt,
        ]);

        if ($this->component->isUnderMaintenance()) {
            $this->component->update(['checked_at' => $checkedAt]);

            return;
        }

        app(ChangeComponentStatus::class)->handle(
            $this->component,
            $result->status,
            ComponentStatusSourceEnum::Monitor,
            attributes: ['checked_at' => $checkedAt],
        );
    }
}
