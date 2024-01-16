<div class="{{
[
'operational' => 'bg-green-200',
'performance-issues' => 'bg-purple-200',
'partial-outage' => 'bg-orange-200',
'major-outage' => 'bg-red-200',
'unknown' => 'bg-blue-200',
][$type]
}} flex items-center gap-1 rounded-full px-2 py-1 text-sm font-semibold leading-tight">
    <x-dynamic-component :component="'cachet::icons.'.$type" class="h-6 w-6" />

    <div class="dark:text-zinc-700">{{ str($type)->title() }}</div>
</div>
