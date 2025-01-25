@php use Filament\Support\Colors\Color; @endphp

@session('success')
<div x-cloak x-data="{ show: true }">
    <div x-show="show" class="flex items-center justify-between rounded bg-accent/20 dark:bg-accent/30 px-3 py-2 text-sm text-accent">
        {{ session('success') }}

        <button @click="show = false">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
@endsession
