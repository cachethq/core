<?php

namespace Cachet\Commands;

use Cachet\Cachet;
use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class CheckComponentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of all components.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Component::query()
            ->enabled()
            ->checked()
            ->whereNotNull('link')
            ->get()
            ->each(function (Component $component) {
                $attempts = 1;

                try {
                    $response = Http::withUserAgent(Cachet::USER_AGENT)
                        ->retry(3, function (int $attempt) use (&$attempts): int {
                            $attempts = $attempt;

                            return $attempt * 100;
                        })
                        ->timeout(3)
                        ->get($component->link);
                } catch (RequestException|ConnectionException $e) {
                    $status = match (true) {
                        $e->getCode() >= 400 => ComponentStatusEnum::partial_outage,
                        $e->getCode() >= 500 => ComponentStatusEnum::major_outage,
                        default => ComponentStatusEnum::partial_outage,
                    };

                    $component->update([
                        'status' => $status,
                        'checked_at' => now(),
                    ]);

                    return;
                }

                $status = match (true) {
                    $response->successful() && $attempts === 1 => ComponentStatusEnum::operational,
                    $response->successful() && $attempts > 1 => ComponentStatusEnum::performance_issues,
                    $response->status() >= 400 => ComponentStatusEnum::partial_outage,
                    default => ComponentStatusEnum::operational,
                };

                $component->update([
                    'status' => $status,
                    'checked_at' => now(),
                ]);
            });
    }
}
