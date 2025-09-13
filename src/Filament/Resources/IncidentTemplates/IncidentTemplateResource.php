<?php

namespace Cachet\Filament\Resources\IncidentTemplates;

use Cachet\Enums\IncidentTemplateEngineEnum;
use Cachet\Filament\Resources\IncidentTemplates\Pages\CreateIncidentTemplate;
use Cachet\Filament\Resources\IncidentTemplates\Pages\EditIncidentTemplate;
use Cachet\Filament\Resources\IncidentTemplates\Pages\ListIncidentTemplates;
use Cachet\Models\IncidentTemplate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class IncidentTemplateResource extends Resource
{
    protected static ?string $model = IncidentTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-incident-templates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    TextInput::make('name')
                        ->label(__('cachet::incident_template.form.name_label'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                        ->autocomplete(false),
                    TextInput::make('slug')
                        ->label(__('cachet::incident_template.form.slug_label'))
                        ->required(),
                    Textarea::make('template')
                        ->label(__('cachet::incident_template.form.template_label'))
                        ->hint(fn (Get $get) => new HtmlString(Blade::render(match ($get('engine')) {
                            IncidentTemplateEngineEnum::twig => '<x-filament::link href="https://twig.symfony.com/doc/">'.__('cachet::incident_template.engine.twig_docs').'</x-filament::link>',
                            IncidentTemplateEngineEnum::blade => '<x-filament::link href="https://laravel.com/blade">'.__('cachet::incident_template.engine.laravel_blade_docs').'</x-filament::link>',
                            default => null,
                        })))
                        ->required()
                        ->rows(8)
                        ->columnSpanFull(),
                    Select::make('engine')
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
                TextColumn::make('name')
                    ->label(__('cachet::incident_template.list.headers.name'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('cachet::incident_template.list.headers.slug'))
                    ->searchable(),
                TextColumn::make('engine')
                    ->label(__('cachet::incident_template.list.headers.engine'))
                    ->sortable()
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('cachet::incident_template.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::incident_template.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('cachet::incident_template.list.headers.deleted_at'))
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
            'index' => ListIncidentTemplates::route('/'),
            'create' => CreateIncidentTemplate::route('/create'),
            'edit' => EditIncidentTemplate::route('/{record}/edit'),
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
