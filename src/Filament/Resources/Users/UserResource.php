<?php

namespace Cachet\Filament\Resources\Users;

use Cachet\Cachet;
use Cachet\Filament\Resources\Users\Pages\CreateUser;
use Cachet\Filament\Resources\Users\Pages\EditUser;
use Cachet\Filament\Resources\Users\Pages\ListUsers;
use Cachet\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->columns()->schema([
                    TextInput::make('name')
                        ->label(__('cachet::user.form.name_label'))
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false),

                    TextInput::make('email')
                        ->label(__('cachet::user.form.email_label'))
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->unique('users', 'email'),

                    TextInput::make('password')
                        ->label(__('cachet::user.form.password_label'))
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state)),

                    TextInput::make('password_confirmation')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->same('password')
                        ->label(__('cachet::user.form.password_confirmation_label')),

                    Select::make('preferred_locale')
                        ->selectablePlaceholder(false)
                        ->options([
                            '' => __('cachet::user.form.preferred_locale_system_default'),
                            ...config('cachet.supported_locales'),
                        ])
                        ->label(__('cachet::user.form.preferred_locale')),

                    Toggle::make('is_admin')
                        ->label(__('cachet::user.form.is_admin_label'))
                        ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('cachet::user.list.headers.name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('cachet::user.list.headers.email'))
                    ->searchable(),

                ToggleColumn::make('is_admin')
                    ->disabled(fn (User $record) => auth()->user()->is($record))
                    ->label(__('cachet::user.list.headers.is_admin')),

                TextColumn::make('email_verified_at')
                    ->label(__('cachet::user.list.headers.email_verified_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('verify-email')
                    ->authorize('update')
                    ->label(__('cachet::user.list.actions.verify_email'))
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->disabled(fn (User $record): bool => $record->hasVerifiedEmail())
                    ->action(fn (Builder $query, User $record) => $record->sendEmailVerificationNotification()),
                Action::make('reset-two-factor')
                    ->authorize('update')
                    ->label(__('cachet::user.list.actions.reset_two_factor'))
                    ->icon(Heroicon::OutlinedShieldExclamation)
                    ->requiresConfirmation()
                    ->modalDescription(__('cachet::user.list.actions.reset_two_factor_confirmation'))
                    ->visible(fn (User $record): bool => filled($record->getAppAuthenticationSecret()))
                    ->action(function (Action $action, User $record): void {
                        $record->saveAppAuthenticationSecret(null);
                        $record->saveAppAuthenticationRecoveryCodes(null);

                        $action->success();
                    })
                    ->successNotificationTitle(__('cachet::user.list.actions.reset_two_factor_success')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords(),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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
