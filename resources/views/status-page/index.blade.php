@extends('cachet::layouts.status-page')
@section('content')
    <x-cachet::header />

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <h2 class="text-3xl font-semibold">About This Site</h2>
            <div class="prose prose-zinc mt-1 dark:prose-invert prose-a:text-primary-500 prose-a:underline">
                {{-- format-ignore-start --}}
                    <p>
                        This is the demo instance of <a href="https://cachethq.io/" target="_blank">Cachet</a>. The open-source status page system.
                    </p>
                {{-- format-ignore-end --}}
            </div>
        </div>

        <div class="mt-6 space-y-10">
            <x-cachet::status-bar />

            <x-cachet::component-group title="Laravel" />
            <x-cachet::component-group title="Cachet" />

            <x-cachet::maintenance />

            <x-cachet::incidents />
        </div>
    </div>

    <x-cachet::footer />
@endsection
