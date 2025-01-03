<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\WebhookEventEnum;
use Cachet\Enums\WebhookEventSelectionEnum;
use Cachet\Filament\Resources\WebhookSubscriptionResource\Pages;
use Cachet\Models\WebhookSubscription;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebhookSubscriptionResource extends Resource
{
    protected static ?string $model = WebhookSubscription::class;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_webhooks');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('url')
                        ->label(__('cachet::webhook.form.url_label'))
                        ->helperText(__('cachet::webhook.form.url_helper'))
                        ->required()
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('secret')
                        ->label(__('cachet::webhook.form.secret_label'))
                        ->helperText(__('cachet::webhook.form.secret_helper'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('description')
                        ->label(__('cachet::webhook.form.description_label'))
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    Forms\Components\Toggle::make('send_all_events')
                        ->label(__('cachet::webhook.form.event_selection_label'))
                        ->default(true)
                        ->inline()
                        ->reactive()
                        ->columnSpanFull(),
                    Forms\Components\Section::make()->columns(2)->schema([
                        Forms\Components\CheckboxList::make('selected_events')
                            ->label(__('cachet::webhook.form.events_label'))
                            ->options(WebhookEventEnum::class)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => !$get('send_all_events')),
                ])->columnSpan(2),

                Section::make()->schema(fn (WebhookSubscription $subscription) => [
                    Forms\Components\ViewField::make('attempts')
                        ->view('cachet::filament.widgets.webhook-attempts', [
                            'attempts' => $subscription->attempts,
                        ])      
                ])
                ->visibleOn(['edit'])
                ->heading(__('cachet::webhook.attempts.heading'))
                ->columnSpan(2),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label(__('cachet::webhook.list.headers.url'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('24h_success_rate')
                    ->label(__('cachet::webhook.list.headers.24h_success_rate')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListWebhookSubscriptions::route('/'),
            'create' => Pages\CreateWebhookSubscription::route('/create'),
            'edit' => Pages\EditWebhookSubscription::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans_choice('cachet::webhook.resource_label', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('cachet::webhook.resource_label', 2);
    }
}
