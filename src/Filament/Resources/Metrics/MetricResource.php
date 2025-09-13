<?php

namespace Cachet\Filament\Resources\Metrics;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\Metrics\Pages\CreateMetric;
use Cachet\Filament\Resources\Metrics\Pages\EditMetric;
use Cachet\Filament\Resources\Metrics\Pages\ListMetrics;
use Cachet\Models\Metric;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MetricResource extends Resource
{
    protected static ?string $model = Metric::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-metrics';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(4)->schema([
                    TextInput::make('name')
                        ->label(__('cachet::metric.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(3)
                        ->autocomplete(false),
                    TextInput::make('suffix')
                        ->label(__('cachet::metric.form.suffix_label'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. ms, %, etc.'),
                    MarkdownEditor::make('description')
                        ->label(__('cachet::metric.form.description_label'))
                        ->maxLength(255)
                        ->columnSpanFull(),
                    ToggleButtons::make('default_view')
                        ->label(__('cachet::metric.form.default_view_label'))
                        ->options(MetricViewEnum::class)
                        ->inline()
                        ->required()
                        ->default(MetricViewEnum::last_hour)
                        ->columnSpanFull(),
                    TextInput::make('default_value')
                        ->label(__('cachet::metric.form.default_value_label'))
                        ->numeric()
                        ->columnSpan(2),
                    Select::make('calc_type')
                        ->label(__('cachet::metric.form.calc_type_label'))
                        ->required()
                        ->options(MetricTypeEnum::class)
                        ->default(MetricTypeEnum::sum)
                        ->columnSpan(2),
                    TextInput::make('places')
                        ->label(__('cachet::metric.form.places_label'))
                        ->required()
                        ->numeric()
                        ->default(2)
                        ->columnSpan(2),
                    TextInput::make('threshold')
                        ->label(__('cachet::metric.form.threshold_label'))
                        ->required()
                        ->numeric()
                        ->default(5)
                        ->columnSpan(2),
                ])->columnSpan(3),
                Section::make()->schema([
                    ToggleButtons::make('visible')
                        ->label(__('cachet::metric.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Toggle::make('display_chart')
                        ->label(__('cachet::metric.form.display_chart_label'))
                        ->default(true)
                        ->required(),
                    Toggle::make('show_when_empty')
                        ->label(__('cachet::metric.form.show_when_empty_label'))
                        ->default(true)
                        ->required(),

                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::metric.list.headers.name'))
                    ->searchable(),
                TextColumn::make('suffix')
                    ->label(__('cachet::metric.list.headers.suffix'))
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('default_value')
                    ->label(__('cachet::metric.list.headers.default_value'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('calc_type')
                    ->label(__('cachet::metric.list.headers.calc_type'))
                    ->badge()
                    ->sortable(),
                IconColumn::make('display_chart')
                    ->label(__('cachet::metric.list.headers.display_chart'))
                    ->boolean(),
                TextColumn::make('places')
                    ->label(__('cachet::metric.list.headers.places'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('default_view')
                    ->label(__('cachet::metric.list.headers.default_view'))
                    ->sortable(),
                TextColumn::make('threshold')
                    ->label(__('cachet::metric.list.headers.threshold'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('order')
                    ->label(__('cachet::metric.list.headers.order'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('visible')
                    ->label(__('cachet::metric.list.headers.visible'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('points_count')
                    ->label(__('cachet::metric.list.headers.points_count'))
                    ->counts('metricPoints'),
                TextColumn::make('created_at')
                    ->label(__('cachet::metric.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::metric.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->reorderable('order')
            ->defaultSort('order')
            ->emptyStateHeading(__('cachet::metric.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::metric.list.empty_state.description'));
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::metric.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::metric.resource_label', 2);
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
            'index' => ListMetrics::route('/'),
            'create' => CreateMetric::route('/create'),
            'edit' => EditMetric::route('/{record}/edit'),
        ];
    }
}
