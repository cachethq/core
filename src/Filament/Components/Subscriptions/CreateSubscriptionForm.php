<?php

declare(strict_types=1);

namespace Cachet\Filament\Components\Subscriptions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Livewire\Component;

/**
 * @property-read Form $form
 */
class CreateSubscriptionForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make(heading: __('cachet::subscriptions.create.heading'))
                    ->columns()
                    ->schema([
                        Forms\Components\TextInput::make('email_address')
                            ->label(__('cachet::subscriptions.create.form.email.label'))
                            ->placeholder(__('cachet::subscriptions.create.form.email.placeholder'))
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->autofocus()
                            ->autocomplete('email'),
                        Forms\Components\TextInput::make('phone_number')
                            ->label(__('cachet::subscriptions.create.form.phone.label'))
                            ->placeholder(__('cachet::subscriptions.create.form.phone.placeholder'))
                            ->tel(),
                    ]),
            ]);
    }

    public function store(): void
    {
        $state = $this->form->getState();

        dd($state);
    }

    public function render(): string
    {
        return <<<'blade'
            <form wire:submit.prevent="store" class="space-y-6 max-w-6xl">
                {{ $this->form }}
                
                <div class="flex justify-end space-x-2">
                    <x-filament::button size="lg" color="gray" tag="a" :href="route('cachet.status-page')">Cancel</x-filament::button>
                    <x-filament::button size="lg" type="submit">Subscribe</x-filament::button>
                </div>
            </form>
        blade;
    }
}
