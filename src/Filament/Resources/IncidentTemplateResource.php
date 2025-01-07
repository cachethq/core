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
                        ->label(__('cachet::incident_template.form.name_label'))
                        ->required()
                        ->live(debounce: 250)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('slug')
                        ->label(__('cachet::incident_template.form.slug_label'))
                        ->required(),
                    Forms\Components\Textarea::make('template')
                        ->label(__('cachet::incident_template.form.template_label'))
                        ->hint(fn (Get $get) => new HtmlString(Blade::render(match ($get('engine')) {
                            IncidentTemplateEngineEnum::twig => '<x-filament::link href="https://twig.symfony.com/doc/">'.__('cachet::incident_template.engine.twig_docs').'</x-filament::link>',
                            IncidentTemplateEngineEnum::blade => '<x-filament::link href="https://laravel.com/blade">'.__('cachet::incident_template.engine.laravel_blade_docs').'</x-filament::link>',
                            default => null,
                        })))
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('engine')
                        ->label(__('cachet::incident_template.form.engine_label'))
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
                    ->label(__('cachet::incident_template.list.headers.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('cachet::incident_template.list.headers.slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('engine')
                    ->label(__('cachet::incident_template.list.headers.engine'))
                    ->sortable()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::incident_template.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::incident_template.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('cachet::incident_template.list.headers.deleted_at'))
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
            ->emptyStateHeading(__('cachet::incident_template.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::incident_template.list.empty_state.description'));
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
        return trans_choice('cachet::incident_template.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::incident_template.resource_label', 2);
    }
}
