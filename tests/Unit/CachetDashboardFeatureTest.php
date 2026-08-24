<?php

use Cachet\Cachet;
use Cachet\Filament\Resources\WebhookSubscriptions\WebhookSubscriptionResource;
use Illuminate\Support\Facades\Gate;
use Workbench\App\User;

it('allows dashboard features by default', function () {
    expect(Cachet::canAccessDashboardFeature('unregistered'))->toBeTrue();
});

it('lets a host authorize dashboard features', function () {
    Cachet::authorizeDashboardFeatureUsing('hosted-feature', fn (): bool => false);

    expect(Cachet::canAccessDashboardFeature('hosted-feature'))->toBeFalse();
});

it('retains Filament authorization for dashboard resources', function () {
    $this->actingAs(User::factory()->create());

    Gate::before(fn (): bool => false);

    expect(WebhookSubscriptionResource::canAccess())->toBeFalse();
});
