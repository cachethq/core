<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\MetricTypeEnum;
use Cachet\Enums\MetricViewEnum;
use Cachet\Enums\ResourceVisibilityEnum;
use Cachet\Filament\Resources\MetricResource\Pages;
use Cachet\Models\Metric;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MetricResource extends Resource
{
    protected static ?string $model = Metric::class;

    protected static ?string $navigationIcon = 'cachet-metrics';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(4)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('cachet::metric.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(3)
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('suffix')
                        ->label(__('cachet::metric.form.suffix_label'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. ms, %, etc.'),
                    Forms\Components\MarkdownEditor::make('description')
                        ->label(__('cachet::metric.form.description_label'))
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\ToggleButtons::make('default_view')
                        ->label(__('cachet::metric.form.default_view_label'))
                        ->options(MetricViewEnum::class)
                        ->inline()
                        ->required()
                        ->default(MetricViewEnum::last_hour)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('default_value')
                        ->label(__('cachet::metric.form.default_value_label'))
                        ->numeric()
                        ->columnSpan(2),
                    Forms\Components\Select::make('calc_type')
                        ->label(__('cachet::metric.form.calc_type_label'))
                        ->required()
                        ->options(MetricTypeEnum::class)
                        ->default(MetricTypeEnum::sum)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('places')
                        ->label(__('cachet::metric.form.places_label'))
                        ->required()
                        ->numeric()
                        ->default(2)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('threshold')
                        ->label(__('cachet::metric.form.threshold_label'))
                        ->required()
                        ->numeric()
                        ->default(5)
                        ->columnSpan(2),
                ])->columnSpan(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\ToggleButtons::make('visible')
                        ->label(__('cachet::metric.form.visible_label'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Toggle::make('display_chart')
                        ->label(__('cachet::metric.form.display_chart_label'))
                        ->default(true)
                        ->required(),

                ])->columnSpan(1),
            ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::metric.list.headers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->label(__('cachet::metric.list.headers.suffix'))
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('default_value')
                    ->label(__('cachet::metric.list.headers.default_value'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calc_type')
                    ->label(__('cachet::metric.list.headers.calc_type'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('display_chart')
                    ->label(__('cachet::metric.list.headers.display_chart'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('places')
                    ->label(__('cachet::metric.list.headers.places'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('default_view')
                    ->label(__('cachet::metric.list.headers.default_view'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('threshold')
                    ->label(__('cachet::metric.list.headers.threshold'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('cachet::metric.list.headers.order'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('visible')
                    ->label(__('cachet::metric.list.headers.visible'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_count')
                    ->label(__('cachet::metric.list.headers.points_count'))
                    ->counts('metricPoints'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::metric.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::metric.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMetrics::route('/'),
            'create' => Pages\CreateMetric::route('/create'),
            'edit' => Pages\EditMetric::route('/{record}/edit'),
        ];
    }
}
