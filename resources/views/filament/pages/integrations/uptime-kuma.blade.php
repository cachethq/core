<!-- 
    * This view is used for the Uptime Kuma integration page in the Filament admin panel.
    * It provides a form for configuring the integration and displays linked components.
 -->
<x-filament::page>
    <form class="grid gap-6" wire:submit.prevent="syncMonitors">
        {{ $this->form }}

        <div class="flex gap-3 flex-wrap">
            <x-filament::button 
                type="button" 
                wire:click="testConnection" 
                color="gray"
            >
                Test Connection
            </x-filament::button>

            <x-filament::button type="submit">
                Sync Monitors
            </x-filament::button>

            <x-filament::button 
                type="button"
                wire:click="syncStatusOnly"
                color="gray"
            >
                Sync Status Only
            </x-filament::button>
        </div>
    </form>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Linked Components
        </x-slot>
        <x-slot name="description">
            Components that are linked to Uptime Kuma monitors.
        </x-slot>

        @php
            $linkedComponents = \Cachet\Models\Component::whereNotNull('meta->uptime_kuma_monitor_id')->get();
        @endphp

        @if($linkedComponents->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">
                No components are linked to Uptime Kuma monitors yet. Use the "Sync Monitors" button above to import monitors, or manually link components by editing them.
            </p>
        @else
            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Component</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Monitor ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        @foreach($linkedComponents as $component)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('filament.cachet.resources.components.edit', $component) }}" class="text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                        {{ $component->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $component->meta['uptime_kuma_monitor_id'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <x-cachet::badge :status="$component->status" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    <x-filament::section class="mt-6" :collapsed="true">
        <x-slot name="heading">
            Setup Instructions
        </x-slot>
        <x-slot name="description">
            How to configure Uptime Kuma webhooks for automatic incident creation.
        </x-slot>

        <div class="prose dark:prose-invert max-w-none text-sm">
            <ol class="space-y-3">
                <li>
                    <strong>Add a Webhook notification in Uptime Kuma:</strong>
                    <ol class="mt-2 ml-4 space-y-1">
                        <li>Go to your monitor in Uptime Kuma</li>
                        <li>Click on "Setup Notifications"</li>
                        <li>Add a new notification of type "Webhook"</li>
                    </ol>
                </li>
                <li>
                    <strong>Configure the Webhook:</strong>
                    <ul class="mt-2 ml-4 space-y-1">
                        <li>URL: <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ url('/api/integrations/uptime-kuma/webhook') }}</code></li>
                        <li>Content Type: <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">application/json</code></li>
                        @if(config('cachet.uptime_kuma.webhook_secret'))
                            <li>Add Header: <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">X-Webhook-Secret: {{ config('cachet.uptime_kuma.webhook_secret') }}</code></li>
                        @endif
                    </ul>
                </li>
                <li>
                    <strong>Sync Monitors:</strong>
                    <p class="mt-1">Use the "Sync Monitors" button above to import monitors from your Uptime Kuma status page. This will create components for each monitor.</p>
                </li>
                <li>
                    <strong>Automatic Incidents:</strong>
                    <p class="mt-1">When a monitor goes DOWN, an incident will automatically be created. When it goes UP, the incident will be resolved.</p>
                </li>
            </ol>
        </div>
    </x-filament::section>
</x-filament::page>
