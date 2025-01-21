<form wire:submit.prevent="store" class="space-y-6 max-w-6xl">
    {{ $this->form }}

    <div class="flex justify-end space-x-2">
        <x-filament::button size="lg" color="gray" tag="a" :href="route('cachet.status-page')">Cancel</x-filament::button>
        <x-filament::button size="lg" type="submit">Subscribe</x-filament::button>
    </div>
</form>
