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
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('suffix')
                        ->label(__('Suffix'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. ms, %, etc.'),
                    Forms\Components\MarkdownEditor::make('description')
                        ->label(__('Description'))
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\ToggleButtons::make('default_view')
                        ->label(__('Default view'))
                        ->options(MetricViewEnum::class)
                        ->inline()
                        ->required()
                        ->default(MetricViewEnum::last_hour)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('default_value')
                        ->numeric()
                        ->columnSpan(2),
                    Forms\Components\Select::make('calc_type')
                        ->label(__('Metric Type'))
                        ->required()
                        ->options(MetricTypeEnum::class)
                        ->default(MetricTypeEnum::sum)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('places')
                        ->label(__('Places'))
                        ->required()
                        ->numeric()
                        ->default(2)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('threshold')
                        ->label(__('Threshold'))
                        ->required()
                        ->numeric()
                        ->default(5)
                        ->columnSpan(2),
                ])->columnSpan(3),
                Forms\Components\Section::make()->schema([
                    Forms\Components\ToggleButtons::make('visible')
                        ->label(__('Visible'))
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Toggle::make('display_chart')
                        ->label(__('Display chart'))
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
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->label(__('Suffix'))
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('default_value')
                    ->label(__('Default value'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calc_type')
                    ->label(__('Metric Type'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('display_chart')
                    ->label(__('Display chart'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('places')
                    ->label(__('Places'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('default_view')
                    ->label(__('Default view'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('threshold')
                    ->label(__('Threshold'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('visible')
                    ->label(__('Visible'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_count')
                    ->label(__('Points count'))
                    ->counts('metricPoints'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated at'))
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
            ->defaultSort('order');
    }

    public static function getLabel(): ?string
    {
        return __('Metric');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Metrics');
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
