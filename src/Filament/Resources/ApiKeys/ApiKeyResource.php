<?php

namespace Cachet\Filament\Resources\ApiKeys;

use Cachet\Cachet;
use Cachet\Filament\Resources\ApiKeys\Pages\CreateApiKey;
use Cachet\Filament\Resources\ApiKeys\Pages\ListApiKeys;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeyResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tokenable_type', auth()->user()::class)
            ->where('tokenable_id', auth()->id());
    }

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.settings.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.settings.items.manage_api_keys');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label(__('cachet::api_key.form.name_label'))
                        ->required()
                        ->unique('api_keys', 'name')
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->autofocus()
                        ->autocomplete(false),
                    DatePicker::make('expires_at')
                        ->label(__('cachet::api_key.form.expires_at_label'))
                        ->helperText(__('cachet::api_key.form.expires_at_helper'))
                        ->nullable()
                        ->rules(['after:today'])
                        ->validationMessages(['after' => __('cachet::api_key.form.expires_at_validation')])
                        ->placeholder(null),
                    CheckboxList::make('abilities')
                        ->label(__('cachet::api_key.form.abilities_label'))
                        ->hint(__('cachet::api_key.form.abilities_hint'))
                        ->hintColor('warning')
                        ->options(self::getAbilities())
                        ->columns(3),
                ])->columnSpan(4),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::api_key.list.headers.name'))
                    ->searchable(),
                TextColumn::make('abilities')
                    ->label(__('cachet::api_key.list.headers.abilities'))
                    ->color('gray')
                    ->badge()
                    ->limitList(3),
                TextColumn::make('created_at')
                    ->label(__('cachet::api_key.list.headers.created_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label(__('cachet::api_key.list.headers.expires_at'))
                    ->sortable()
                    ->color(fn (PersonalAccessToken $record) => $record->expires_at ? null : 'gray')
                    ->badge(fn (PersonalAccessToken $record) => ! $record->expires_at)
                    ->getStateUsing(fn (PersonalAccessToken $record) => $record->expires_at?->format($table->getDefaultDateDisplayFormat()) ?? 'N/A'),
                TextColumn::make('updated_at')
                    ->label(__('cachet::api_key.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('expired')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('expires_at', '<', now())),
            ])
            ->recordActions([
                DeleteAction::make('revoke')
                    ->label(__('cachet::api_key.list.actions.revoke')),
            ])
            ->toolbarActions([
                BulkAction::make('revoke')
                    ->label(__('cachet::api_key.list.actions.revoke'))
                    ->action(fn (Collection $records) => $records->each->delete())
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiKeys::route('/'),
            'create' => CreateApiKey::route('/create'),
        ];
    }

    public static function getModelLabel(): string
    {
        return trans_choice('cachet::api_key.resource_label', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('cachet::api_key.resource_label', 2);
    }

    /** @return array<string, string> */
    private static function getAbilities(): array
    {
        $abilities = [];

        foreach (Cachet::getResourceApiAbilities() as $resource => $apiAbilities) {
            foreach ($apiAbilities as $ability) {
                $key = "{$resource}.{$ability}";
                $abilities[$key] = Str::headline(__('cachet::api_key.abilities_label', [
                    'ability' => $ability,
                    'resource' => Str::plural($resource),
                ]));
            }
        }

        return $abilities;
    }
}
