<x-filament::section heading="{{ __('cachet::webhook.attempts.heading') }}">
    <div class="space-y-4 text-sm">
        @forelse($attempts as $attempt)
            <div class="flex items-center space-x-2">
                <div @class([
                    'bg-red-100 text-red-500' => !$attempt->isSuccess(),
                    'bg-primary-100 text-primary-500' => $attempt->isSuccess(),
                    'shrink-0 whitespace-nowrap px-1 py-1 rounded-md font-semibold' => true,
                ])>
                    @if ($attempt->isSuccess())
                        <x-heroicon-m-check class="size-5" />
                    @else
                        <x-heroicon-m-x-mark class="size-5" />
                    @endif
                </div>

                <div class="font-mono font-medium flex-1">{{ $attempt->event }}</div>
                <div class="text-gray-500 shrink-0">{{ $attempt->created_at?->toDateTimeString() }}</div>
            </div>
        @empty
            <div class="text-gray-500">{{ __('cachet::webhook.attempts.empty_state') }}</div>
        @endforelse
    </div>
</x-filament::section>