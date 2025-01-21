<?php

declare(strict_types=1);

namespace Cachet\Filament\Components\Subscriptions;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Form;
use Livewire\Component;

class CreateSubscriptionForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(heading: __('cachet::subscriptions.create.page_heading'))
                    ->columns()
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('cachet::subscriptions.create.email_label'))
                            ->placeholder(__('cachet::subscriptions.create.email_placeholder'))
                            ->email()
                            ->maxLength(255)
                            ->required(),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'blade'
            <div>
                {{ $this->form }}
            </div>
        blade;
    }
}
