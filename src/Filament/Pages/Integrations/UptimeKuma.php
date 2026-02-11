<?php

namespace Cachet\Filament\Pages\Integrations;

use Cachet\Actions\UptimeKuma\SyncMonitorsFromUptimeKuma;
use Cachet\Models\Component;
use Cachet\Services\UptimeKuma\UptimeKumaClient;
use Cachet\Settings\IntegrationSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

/**
 * @property \Filament\Schemas\Schema $form
 */
class UptimeKuma extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected string $view = 'cachet::filament.pages.integrations.uptime-kuma';

    public string $uptime_kuma_url = '';

    public string $status_page_slug = '';

    public bool $update_status = true;

    public bool $auto_incidents = true;

    public bool $auto_resolve = true;

    public bool $send_notifications = true;

    public string $webhook_secret = '';

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.integrations.label');
    }

    public static function getNavigationLabel(): string
    {
        return 'Uptime Kuma';
    }

    public function getTitle(): string
    {
        return 'Uptime Kuma Integration';
    }

    /**
     * Mount the page and load settings from database.
     */
    public function mount(): void
    {
        $settings = $this->getSettings();

        $this->form->fill([
            'uptime_kuma_url' => $settings->getUptimeKumaUrl(),
            'status_page_slug' => $settings->getUptimeKumaStatusPageSlug(),
            'update_status' => true,
            'auto_incidents' => $settings->uptime_kuma_auto_incidents,
            'auto_resolve' => $settings->uptime_kuma_auto_resolve,
            'send_notifications' => $settings->uptime_kuma_send_notifications,
            'webhook_secret' => $settings->getWebhookSecret() ?? '',
        ]);
    }

    /**
     * Get integration settings instance.
     */
    protected function getSettings(): IntegrationSettings
    {
        return app(IntegrationSettings::class);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Connection Settings')
                    ->description('Configure the connection to your Uptime Kuma status page.')
                    ->schema([
                        TextInput::make('uptime_kuma_url')
                            ->label('Uptime Kuma URL')
                            ->placeholder('http://localhost:3001')
                            ->url()
                            ->required()
                            ->helperText('The URL of your Uptime Kuma instance.'),

                        TextInput::make('status_page_slug')
                            ->label('Status Page Slug')
                            ->placeholder('united-codes')
                            ->required()
                            ->helperText('The slug of your public status page in Uptime Kuma (found in the URL: /status/YOUR-SLUG).'),
                    ]),

                Section::make('Sync Options')
                    ->description('Configure how monitors and groups should be synced.')
                    ->schema([
                        Toggle::make('update_status')
                            ->label('Sync component status')
                            ->helperText('Update component status based on monitor status (UP = Operational, DOWN = Major Outage).')
                            ->default(true),

                        Toggle::make('auto_incidents')
                            ->label('Auto-create incidents')
                            ->helperText('Automatically create incidents when monitors go down via webhooks.')
                            ->default(true),

                        Toggle::make('auto_resolve')
                            ->label('Auto-resolve incidents')
                            ->helperText('Automatically resolve incidents when monitors come back up.')
                            ->default(true),

                        Toggle::make('send_notifications')
                            ->label('Send notifications')
                            ->helperText('Send notifications when incidents are auto-created.')
                            ->default(true),
                    ]),

                Section::make('Webhook Security')
                    ->description('Configure webhook authentication between Uptime Kuma and Cachet.')
                    ->schema([
                        TextInput::make('webhook_url')
                            ->label('Webhook URL')
                            ->disabled()
                            ->default(fn () => url('/api/integrations/uptime-kuma/webhook'))
                            ->helperText('Add this URL as a Webhook notification in Uptime Kuma.'),

                        TextInput::make('webhook_secret')
                            ->label('Webhook Secret')
                            ->password()
                            ->revealable()
                            ->copyable()
                            ->helperText(fn () => new HtmlString(
                                'Copy this secret and add it to Uptime Kuma notification settings.<br>'
                                . '<strong>Custom Headers:</strong> <code>{"X-Webhook-Secret": "YOUR_SECRET_HERE"}</code>'
                            ))
                            ->suffixAction(
                                Action::make('generate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->tooltip('Generate new secret')
                                    ->requiresConfirmation()
                                    ->modalHeading('Generate New Webhook Secret')
                                    ->modalDescription('This will generate a new secret and invalidate the old one. You will need to update Uptime Kuma with the new secret.')
                                    ->action(fn () => $this->generateWebhookSecret())
                            ),
                    ]),
            ]);
    }

    /**
     * Test the connection to Uptime Kuma
     */
    public function testConnection(): void
    {
        $this->saveSettings();

        $client = new UptimeKumaClient($this->uptime_kuma_url, $this->status_page_slug);

        if ($client->ping()) {
            $data = $client->getStatusPageData();

            if ($data) {
                $groupCount = count($data['publicGroupList'] ?? []);
                $monitorCount = array_sum(array_map(fn ($g) => count($g['monitorList'] ?? []), $data['publicGroupList'] ?? []));

                Notification::make()
                    ->title('Connection Successful')
                    ->body("Connected to Uptime Kuma. Found {$groupCount} groups with {$monitorCount} monitors.")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Connection Partial')
                    ->body("Connected to Uptime Kuma but couldn't fetch status page '{$this->status_page_slug}'. Check the slug.")
                    ->warning()
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Connection Failed')
                ->body('Could not connect to Uptime Kuma. Please check the URL and ensure Uptime Kuma is running.')
                ->danger()
                ->send();
        }
    }

    /**
     * Sync monitors and groups from Uptime Kuma.
     */
    public function syncMonitors(): void
    {
        $this->validate();
        $this->saveSettings();

        $sync = app(SyncMonitorsFromUptimeKuma::class);
        $result = $sync->handle(updateStatus: $this->update_status);

        if (! empty($result['errors'])) {
            Notification::make()
                ->title('Sync Failed')
                ->body(implode("\n", $result['errors']))
                ->danger()
                ->send();

            return;
        }

        if ($result['total_synced'] === 0) {
            Notification::make()
                ->title('No Monitors Found')
                ->body("No monitors found. Make sure you have a public status page configured in Uptime Kuma with slug: {$this->status_page_slug}")
                ->warning()
                ->send();

            return;
        }
        $settings = $this->getSettings();
        $settings->uptime_kuma_last_sync = now();
        $settings->save();

        Notification::make()
            ->title('Sync Completed!')
            ->body("Groups: {$result['groups_created']} created, {$result['groups_updated']} updated. Components: {$result['components_created']} created, {$result['components_updated']} updated.")
            ->success()
            ->send();
    }

    /**
     * Sync status only for existing linked components.
     */
    public function syncStatusOnly(): void
    {        $this->saveSettings();

        $sync = app(SyncMonitorsFromUptimeKuma::class);
        $result = $sync->syncStatusOnly();

        if (! empty($result['errors'])) {
            Notification::make()
                ->title('Status Sync Failed')
                ->body(implode(', ', $result['errors']))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title('Status Synced')
            ->body("Updated status for {$result['updated']} components.")
            ->success()
            ->send();
    }

    /**
     * Save current form values to persistent database settings.
     */
    protected function saveSettings(): void
    {
        $settings = $this->getSettings();

        $settings->uptime_kuma_url = $this->uptime_kuma_url;
        $settings->uptime_kuma_status_page_slug = $this->status_page_slug;
        $settings->uptime_kuma_auto_incidents = $this->auto_incidents;
        $settings->uptime_kuma_auto_resolve = $this->auto_resolve;
        $settings->uptime_kuma_send_notifications = $this->send_notifications;

        if (! empty($this->webhook_secret)) {
            $settings->uptime_kuma_webhook_secret = $this->webhook_secret;
        }

        $settings->save();
        config([
            'cachet.uptime_kuma.url' => $this->uptime_kuma_url,
            'cachet.uptime_kuma.status_page_slug' => $this->status_page_slug,
            'cachet.uptime_kuma.auto_resolve' => $this->auto_resolve,
            'cachet.uptime_kuma.send_notifications' => $this->send_notifications,
            'cachet.uptime_kuma.webhook_secret' => $settings->getWebhookSecret(),
        ]);
    }

    /**
     * Generate a new webhook secret.
     */
    public function generateWebhookSecret(): void
    {
        $settings = $this->getSettings();
        $newSecret = $settings->generateWebhookSecret();

        $this->webhook_secret = $newSecret;
        config(['cachet.uptime_kuma.webhook_secret' => $newSecret]);

        Notification::make()
            ->title('Webhook Secret Generated')
            ->body('New secret generated. Copy it and update Uptime Kuma notification settings.')
            ->success()
            ->send();
    }
}
