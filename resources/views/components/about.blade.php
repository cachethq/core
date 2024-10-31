@if ($about !== '')
<div>
    <h1 class="text-3xl font-semibold">{{ {{ $title }} }}</h1>
    <div class="prose-sm md:prose prose-zinc mt-1 dark:prose-invert prose-a:text-primary-500 prose-a:underline">
        {!! $about !!}
    </div>
</div>
@endif
