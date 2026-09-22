<?php

namespace Tests\Feature\Policies;

use Cachet\Models\Subscriber;
use Illuminate\Support\Facades\Gate;
use Workbench\App\User;

it('allows only administrators to manage subscribers', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);
    $subscriber = Subscriber::factory()->create();

    foreach (['viewAny', 'create'] as $ability) {
        expect(Gate::forUser($admin)->allows($ability, Subscriber::class))->toBeTrue()
            ->and(Gate::forUser($user)->allows($ability, Subscriber::class))->toBeFalse();
    }

    foreach (['view', 'update', 'delete'] as $ability) {
        expect(Gate::forUser($admin)->allows($ability, $subscriber))->toBeTrue()
            ->and(Gate::forUser($user)->allows($ability, $subscriber))->toBeFalse();
    }
});
