<?php

namespace Cachet\Jobs;

use Cachet\Cachet;
use Cachet\Events\Beacon\BeaconSent;
use Cachet\Models\Component;
use Cachet\Models\Incident;
use Cachet\Models\Metric;
use Cachet\Models\Schedule;
use Illuminate\Support\Facades\Http;

class SendBeaconJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! config('cachet.beacon')) {
            return;
        }

        $request = Http::asJson()->post('https://cachethq.io/beacon', [
            'install_id' => null, // @todo Figure out this again.
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
