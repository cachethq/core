@props(['component' => null])

<div class="group relative overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-zinc-900/10 dark:bg-zinc-900 dark:ring-white/15">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/40 to-transparent" aria-hidden="true"></div>

    <ul>
        <x-cachet::component :component="$component" />
    </ul>
</div>
