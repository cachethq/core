<?php

namespace Cachet\Filament\Pages\Integrations;

use Cachet\Actions\Integrations\ImportOhDearFeed;
use Cachet\Filament\Resources\ComponentGroupResource;
use Cachet\Models\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * @property Form $form
 */
class OhDear extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'cachet-oh-dear';

    protected static string $view = 'cachet::filament.pages.integrations.oh-dear';

    public string $url;

    public bool $import_sites = false;

    public ?int $component_group_id = null;

    public bool $import_incidents = false;

    public static function getNavigationGroup(): ?string
    {
        return __('cachet::navigation.integrations.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('cachet::navigation.integrations.items.oh_dear');
    }

    /**
     * Mount the page.
     */
    public function mount(): void
    {
        $this->form->fill([
            'url' => '',
            'import_sites' => true,
            'component_group_id' => null,
            'import_incidents' => true,
        ]);
    }

    /**
     * Get the form schema definition.
     */
    protected function getFormSchema(): array
    {
        return [
            Section::make()->schema([
                TextInput::make('url')
                    ->label(__('cachet::integrations.oh_dear.status_page_url_label'))
                    ->placeholder('https://status.example.com')
                    ->url()
                    ->required()
                    ->suffix('/json')
                    ->helperText(__('cachet::integrations.oh_dear.status_page_url_helper')),

                Toggle::make('import_sites')
                    ->label(__('cachet::integrations.oh_dear.import_sites_as_components_label'))
                    ->helperText(__('cachet::integrations.oh_dear.import_sites_as_components_helper'))
                    ->default(true)
                    ->reactive(),

                Select::make('component_group_id')
                    ->searchable()
                    ->visible(fn (Get $get) => $get('import_sites') === true)
                    ->relationship('group', 'name')
                    ->model(Component::class)
                    ->label(__('cachet::integrations.oh_dear.component_group_label'))
                    ->helperText(__('cachet::integrations.oh_dear.component_group_helper'))
                    ->createOptionForm(fn (Form $form) => ComponentGroupResource::form($form))
                    ->preload(),

                Toggle::make('import_incidents')
                    ->label(__('cachet::integrations.oh_dear.import_incidents_label'))
                    ->helperText(__('cachet::integrations.oh_dear.import_incidents_helper'))
                    ->default(false),
            ]),
        ];
    }

    /**
     * Import the OhDear feed.
     */
    public function importFeed(ImportOhDearFeed $importOhDearFeedAction): void
    {
        $this->validate();

        try {
            $ohDear = Http::baseUrl(rtrim($this->url))
                ->get('/json')
                ->throw()
                ->json();
        } catch (ConnectionException $e) {
            $this->addError('url', $e->getMessage());

            return;
        } catch (RequestException $e) {
            $this->addError('url', __('cachet::integrations.oh_dear.provided_url_invalid'));

            return;
        }

        if (! isset($ohDear['sites'], $ohDear['summarizedStatus'])) {
            $this->addError('url', __('cachet::integrations.oh_dear.provided_url_invalid'));

            return;
        }

        $importOhDearFeedAction->__invoke($ohDear, $this->import_sites, $this->component_group_id, $this->import_incidents);

        Notification::make()
            ->title(__('cachet::integrations.oh_dear.imported_successfully'))
            ->success()
            ->send();

        $this->reset(['url', 'import_sites', 'import_incidents']);
    }
}
