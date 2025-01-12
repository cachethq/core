<x-filament::page>
    @if($token = session('api-token'))
        <x-filament::section :heading="__('cachet::api_key.show_token.heading')">
            <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                {{ __('cachet::api_key.show_token.description') }}
            </p>

            <div class="flex items-center gap-3 mt-3">
                <div
                    style="--c-50:var(--success-50);--c-400:var(--success-400);--c-600:var(--success-600);"
                    class="fi-badge cursor-pointer inline-block rounded-md px-3 py-2 text-sm ring-1 ring-inset min-w-[theme(spacing.6)] fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-success"
                    x-on:click="
                        window.navigator.clipboard.writeText(@js($token))
                        $tooltip(@js(__('cachet::api_key.show_token.copy_tooltip')), {
                            theme: $store.theme,
                            timeout: 2000,
                        })
                    "
                >
                    {{ $token }}
                </div>
            </div>
        </x-filament::section>
    @endsession

    {{ $this->table }}
</x-filament::page>
