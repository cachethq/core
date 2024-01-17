<x-cachet::cachet :title="$title">
    <div class="flex items-center justify-center px-8 py-6">
        <div>
            <a href="{{ route('cachet.status-page') }}" class="transition hover:opacity-80">
                <x-cachet::logo class="hidden h-8 w-auto sm:block"/>
                <x-cachet::logomark class="h-8 w-auto sm:hidden"/>
            </a>
        </div>
    </div>

    Setup Content

    <x-cachet::footer/>
</x-cachet::cachet>
