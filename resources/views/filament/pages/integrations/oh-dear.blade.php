<x-filament::page>
    <form class="grid gap-6" wire:submit.prevent="importFeed">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit" color="primary">
                {{ __('cachet::integrations.oh_dear.import_feed_button') }}
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
