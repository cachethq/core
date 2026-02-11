<?php

namespace Cachet\Commands;

use Cachet\Actions\UptimeKuma\SyncMonitorsFromUptimeKuma;
use Cachet\Services\UptimeKuma\UptimeKumaClient;
use Illuminate\Console\Command;

class SyncUptimeKumaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:sync-uptime-kuma
        {--status-only : Only sync status of existing linked components}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync monitors and groups from Uptime Kuma to Cachet components';

    /**
     * Execute the console command.
     */
    public function handle(SyncMonitorsFromUptimeKuma $sync, UptimeKumaClient $client): int
    {
        $slug = config('cachet.uptime_kuma.status_page_slug', 'default');

        $this->info('Connecting to Uptime Kuma at '.$client->getBaseUrl().'...');
        $this->info("Using status page slug: {$slug}");
        $this->line('');

        if (! $client->ping()) {
            $this->error('Cannot connect to Uptime Kuma. Please check your configuration.');
            $this->line('');
            $this->line('Make sure:');
            $this->line('  1. Uptime Kuma is running at '.config('cachet.uptime_kuma.url'));
            $this->line('  2. You have a public status page configured with slug "'.$slug.'"');
            $this->line('  3. The URL is accessible from this server');

            return self::FAILURE;
        }

        $this->info('Connected to Uptime Kuma!');
        $this->line('');

        if ($this->option('status-only')) {
            $this->info('Syncing status only...');
            $result = $sync->syncStatusOnly();

            $this->info("Updated: {$result['updated']} components");
        } else {
            $this->info('Fetching groups and monitors from Uptime Kuma...');

            $result = $sync->handle(updateStatus: true);

            $this->line('');
            $this->info('Sync completed!');
            $this->line("  Groups created: {$result['groups_created']}");
            $this->line("  Groups updated: {$result['groups_updated']}");
            $this->line("  Components created: {$result['components_created']}");
            $this->line("  Components updated: {$result['components_updated']}");
            $this->line("  Total synced: {$result['total_synced']}");

            if (! empty($result['errors'])) {
                $this->line('');
                $this->warn('Errors:');
                foreach ($result['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
        }

        return self::SUCCESS;
    }
}
