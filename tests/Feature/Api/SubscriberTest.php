<?php

use Cachet\Models\Component;
use Cachet\Models\Subscriber;
use Cachet\Notifications\VerifySubscriberEmail;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Workbench\App\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('cannot list subscribers when not authenticated', function () {
    Subscriber::factory(2)->create();

    $response = getJson('/status/api/subscribers');

    $response->assertUnauthorized();
});

it('cannot list subscribers without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    Subscriber::factory(2)->create();

    $response = getJson('/status/api/subscribers');

    $response->assertForbidden();
});

it('can list subscribers', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(2)->create();

    $response = getJson('/status/api/subscribers');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

it('does not list more than 15 subscribers by default', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(20)->create();

    $response = getJson('/status/api/subscribers');

    $response->assertOk();
    $response->assertJsonCount(15, 'data');
});

it('can list more than 15 subscribers', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(20)->create();

    $response = getJson('/status/api/subscribers?per_page=18');

    $response->assertOk();
    $response->assertJsonCount(18, 'data');
});

it('can filter subscribers by email', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(5)->create();
    Subscriber::factory()->create(['email' => 'james@alt-three.com']);

    $response = getJson('/status/api/subscribers?filter[email]=james@alt-three.com');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment(['email' => 'james@alt-three.com']);
});

it('cannot get a subscriber when not authenticated', function () {
    $subscriber = Subscriber::factory()->create();

    $response = getJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertUnauthorized();
});

it('cannot get a subscriber without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $subscriber = Subscriber::factory()->create();

    $response = getJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertForbidden();
});

it('can get a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(5)->create();
    $subscriber = Subscriber::factory()->create();

    $response = getJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $subscriber->id,
    ]);
});

it('can get a subscriber with components', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->hasComponents(2)->create();

    $response = getJson('/status/api/subscribers/'.$subscriber->id.'?include=components');

    $response->assertOk();
    $response->assertJsonFragment(['id' => $subscriber->id]);
});

it('can filter subscribers by meta', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    Subscriber::factory(5)->create();
    $subscriber = Subscriber::factory()->create();
    $subscriber->syncMeta(['region' => 'eu-west']);

    $query = http_build_query(['filter' => ['meta' => ['region' => 'eu-west']]]);

    $response = getJson('/status/api/subscribers?'.$query);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.attributes.id', $subscriber->id);
});

it('can include meta on a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->create();
    $subscriber->syncMeta(['region' => 'eu-west', 'priority' => 3, 'critical' => true]);

    $response = getJson('/status/api/subscribers/'.$subscriber->id.'?include=meta');

    $response->assertOk();
    $response->assertJsonPath('data.attributes.meta', [
        'region' => 'eu-west',
        'priority' => 3,
        'critical' => true,
    ]);
});

it('does not include meta on a subscriber by default', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->create();
    $subscriber->syncMeta(['region' => 'eu-west']);

    $response = getJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertOk();
    $response->assertJsonMissingPath('data.attributes.meta');
});

it('can create a subscriber with meta', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
        'meta' => ['region' => 'eu-west', 'priority' => 3, 'critical' => true],
    ]);

    $response->assertCreated();
    expect(Subscriber::query()->firstWhere('email', 'james@alt-three.com')->metaValues())
        ->toBe(['region' => 'eu-west', 'priority' => 3, 'critical' => true]);
});

it('cannot create a subscriber when not authenticated', function () {
    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertUnauthorized();
});

it('cannot create a subscriber without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertForbidden();
});

it('can create a subscriber', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertCreated();
    $response->assertJsonFragment([
        'email' => 'james@alt-three.com',
    ]);
    $this->assertDatabaseHas('subscribers', [
        'email' => 'james@alt-three.com',
        'email_verified_at' => null,
    ]);

    Notification::assertSentTo(
        Subscriber::query()->firstWhere('email', 'james@alt-three.com'),
        VerifySubscriberEmail::class,
    );
});

it('can create a verified subscriber without sending a verification email', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
        'verified' => true,
    ]);

    $response->assertCreated();

    expect(Subscriber::query()->firstWhere('email', 'james@alt-three.com'))
        ->hasVerifiedEmail()->toBeTrue();

    Notification::assertNothingSent();
});

it('can create a subscriber with component subscriptions', function () {
    Notification::fake();

    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $components = Component::factory(2)->create();

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
        'global' => false,
        'components' => $components->pluck('id')->values()->all(),
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('subscribers', [
        'email' => 'james@alt-three.com',
        'global' => false,
    ]);
    $this->assertDatabaseHas('subscriptions', [
        'subscriber_id' => $response->json('data.id'),
        'component_id' => $components->first()->id,
    ]);
});

it('cannot create a subscriber with an invalid email address', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $response = postJson('/status/api/subscribers', [
        'email' => 'not-an-email',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('email');
});

it('cannot create a subscriber with components that do not exist', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $response = postJson('/status/api/subscribers', [
        'email' => 'james@alt-three.com',
        'components' => [999],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('components.0');
});

it('cannot update a subscriber when not authenticated', function () {
    $subscriber = Subscriber::factory()->create();

    $response = putJson('/status/api/subscribers/'.$subscriber->id, [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertUnauthorized();
});

it('cannot update a subscriber without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $subscriber = Subscriber::factory()->create();

    $response = putJson('/status/api/subscribers/'.$subscriber->id, [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertForbidden();
});

it('can update a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->verified()->create();

    $response = putJson('/status/api/subscribers/'.$subscriber->id, [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertOk();
    $response->assertJsonFragment([
        'email' => 'james@alt-three.com',
    ]);
    $this->assertDatabaseHas('subscribers', [
        'id' => $subscriber->id,
        'email' => 'james@alt-three.com',
        'email_verified_at' => null,
    ]);
});

it('can update a subscriber component subscriptions', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->hasComponents(1)->create();
    $components = Component::factory(2)->create();

    $response = putJson('/status/api/subscribers/'.$subscriber->id, [
        'components' => $components->pluck('id')->values()->all(),
    ]);

    $response->assertOk();

    expect($subscriber->fresh()->components)->toHaveCount(2);
});

it('does not detach component subscriptions when components are omitted from an update', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.manage']);

    $subscriber = Subscriber::factory()->hasComponents(2)->create();

    $response = putJson('/status/api/subscribers/'.$subscriber->id, [
        'email' => 'james@alt-three.com',
    ]);

    $response->assertOk();

    expect($subscriber->fresh()->components)->toHaveCount(2);
});

it('cannot delete a subscriber when not authenticated', function () {
    $subscriber = Subscriber::factory()->create();

    $response = deleteJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertUnauthorized();
});

it('cannot delete a subscriber without the token ability', function () {
    Sanctum::actingAs(User::factory()->create());

    $subscriber = Subscriber::factory()->create();

    $response = deleteJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertForbidden();
});

it('can delete a subscriber', function () {
    Sanctum::actingAs(User::factory()->create(), ['subscribers.delete']);

    $subscriber = Subscriber::factory()->hasComponents(2)->create();

    $response = deleteJson('/status/api/subscribers/'.$subscriber->id);

    $response->assertNoContent();
    $this->assertDatabaseMissing('subscribers', [
        'id' => $subscriber->id,
    ]);
    $this->assertDatabaseMissing('subscriptions', [
        'subscriber_id' => $subscriber->id,
    ]);
});
