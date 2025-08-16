<?php

namespace Cachet\Filament\Resources\WebhookSubscriptions;

use Cachet\Enums\WebhookEventEnum;
use Cachet\Filament\Resources\WebhookSubscriptions\Pages\CreateWebhookSubscription;
use Cachet\Filament\Resources\WebhookSubscriptions\Pages\EditWebhookSubscription;
use Cachet\Filament\Resources\WebhookSubscriptions\Pages\ListWebhookSubscriptions;
use Cachet\Models\WebhookSubscription;
use Cachet\View\Htmlable\TextWithLink;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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
        return TextInput::make('secret')
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('url')
                        ->label(__('cachet::webhook.form.url_label'))
                        ->helperText(__('cachet::webhook.form.url_helper'))
                        ->required()
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),

                    self::secretField()
                        ->visibleOn(['create']),

                    Actions::make([
                        Action::make('edit_secret')
                            ->label(__('cachet::webhook.form.edit_secret_label'))
                            ->modal()
                            ->schema([
                                self::secretField(),
                            ])
                            ->action(function (array $data, WebhookSubscription $webhookSubscription) {
                                $webhookSubscription->update($data);
                            })
                            ->modalSubmitActionLabel(__('cachet::webhook.form.update_secret_label')),
                    ])->visibleOn(['edit']),

                    TextInput::make('description')
                        ->label(__('cachet::webhook.form.description_label'))
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autocomplete(false),
                    Toggle::make('send_all_events')
                        ->label(__('cachet::webhook.form.event_selection_label'))
                        ->default(true)
                        ->inline()
                        ->reactive()
                        ->columnSpanFull(),
                    Section::make()->columns(2)->schema([
                        CheckboxList::make('selected_events')
                            ->label(__('cachet::webhook.form.events_label'))
                            ->options(WebhookEventEnum::class)
                            ->columnSpanFull(),
                    ])
                        ->visible(fn (Get $get) => ! $get('send_all_events')),
                ])->columnSpan(4),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label(__('cachet::webhook.list.headers.url'))
                    ->searchable(),
                TextColumn::make('success_rate_24h')
                    ->label(__('cachet::webhook.list.headers.success_rate_24h')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListWebhookSubscriptions::route('/'),
            'create' => CreateWebhookSubscription::route('/create'),
            'edit' => EditWebhookSubscription::route('/{record}/edit'),
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
