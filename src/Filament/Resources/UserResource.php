<?php

namespace Cachet\Filament\Resources;

use Cachet\Filament\Resources\UserResource\Pages;
use Cachet\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canAccess(): bool
    {
        return auth()->user()->is_admin;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('cachet::user.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),

                    Forms\Components\TextInput::make('email')
                        ->label(__('cachet::user.form.email_label'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->unique('users', 'email', ignoreRecord: true),

                    Forms\Components\TextInput::make('password')
                        ->label(__('cachet::user.form.password_label'))
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state)),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->same('password')
                        ->label(__('cachet::user.form.password_confirmation_label')),

                    Forms\Components\Toggle::make('is_admin')
                        ->label(__('cachet::user.form.is_admin_label')),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('cachet::user.list.headers.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('cachet::user.list.headers.email'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('cachet::user.list.headers.email_verified_at'))
                    ->dateTime(),

                Tables\Columns\ToggleColumn::make('is_admin')
                    ->disabled(fn () => !auth()->user()->is_admin)
                    ->label(__('cachet::user.list.headers.is_admin')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('verify-email')
                    ->label(__('cachet::user.list.actions.verify_email'))
                    ->icon('heroicon-o-check-badge')
                    ->disabled(fn (Authenticatable $record): bool => $record->hasVerifiedEmail())
                    ->action(fn (Builder $query, Authenticatable $record) => $record->sendEmailVerificationNotification()),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): ?string
    {
        return trans_choice('cachet::user.resource_label', 1);
    }

    public static function getPluralLabel(): ?string
    {
        return trans_choice('cachet::user.resource_label', 2);
    }

    public static function getModel(): string
    {
        return config('cachet.user_model');
    }
}
