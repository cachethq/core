<?php

namespace Tests\Feature\Filament\Resources;

use Cachet\Events\Subscribers\SubscriberVerified;
use Cachet\Filament\Resources\Subscribers\Pages\CreateSubscriber;
use Cachet\Filament\Resources\Subscribers\Pages\ListSubscribers;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification as NotificationFacade;
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

it('sends a verification email when creating an unverified subscriber', function () {
    NotificationFacade::fake();

    livewire(CreateSubscriber::class)
        ->fillForm(['email' => 'james@example.com'])
        ->call('create')
        ->assertHasNoFormErrors();

    $subscriber = Subscriber::query()->where('email', 'james@example.com')->sole();

    NotificationFacade::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('does not send a verification email when creating a pre-verified subscriber', function () {
    NotificationFacade::fake();

    livewire(CreateSubscriber::class)
        ->fillForm(['email' => 'james@example.com', 'email_verified_at' => now()])
        ->call('create')
        ->assertHasNoFormErrors();

    NotificationFacade::assertNothingSent();
});

it('resends the verification email from the table action', function () {
    NotificationFacade::fake();

    $subscriber = Subscriber::factory()->create();

    livewire(ListSubscribers::class)
        ->callAction(TestAction::make('resend-verification')->table($subscriber))
        ->assertHasNoActionErrors();

    NotificationFacade::assertSentTo($subscriber, VerifySubscriberEmail::class);
});

it('hides the resend verification action for verified subscribers', function () {
    $subscriber = Subscriber::factory()->verified()->create();

    livewire(ListSubscribers::class)
        ->assertActionHidden(TestAction::make('resend-verification')->table($subscriber));
});
