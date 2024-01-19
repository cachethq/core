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
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('suffix')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. ms, %, etc.'),
                    Forms\Components\MarkdownEditor::make('description')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('default_value')
                        ->numeric(),
                    Forms\Components\Select::make('calc_type')
                        ->label(__('Metric Type'))
                        ->required()
                        ->options(MetricTypeEnum::class)
                        ->default(MetricTypeEnum::sum),
                    Forms\Components\TextInput::make('places')
                        ->required()
                        ->numeric()
                        ->default(2),
                    Forms\Components\ToggleButtons::make('default_view')
                        ->options(MetricViewEnum::class)
                        ->inline()
                        ->required()
                        ->default(MetricViewEnum::last_hour),
                    Forms\Components\TextInput::make('threshold')
                        ->required()
                        ->numeric()
                        ->default(5),
                    Forms\Components\ToggleButtons::make('visible')
                        ->inline()
                        ->options(ResourceVisibilityEnum::class)
                        ->default(ResourceVisibilityEnum::guest)
                        ->required(),
                    Forms\Components\Toggle::make('display_chart')
                        ->default(true)
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suffix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('calc_type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('display_chart')
                    ->boolean(),
                Tables\Columns\TextColumn::make('places')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('default_view')
                    ->sortable(),
                Tables\Columns\TextColumn::make('threshold')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('visible')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_count')->counts('metricPoints'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListMetrics::route('/'),
            'create' => Pages\CreateMetric::route('/create'),
            'edit' => Pages\EditMetric::route('/{record}/edit'),
        ];
    }
}
