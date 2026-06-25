<?php

namespace Cachet\Jobs;

use Cachet\Cachet;
use Cachet\Data\Checks\CheckResult;
use Cachet\Models\Component;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckComponent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Component $component) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attempts = 1;

        try {
            $response = Http::withUserAgent(Cachet::USER_AGENT)
                ->retry(3, function (int $attempt) use (&$attempts): int {
                    $attempts = $attempt;

                    return $attempt * 100;
                })
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

        $this->component->update([
            'status' => $result->status,
            'checked_at' => $checkedAt,
        ]);
    }
}
