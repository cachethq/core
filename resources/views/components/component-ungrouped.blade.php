@props(['component' => null])

<div data-component="component-list" class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
    <ul data-slot="list">
        <x-cachet::component :component="$component" />
    </ul>
</div>
