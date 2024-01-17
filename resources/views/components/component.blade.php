@props(['component'])

<li class="px-4 py-3">
    <div class="flex items-center justify-between">
        <div class="flex flex-col">
            <div class="flex items-center gap-1.5">
                <div class="font-semibold leading-6">{{ $component->name }}</div>
                <div x-data x-popover class="flex items-center">
                    <button x-ref="anchor" x-popover:button>
                        <x-heroicon-o-question-mark-circle class="h-5 w-5 text-zinc-500 dark:text-zinc-300" />
                    </button>
                    <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="rounded bg-white px-2 py-1 text-xs font-medium text-zinc-800 drop-shadow dark:text-zinc-800">
                        <span class="pointer-events-none absolute -left-1 top-1.5 h-3 w-3 rotate-45 bg-white"></span>
                        <p class="relative">Updated Monday 1st January 2024 13:30:03</p>
                    </div>
                </div>
            </div>
            <div class="mt-1 text-sm text-zinc-500">
                <a href="#" class="text-zinc-700 underline dark:text-zinc-300">View Details</a>
            </div>
        </div>

        <x-cachet::component-badge :type="$component->status" />
    </div>
</li>
