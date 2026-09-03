<?php

namespace Cachet\Jobs;

use Cachet\Cachet;
use Cachet\Events\Beacon\BeaconSent;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Cachet\Settings\AppSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendBeaconJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 15;

    /** @var list<int> */
    public array $backoff = [60, 300];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! config('cachet.beacon')) {
            return;
        }

        $request = Http::asJson()
            ->withUserAgent(Cachet::USER_AGENT)
            ->retry(3)
            ->connectTimeout(3)
            ->timeout(10)
            ->post('https://cachethq.io/beacon', [
                'install_id' => app(AppSettings::class)->install_id,
                'php_version' => PHP_VERSION,
                'version' => Cachet::version(),
                'docker' => config('cachet.docker'),
                'database' => config('database.default'),
                'data' => [
                    'components' => Component::query()->count(),
                    'incidents' => Incident::query()->count(),
                    'metrics' => Metric::query()->count(),
                    'schedules' => Schedule::query()->count(),
                ],
            ]);

        BeaconSent::dispatchIf($request->successful());
    }
}
