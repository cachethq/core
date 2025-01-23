@props(['heading' => null, 'breadcrumbs' => []])

<x-cachet::cachet>
    <x-cachet::header />

    <section class="container mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 flex flex-col gap-y-8">
        @if($heading)
            <x-cachet::page-header
                    :breadcrumbs="$breadcrumbs"
                    heading="{{ __('cachet::subscriber.public_form.heading') }}"
            >
                <x-slot name="heading">
                    {{ $heading }}
                </x-slot>
            </x-cachet::page-header>
        @endif

        <main class="flex flex-col space-y-6">
            {{ $slot }}
        </main>
    </section>

    <x-cachet::footer />
</x-cachet::cachet>
