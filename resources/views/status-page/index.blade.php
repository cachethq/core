@extends('cachet::layouts.status-page')

@section('content')
    <x-cachet::header />

    <div class="max-w-5xl mt-4 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-y-4 py-4 -mt-4">
            <x-cachet::status-bar />

            <x-cachet::component-group />
        </div>
    </div>

    <x-cachet::footer />
@endsection
