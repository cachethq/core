<div class="space-y-4 text-sm">
    @forelse($attempts as $attempt)
        <div class="flex items-center space-x-2">
            <div @class([
                'bg-red-500' => $attempt->response_code < 200 || $attempt->response_code > 299,
                'bg-primary-500' => $attempt->response_code >= 200 && $attempt->response_code <= 299,
                'flex-shrink-0 whitespace-nowrap text-white px-2 py-1 rounded-md font-semibold' => true,
            ])>{{ $attempt->response_code }}</div>

            <div class="font-mono font-medium flex-1">{{ $attempt->event }}</div>
            <div class="text-gray-500 flex-shrink-0">{{ $attempt->created_at?->toDateTimeString() }}</div>
        </div>
    @empty
        <div class="text-gray-500">{{ __('cachet::webhook.attempts.empty_state') }}</div>
    @endforelse
</div>