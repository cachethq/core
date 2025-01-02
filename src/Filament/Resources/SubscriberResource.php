<?php

namespace Cachet\Filament\Resources;

use Cachet\Filament\Resources\SubscriberResource\Pages;
use Cachet\Models\Subscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'cachet-subscribers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('email')
                        ->label(__('cachet::subscriber.form.email_label'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),
                    Forms\Components\TextInput::make('verify_code')
                        ->label(__('cachet::subscriber.form.verify_code_label'))
                        ->required()
                        ->default(fn () => Str::random())
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label(__('cachet::subscriber.form.verified_at_label')),
                    Forms\Components\Toggle::make('global')
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
                Tables\Columns\TextColumn::make('email')
                    ->label(__('cachet::subscriber.list.headers.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('verify_code')
                    ->label(__('cachet::subscriber.list.headers.verify_code'))
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\IconColumn::make('global')
                    ->label(__('cachet::subscriber.list.headers.global'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('cachet::subscriber.list.headers.phone_number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slack_webhook_url')
                    ->label(__('cachet::subscriber.list.headers.slack_webhook_url'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label(__('cachet::subscriber.list.headers.verified_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('cachet::subscriber.list.headers.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('cachet::subscriber.list.headers.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify')
                    ->label(__('cachet::subscriber.list.actions.verify_label'))
                    ->color('warning')
                    ->action(fn (Subscriber $record) => $record->verify())
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
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
