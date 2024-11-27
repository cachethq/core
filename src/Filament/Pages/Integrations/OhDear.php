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
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class OhDear extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'cachet-oh-dear';

    protected static ?string $navigationGroup = 'Integrations';

    protected static string $view = 'cachet::filament.pages.integrations.oh-dear';

    public string $url;
    public bool $import_sites = false;
    public ?int $component_group_id = null;
    public bool $import_incidents = false;

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
                    ->label(__('OhDear Status Page URL'))
                    ->placeholder('https://status.example.com')
                    ->url()
                    ->required()
                    ->suffix('/json')
                    ->helperText(__('Enter the URL of your OhDear status page (e.g., https://status.example.com).')),

                Toggle::make('import_sites')
                    ->label(__('Import Sites as Components'))
                    ->helperText(__('Sites configured in Oh Dear will be imported as components in Cachet.'))
                    ->default(true)
                    ->reactive(),

                Select::make('component_group_id')
                    ->searchable()
                    ->visible(fn (Get $get) => $get('import_sites') === true)
                    ->relationship('group', 'name')
                    ->model(Component::class)
                    ->label(__('Component Group'))
                    ->helperText(__('The component group to assign imported components to.'))
                    ->createOptionForm(fn (Form $form) => ComponentGroupResource::form($form))
                    ->preload(),

                Toggle::make('import_incidents')
                    ->label(__('Import Incidents'))
                    ->helperText(__('Recent incidents from Oh Dear will be imported as incidents in Cachet.'))
                    ->default(false),
            ])
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
            $this->addError('url', 'The provided URL is not a valid OhDear status page endpoint.');

            return;
        }

        if (! isset($ohDear['sites'], $ohDear['summarizedStatus'])) {
            $this->addError('url', 'The provided URL is not a valid OhDear status page endpoint.');

            return;
        }

        $importOhDearFeedAction->__invoke($ohDear, $this->import_sites, $this->component_group_id, $this->import_incidents);

        Notification::make()
            ->title(__('OhDear feed imported successfully'))
            ->success()
            ->send();

        $this->reset(['url', 'import_sites', 'import_incidents']);
    }
}
