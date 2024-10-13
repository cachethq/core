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
                        ->label(__('Email'))
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('verify_code')
                        ->label(__('Verify code'))
                        ->required()
                        ->default(fn () => Str::random())
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label(__('Verified at')),
                    Forms\Components\Toggle::make('global')
                        ->label(__('Global'))
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
                    ->label(__('Email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('verify_code')
                    ->label(__('Verify code'))
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\IconColumn::make('global')
                    ->label(__('Global'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label(__('Phone number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slack_webhook_url')
                    ->label(__('Slack Webhook URL'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label(__('Verified at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
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
                Tables\Actions\Action::make('verify')
                    ->label(__('Verify'))
                    ->color('warning')
                    ->action(fn (Subscriber $record) => $record->verify())
                    ->requiresConfirmation(),
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
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return __('Subscriber');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Subscribers');
    }
}
