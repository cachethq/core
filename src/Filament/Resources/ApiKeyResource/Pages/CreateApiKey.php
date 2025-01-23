<?php

namespace Cachet\Filament\Resources\ApiKeyResource\Pages;

use Cachet\Filament\Resources\ApiKeyResource;
use Cachet\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateApiKey extends CreateRecord
{
    protected ?string $plainTextToken = null;

    protected static string $resource = ApiKeyResource::class;

    public function handleRecordCreation(array $data): Model
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        $token = $user->createToken(
            name: $data['name'],
            abilities: empty($data['abilities']) ? ['*'] : $data['abilities'],
            expiresAt: filled($data['expires_at']) ? Carbon::parse($data['expires_at']) : null,
        );

        session()->flash('api-token', $token->plainTextToken);

        return $token->accessToken;
    }
}
