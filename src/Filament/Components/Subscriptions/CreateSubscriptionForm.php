<?php

declare(strict_types=1);

namespace Cachet\Filament\Components\Subscriptions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
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
                        Forms\Components\TextInput::make('email')
                            ->label(__('cachet::subscriptions.create.form.email.label'))
                            ->placeholder(__('cachet::subscriptions.create.form.email.placeholder'))
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->autofocus()
                            ->autocomplete('email'),
                    ]),
            ]);
    }

    public function store(): void
    {
        $state = $this->form->getState();

        dd($state);
    }

    public function render(): View|Closure|string
    {
        return view('cachet::pages.subscriptions.create-subscription-form');
    }
}
