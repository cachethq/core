<x-filament::page>
    <x-filament-panels::form wire:submit.prevent="importFeed">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit" color="primary">
                {{ __('cachet::integrations.oh_dear.import_feed_button') }}
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament::page>
