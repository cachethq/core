<?php

namespace Cachet\Filament\Resources;

use Cachet\Cachet;
use Cachet\Filament\Resources\UserResource\Pages;
use Cachet\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return ! Cachet::demoMode() && (auth()->user()->is($record) || auth()->user()->isAdmin());
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

                    Forms\Components\Select::make('preferred_locale')
                        ->selectablePlaceholder(false)
                        ->options([
                            null => __('cachet::user.form.preferred_locale_system_default'),
                            ...config('cachet.supported_locales'),
                        ])
                        ->label(__('cachet::user.form.preferred_locale')),

                    Forms\Components\Toggle::make('is_admin')
                        ->label(__('cachet::user.form.is_admin_label'))
                        ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                ]),
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
                    ->disabled(fn (User $record) => auth()->user()->is($record))
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
                    ->disabled(fn (User $record): bool => $record->hasVerifiedEmail())
                    ->action(fn (Builder $query, User $record) => $record->sendEmailVerificationNotification()),
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
        return Cachet::userModel()::class;
    }
}
