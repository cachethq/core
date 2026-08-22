@if ($metrics->isNotEmpty())
    <div class="flex flex-col gap-8">
        @foreach ($metrics as $metric)
            <x-cachet::metric :metric="$metric" />
        @endforeach
    </div>
@endif
