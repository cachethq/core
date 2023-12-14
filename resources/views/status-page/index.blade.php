@extends('cachet::layouts.status-page')

@section('content')
    <x-cachet::header />

    <div class="max-w-5xl my-4 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-y-6 py-4 -mt-4">
            <div class="py-2">
                <h2 class="text-3xl font-semibold">About This Site</h2>
                <div class="prose-sm">
                    <p>This is the demo instance of Cachet. The open-source status page system.</p>
                </div>
            </div>

            <x-cachet::status-bar />

            <x-cachet::component-group title="Laravel" />
            <x-cachet::component-group title="Cachet" />

            <x-cachet::maintenance />

            <x-cachet::incidents />
        </div>
    </div>

    <x-cachet::footer />
@endsection
