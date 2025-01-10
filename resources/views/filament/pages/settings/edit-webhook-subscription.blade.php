<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ view('cachet::filament.widgets.webhook-attempts', [
        'attempts' => $this->record->attempts,
    ]) }}
</x-filament-panels::page>
