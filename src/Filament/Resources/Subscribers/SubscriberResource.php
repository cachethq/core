<?php

namespace Cachet\Filament\Resources\Subscribers;

use Cachet\Filament\Resources\Subscribers\Pages\CreateSubscriber;
use Cachet\Filament\Resources\Subscribers\Pages\EditSubscriber;
use Cachet\Filament\Resources\Subscribers\Pages\ListSubscribers;
use Cachet\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static string|\BackedEnum|null $navigationIcon = 'cachet-subscribers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns(2)->schema([
                    TextInput::make('email')
                        ->label(__('cachet::subscriber.form.email_label'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    TextInput::make('verify_code')
                        ->label(__('cachet::subscriber.form.verify_code_label'))
                        ->required()
                        ->default(fn () => Str::random())
                        ->maxLength(255),
                    DateTimePicker::make('verified_at')
                        ->label(__('cachet::subscriber.form.verified_at_label')),
                    Toggle::make('global')
                        ->label(__('cachet::subscriber.form.global_label'))
                        ->required(),
                    //                Forms\Components\TextInput::make('phone_number')
                    //                    ->tel(),
                    //                Forms\Components\TextInput::make('slack_webhook_url'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('cachet::subscriber.list.headers.email'))
                    ->searchable(),
                TextColumn::make('verify_code')
                    ->label(__('cachet::subscriber.list.headers.verify_code'))
                    ->fontFamily('mono')
                    ->searchable(),
                IconColumn::make('global')
                    ->label(__('cachet::subscriber.list.headers.global'))
                    ->boolean(),
                TextColumn::make('phone_number')
                    ->label(__('cachet::subscriber.list.headers.phone_number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slack_webhook_url')
                    ->label(__('cachet::subscriber.list.headers.slack_webhook_url'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('verified_at')
                    ->label(__('cachet::subscriber.list.headers.verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('cachet::subscriber.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('cachet::subscriber.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('verify')
                    ->label(__('cachet::subscriber.list.actions.verify_label'))
                    ->color('warning')
                    ->action(fn (Subscriber $record) => $record->verify())
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('cachet::subscriber.list.empty_state.heading'))
            ->emptyStateDescription(__('cachet::subscriber.list.empty_state.description'));
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
            'index' => ListSubscribers::route('/'),
            'create' => CreateSubscriber::route('/create'),
            'edit' => EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::subscriber.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::subscriber.resource_label', 2);
    }
}
