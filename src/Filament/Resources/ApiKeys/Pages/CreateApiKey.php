<?php

declare(strict_types=1);

namespace Cachet\Filament\Resources\ApiKeys\Pages;

use Cachet\Filament\Resources\ApiKeys\ApiKeyResource;
use Cachet\Models\Subscriber;
use Cachet\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class CreateApiKey extends CreateRecord
{
    protected ?string $plainTextToken = null;

    protected static string $resource = ApiKeyResource::class;

    public function handleRecordCreation(array $data): Model
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        $abilities = $data['abilities'] ?? [];

        if ($abilities === [] || in_array('*', $abilities, true)) {
            Gate::forUser($user)->authorize('issueFullAccessApiToken', $user::class);
            $abilities = ['*'];
        }

        if (collect($abilities)->contains(fn (string $ability): bool => str_starts_with($ability, 'subscribers.'))) {
            Gate::forUser($user)->authorize('viewAny', Subscriber::class);
        }

        $token = $user->createToken(
            name: $data['name'],
            abilities: $abilities,
            expiresAt: filled($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
        );

        session()->flash('api-token', $token->plainTextToken);

        return $token->accessToken;
    }
}
