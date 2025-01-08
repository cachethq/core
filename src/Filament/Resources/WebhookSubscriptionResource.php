<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\WebhookEventEnum;
use Cachet\Filament\Resources\WebhookSubscriptionResource\Pages;
use Cachet\Models\WebhookSubscription;
use Cachet\View\Htmlable\TextWithLink;
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

    public static function secretField()
    {
        return Forms\Components\TextInput::make('secret')
            ->label(__('cachet::webhook.form.secret_label'))
            ->helperText(
                TextWithLink::make(
                    text: __('cachet::webhook.form.secret_helper'),
                    url: 'https://docs.cachethq.io/v3.x/guide/webhooks',
                )
            )
            ->password()
            ->revealable()
            ->required()
            ->maxLength(255)
            ->columnSpanFull()
            ->autocomplete(false);
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

                    self::secretField()
                        ->visibleOn(['create']),

                    Forms\Components\Actions\ActionContainer::make(
                        \Filament\Forms\Components\Actions\Action::make('edit_secret')
                            ->label(__('cachet::webhook.form.edit_secret_label'))
                            ->modal()
                            ->form([
                                self::secretField()
                            ])
                            ->action(function (array $data, WebhookSubscription $webhookSubscription) {
                                $webhookSubscription->update($data);
                            })
                            ->modalSubmitActionLabel(__('cachet::webhook.form.update_secret_label'))
                    )->visibleOn(['edit']),

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
                ])->columnSpan(4),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url')
                    ->label(__('cachet::webhook.list.headers.url'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('success_rate_24h')
                    ->label(__('cachet::webhook.list.headers.success_rate_24h')),
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
