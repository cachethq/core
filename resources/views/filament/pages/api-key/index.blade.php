<x-filament::page>
    @if($token = session('api-token'))
        <x-filament::section :heading="__('cachet::api_key.show_token.heading')">
            <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                {{ __('cachet::api_key.show_token.description') }}
            </p>

            <div class="flex items-center gap-3 mt-3">
                <x-filament::badge
                    color="success"
                    tag="button"
                    :x-on:click="'
                        window.navigator.clipboard.writeText(' . \Illuminate\Support\Js::from($token) . ')
                        $tooltip(' . \Illuminate\Support\Js::from(__('cachet::api_key.show_token.copy_tooltip')) . ', {
                            theme: $store.theme,
                            timeout: 2000,
                        })
                    '"
                >
                    {{ $token }}
                </x-filament::badge>
            </div>
        </x-filament::section>
    @endsession

    {{ $this->table }}
</x-filament::page>
