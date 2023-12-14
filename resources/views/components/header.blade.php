<div class="flex justify-between items-center py-6 px-8 border-b dark:border-zinc-700">
    <div>
        <a href="{{ route('cachet.status-page') }}">
            <x-cachet::logo class="h-8 w-auto hidden sm:block" />
            <x-cachet::logomark class="h-8 w-auto sm:hidden" />
        </a>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('cachet.dashboard.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-white font-semibold">
            Dashboard
        </a>
        {{-- Condition Button. --}}
        <a href="{{ route('cachet.dashboard.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-white font-semibold">
            Logout
        </a>
        {{-- Conditional Button. --}}
        <a href="{{ route('cachet.dashboard.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-white font-semibold">
            Subscribers
        </a>
    </div>
</div>
