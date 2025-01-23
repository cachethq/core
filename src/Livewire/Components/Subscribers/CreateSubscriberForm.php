<?php

namespace Cachet\Livewire\Components\Subscribers;

use Cachet\Actions\Subscriber\CreateSubscriber;
use Cachet\Models\Component;
use Cachet\Models\ComponentGroup;
use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component as LivewireComponent;


/**
 * @property-read Form $form
 */
class CreateSubscriberForm extends LivewireComponent implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    private const SECTION_CLASS = 'rounded bg-white dark:bg-white/5 border ring-0 dark:border-zinc-700';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $componentsNotGrouped = Component::query()->whereDoesntHave('group')->get();

        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->label(__('cachet::subscriber.public_form.email_label'))
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->autofocus()
                    ->autocomplete('email'),

                Forms\Components\View::make('cachet::pages.subscribers.partials.components-heading')
                    ->viewData([
                        'title' => __('cachet::subscriber.public_form.components_label'),
                        'description' => __('cachet::subscriber.public_form.components_hint'),
                    ]),

                Forms\Components\Group::make([
                    // Component Groups
                    ...collect(ComponentGroup::query()->with('components')->get())
                        ->map(fn (ComponentGroup $componentGroup) => Forms\Components\Section::make()
                            ->heading($componentGroup->name)
                            ->extraAttributes(['class' => self::SECTION_CLASS])
                            ->schema([
                                Forms\Components\CheckboxList::make("componentGroup-{$componentGroup->id}-components")
                                    ->hiddenLabel()
                                    ->options(fn () => $componentGroup->components->mapWithKeys(fn (Component $component) => [
                                        $component->id => $component->name
                                    ]))
                                    ->descriptions(fn () => $componentGroup->components->mapWithKeys(fn (Component $component) => [
                                        $component->id => $component->description ?? ''
                                    ]))
                                    ->columns(),
                            ]),
                        ),

                    // Components without a group
                    Forms\Components\Section::make()
                        ->extraAttributes(['class' => self::SECTION_CLASS])
                        ->schema([
                            Forms\Components\CheckboxList::make('components')
                                ->hiddenLabel()
                                ->options($componentsNotGrouped->mapWithKeys(fn (Component $component) => [
                                    $component->id => $component->name,
                                ]))
                                ->descriptions($componentsNotGrouped->mapWithKeys(fn (Component $component) => [
                                    $component->id => $component->description,
                                ])),
                        ]),
                ]),
            ]);
    }

    public function store(CreateSubscriber $createSubscriber)
    {
        $state = $this->form->getState();

        $createSubscriber->handle(
            email: $state['email'],
            components: collect($state)->except('email')->collapse()->toArray(),
        );

        session()->flash('success', __('cachet::subscriber.public_form.success_message'));

        return redirect()->route('cachet.status-page');
    }

    public function render(): View|Closure|string
    {
        return view('cachet::pages.subscribers.partials.create-subscriber-form');
    }
}
