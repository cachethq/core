@if ($about !== '' && $about !== null)
<div>
    <h2 class="text-3xl font-semibold">{{ __('About This Site') }}</h2>
    <div class="prose-sm md:prose prose-zinc mt-1 dark:prose-invert prose-a:text-primary-500 prose-a:underline">
        {!! $about !!}
    </div>
</div>
@endif
