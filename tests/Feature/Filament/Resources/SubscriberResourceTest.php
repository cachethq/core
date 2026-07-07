<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Filament\Resources\Subscribers\Pages\ListSubscribers;
use Cachet\Models\Subscriber;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('verifies a subscriber through the table action', function () {
    Event::fake([SubscriberVerified::class]);

    $subscriber = Subscriber::factory()->create();

    livewire(ListSubscribers::class)
        ->callAction(TestAction::make('verify')->table($subscriber))
        ->assertHasNoActionErrors();

    expect($subscriber->fresh()->hasVerifiedEmail())->toBeTrue();

    Event::assertDispatched(SubscriberVerified::class);
});

it('hides the verify action for verified subscribers', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    livewire(ListSubscribers::class)
        ->assertActionHidden(TestAction::make('verify')->table($subscriber));
});
