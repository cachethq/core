<?php

namespace Cachet\Filament\Resources\Subscribers;

use Cachet\Filament\Resources\Subscribers\Pages\CreateSubscriber;
use Cachet\Filament\Resources\Subscribers\Pages\EditSubscriber;
use Cachet\Filament\Resources\Subscribers\Pages\ListSubscribers;
use Cachet\Models\Subscriber;
use Cachet\Settings\MailSettings;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-subscribers';

    public static function shouldRegisterNavigation(): bool
    {
        return app(MailSettings::class)->allow_subscribers;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    TextInput::make('email')
                        ->label(__('cachet::subscriber.form.email_label'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    DateTimePicker::make('email_verified_at')
                        ->label(__('cachet::subscriber.form.verified_at_label')),
                    //                Forms\Components\TextInput::make('phone_number')
                    //                    ->tel(),
                    //                Forms\Components\TextInput::make('slack_webhook_url'),
                    KeyValue::make('meta')
                        ->label(__('cachet::subscriber.form.meta_label'))
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('cachet::subscriber.list.headers.email'))
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label(__('cachet::subscriber.list.headers.phone_number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slack_webhook_url')
                    ->label(__('cachet::subscriber.list.headers.slack_webhook_url'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label(__('cachet::subscriber.list.headers.verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('cachet::subscriber.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::subscriber.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('verify')
                    ->label(__('cachet::subscriber.list.actions.verify_label'))
                    ->color('warning')
                    ->action(fn (Subscriber $record) => $record->verify())
                    ->requiresConfirmation()
                    ->hidden(fn (Subscriber $record): bool => $record->hasVerifiedEmail()),
                Action::make('resend-verification')
                    ->label(__('cachet::subscriber.list.actions.resend_verification_label'))
                    ->color('gray')
                    ->action(function (Subscriber $record) {
                        $record->sendEmailVerificationNotification();

                        Notification::make()
                            ->title(__('cachet::subscriber.resend_verification.success_title'))
                            ->body(__('cachet::subscriber.resend_verification.success_body', ['email' => $record->email]))
                            ->success()
                            ->send();
                    })
                    ->hidden(fn (Subscriber $record): bool => $record->hasVerifiedEmail()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::subscriber.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::subscriber.list.empty_state.description'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscribers::route('/'),
            'create' => CreateSubscriber::route('/create'),
            'edit' => EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::subscriber.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::subscriber.resource_label', 2);
    }
}
