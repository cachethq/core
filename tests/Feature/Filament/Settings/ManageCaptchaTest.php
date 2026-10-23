<?php

namespace Tests\Feature\Filament\Settings;

use Cachet\Enums\CaptchaProviderEnum;
use Cachet\Filament\Pages\Settings\ManageCaptcha;
use Cachet\Settings\CaptchaSettings;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Workbench\App\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('cachet'));

    actingAs(User::factory()->create(['is_admin' => true]));
});

it('renders the manage captcha page', function () {
    $this->get(ManageCaptcha::getUrl())->assertOk();
});

it('defaults to no captcha', function () {
    expect(app(CaptchaSettings::class))
        ->provider->toBe(CaptchaProviderEnum::none)
        ->altcha_complexity->toBe(50000);

    livewire(ManageCaptcha::class)
        ->assertFormSet(['provider' => CaptchaProviderEnum::none])
        ->assertFormFieldHidden('site_key')
        ->assertFormFieldHidden('secret_key')
        ->assertFormFieldHidden('altcha_complexity');
});

it('saves a hosted provider and encrypts the secret key', function () {
    livewire(ManageCaptcha::class)
        ->fillForm(['provider' => CaptchaProviderEnum::turnstile])
        ->assertFormFieldVisible('site_key')
        ->assertFormFieldVisible('secret_key')
        ->assertFormFieldHidden('altcha_complexity')
        ->fillForm(['site_key' => 'site-key', 'secret_key' => 'super-secret'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CaptchaSettings::class)->refresh())
        ->provider->toBe(CaptchaProviderEnum::turnstile)
        ->site_key->toBe('site-key')
        ->secret_key->toBe('super-secret');

    $stored = DB::table('settings')->where('group', 'captcha')->where('name', 'secret_key')->value('payload');

    expect($stored)->not->toContain('super-secret');
});

it('requires both keys for a hosted provider', function () {
    livewire(ManageCaptcha::class)
        ->fillForm(['provider' => CaptchaProviderEnum::hcaptcha, 'site_key' => null, 'secret_key' => null])
        ->call('save')
        ->assertHasFormErrors(['site_key' => 'required', 'secret_key' => 'required']);
});

it('saves altcha without keys', function () {
    livewire(ManageCaptcha::class)
        ->fillForm(['provider' => CaptchaProviderEnum::altcha])
        ->assertFormFieldHidden('site_key')
        ->assertFormFieldVisible('altcha_complexity')
        ->fillForm(['altcha_complexity' => 100000])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CaptchaSettings::class)->refresh())
        ->provider->toBe(CaptchaProviderEnum::altcha)
        ->altcha_complexity->toBe(100000);
});
