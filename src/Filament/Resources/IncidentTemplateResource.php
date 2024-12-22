<?php

namespace Cachet\Filament\Resources;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Filament\Resources\IncidentTemplateResource\Pages;
use Cachet\Models\IncidentTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class IncidentTemplateResource extends Resource
{
    protected static ?string $model = IncidentTemplate::class;

    protected static ?string $navigationIcon = 'cachet-incident-templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->live(debounce: 250)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('slug')
                        ->label(__('Slug'))
                        ->required(),
                    Forms\Components\Textarea::make('template')
                        ->label(__('Template'))
                        ->hint(fn (Get $get) => new HtmlString(Blade::render(match ($get('engine')) {
                            IncidentTemplateEngineEnum::twig => '<x-filament::link href="https://twig.symfony.com/doc/">Twig Documentation</x-filament::link>',
                            IncidentTemplateEngineEnum::blade => '<x-filament::link href="https://laravel.com/blade">Laravel Blade Documentation</x-filament::link>',
                            default => null,
                        })))
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('engine')
                        ->label(__('Engine'))
                        ->options(IncidentTemplateEngineEnum::class)
                        ->default(IncidentTemplateEngineEnum::twig)
                        ->live()
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('engine')
                    ->label(__('Engine'))
                    ->sortable()
                    ->badge()
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('Deleted at'))
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
            ->emptyStateHeading(__('Incident Templates'))
            ->emptyStateDescription(__('Incident templates are used to create reusable incident messages.'));
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
            'index' => Pages\ListIncidentTemplates::route('/'),
            'create' => Pages\CreateIncidentTemplate::route('/create'),
            'edit' => Pages\EditIncidentTemplate::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Incident Template');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Incident Templates');
    }
}
