<div
    data-captcha="altcha"
    x-data="cachetAltcha(@js($challengeUrl))"
    x-bind:data-state="state"
    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-zinc-500 ring-1 ring-inset ring-zinc-300 dark:text-zinc-400 dark:ring-zinc-700"
>
    <input type="hidden" name="altcha" x-bind:value="payload" />
    <x-heroicon-o-shield-check class="size-5 shrink-0" aria-hidden="true" />
    <span x-show="state === 'verifying'" role="status" x-cloak>{{ __('cachet::subscriber.status_page.captcha.verifying') }}</span>
    <span x-show="state === 'verified'" role="status" x-cloak>{{ __('cachet::subscriber.status_page.captcha.verified') }}</span>
    <span x-show="state === 'error'" role="alert" x-cloak>{{ __('cachet::subscriber.status_page.captcha.error') }}</span>
    <noscript>{{ __('cachet::subscriber.status_page.captcha.noscript') }}</noscript>
</div>
