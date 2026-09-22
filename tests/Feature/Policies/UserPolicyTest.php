<?php

namespace Tests\Feature\Policies;

use Cachet\Models\User as CachetUser;
use Illuminate\Support\Facades\Gate;
use Workbench\App\User;

it('allows only administrators to manage users', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);
    $record = User::factory()->create();

    expect(Gate::forUser($admin)->allows('viewAny', CachetUser::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $record))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('create', CachetUser::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $record))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('delete', $record))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', CachetUser::class))->toBeFalse()
        ->and(Gate::forUser($user)->allows('create', CachetUser::class))->toBeFalse()
        ->and(Gate::forUser($user)->allows('update', $record))->toBeFalse()
        ->and(Gate::forUser($user)->allows('delete', $record))->toBeFalse();
});

it('allows non-administrators to view only themselves', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create(['is_admin' => false]);

    expect(Gate::forUser($user)->allows('view', $user))->toBeTrue()
        ->and(Gate::forUser($user)->allows('view', $otherUser))->toBeFalse();
});

it('prevents administrators from deleting themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    expect(Gate::forUser($admin)->allows('delete', $admin))->toBeFalse();
});

it('prevents user updates in demo mode', function () {
    config()->set('cachet.demo_mode', true);

    $admin = User::factory()->create(['is_admin' => true]);
    $record = User::factory()->create();

    expect(Gate::forUser($admin)->allows('update', $record))->toBeFalse();
});

it('allows only administrators to issue full access tokens', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);

    expect(Gate::forUser($admin)->allows('issueFullAccessApiToken', CachetUser::class))->toBeTrue()
        ->and(Gate::forUser($user)->allows('issueFullAccessApiToken', CachetUser::class))->toBeFalse();
});
