<x-cachet::cachet page="status">
    <x-cachet::header />

    @php($appSettings = app(\Cachet\Settings\AppSettings::class))
    @php($siteName = $appSettings->name ?: config('cachet.title', 'Cachet'))

    <main data-slot="main" class="status-page container mx-auto flex max-w-5xl flex-col gap-8 px-4 py-10 sm:px-6 lg:px-8">
        <section data-component="status-overview" class="status-overview relative">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>
            @if ($appSettings->show_site_name)
                <div data-slot="masthead" class="status-overview__masthead">
                    <h1 data-slot="title" class="status-overview__title font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $siteName }}</h1>
                </div>
            @endif

            <x-cachet::status-bar :is-heading="! $appSettings->show_site_name" />

            <x-cachet::component-groups />
        </section>

        <x-cachet::about :show-site-name="false" />

        @if ($display_graphs)
        <x-cachet::metrics />
        @endif

        @if ($schedules->isNotEmpty())
            <x-cachet::schedules :schedules="$schedules" />
        @endif

        <x-cachet::incident-timeline />
    </main>

    <x-cachet::footer />
</x-cachet::cachet>
