<?php

namespace Cachet\Filament\Pages\Settings;

use Cachet\Mail\TestMail;
use Cachet\Settings\MailSettings;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Throwable;

use function __;

class ManageNotifications extends SettingsPage
{
    protected static string $settings = MailSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_notifications');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('cachet::settings.manage_notifications.subscriptions_section_title'))
                    ->schema([
                        Toggle::make('allow_subscribers')
                            ->label(__('cachet::settings.manage_notifications.allow_subscribers_label'))
                            ->helperText(__('cachet::settings.manage_notifications.allow_subscribers_helper')),
                    ]),

                Section::make(__('cachet::settings.manage_notifications.long_running_section_title'))
                    ->description(__('cachet::settings.manage_notifications.long_running_section_description'))
                    ->columns(2)
                    ->schema([
                        Toggle::make('notify_long_running_incidents')
                            ->label(__('cachet::settings.manage_notifications.notify_long_running_incidents_label'))
                            ->helperText(__('cachet::settings.manage_notifications.notify_long_running_incidents_helper'))
                            ->reactive(),
                        TextInput::make('long_running_incident_hours')
                            ->label(__('cachet::settings.manage_notifications.long_running_incident_hours_label'))
                            ->helperText(__('cachet::settings.manage_notifications.long_running_incident_hours_helper'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(720)
                            ->suffix(__('cachet::settings.manage_notifications.long_running_incident_hours_suffix'))
                            ->required(fn (Get $get): bool => $get('notify_long_running_incidents') === true)
                            ->visible(fn (Get $get): bool => $get('notify_long_running_incidents') === true),
                    ]),

                Section::make(__('cachet::settings.manage_notifications.mail_section_title'))
                    ->description(__('cachet::settings.manage_notifications.mail_section_description'))
                    ->columns(2)
                    ->schema([
                        Select::make('mailer')
                            ->label(__('cachet::settings.manage_notifications.mailer_label'))
                            ->helperText(__('cachet::settings.manage_notifications.mailer_helper'))
                            ->placeholder(__('cachet::settings.manage_notifications.mailer_placeholder'))
                            ->options([
                                'smtp' => __('cachet::settings.manage_notifications.mailers.smtp'),
                                'sendmail' => __('cachet::settings.manage_notifications.mailers.sendmail'),
                                'log' => __('cachet::settings.manage_notifications.mailers.log'),
                            ])
                            ->nullable()
                            ->reactive(),

                        TextInput::make('host')
                            ->label(__('cachet::settings.manage_notifications.host_label'))
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('mailer') === 'smtp')
                            ->visible(fn (Get $get): bool => $get('mailer') === 'smtp'),

                        TextInput::make('port')
                            ->label(__('cachet::settings.manage_notifications.port_label'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535)
                            ->placeholder('587')
                            ->nullable()
                            ->visible(fn (Get $get): bool => $get('mailer') === 'smtp'),

                        TextInput::make('username')
                            ->label(__('cachet::settings.manage_notifications.username_label'))
                            ->maxLength(255)
                            ->nullable()
                            ->autocomplete(false)
                            ->visible(fn (Get $get): bool => $get('mailer') === 'smtp'),

                        TextInput::make('password')
                            ->label(__('cachet::settings.manage_notifications.password_label'))
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->nullable()
                            ->autocomplete(false)
                            ->visible(fn (Get $get): bool => $get('mailer') === 'smtp'),
                    ]),

                Section::make(__('cachet::settings.manage_notifications.from_section_title'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('from_address')
                            ->label(__('cachet::settings.manage_notifications.from_address_label'))
                            ->helperText(__('cachet::settings.manage_notifications.from_address_helper'))
                            ->email()
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => filled($get('mailer'))),

                        TextInput::make('from_name')
                            ->label(__('cachet::settings.manage_notifications.from_name_label'))
                            ->helperText(__('cachet::settings.manage_notifications.from_name_helper'))
                            ->maxLength(255)
                            ->nullable(),
                    ]),
            ]);
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestEmail')
                ->label(__('cachet::settings.manage_notifications.test_email_label'))
                ->icon('heroicon-o-envelope')
                ->action(fn () => $this->sendTestEmail()),
        ];
    }

    public function sendTestEmail(): void
    {
        $settings = $this->settingsFromFormState();
        $email = Filament::auth()->user()->email;

        try {
            $mailer = $settings->configured()
                ? Mail::build($settings->toMailerConfig())
                : Mail::mailer();

            $mailer->send(
                (new TestMail)
                    ->to($email)
                    ->from(
                        $settings->from_address ?? config('mail.from.address'),
                        $settings->from_name ?? config('mail.from.name'),
                    )
            );
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->title(__('cachet::settings.manage_notifications.test_email_failed'))
                ->body($exception->getMessage())
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title(__('cachet::settings.manage_notifications.test_email_sent', ['email' => $email]))
            ->send();
    }

    /**
     * Build a settings instance from the current, unsaved form state.
     */
    private function settingsFromFormState(): MailSettings
    {
        $state = $this->form->getState();

        $state['port'] = filled($state['port'] ?? null) ? (int) $state['port'] : null;

        return (clone app(MailSettings::class))->fill($state);
    }
}
