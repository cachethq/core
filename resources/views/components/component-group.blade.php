@props(['title' => null])

<div x-data x-disclosure default-open class="overflow-hidden rounded-lg border shadow dark:border-zinc-700">
    <div class="flex items-center justify-between border-b bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
        <button x-disclosure:button class="flex items-center gap-3 text-zinc-500 dark:text-zinc-300">
            <h3 class="text-lg font-semibold">
                {{ $title }}
            </h3>
            <x-cachet::icons.chevron-up ::class="!$disclosure.isOpen && 'rotate-180'" class="h-6 w-6 transition" />
        </button>
        <a href="#" class="rounded border border-zinc-800 px-2 py-1 text-xs font-semibold text-zinc-800 dark:border-zinc-600 dark:text-zinc-400">4 incidents</a>
    </div>

    <div x-disclosure:panel x-collapse class="flex flex-col divide-y bg-white dark:bg-zinc-800">
        <ul class="divide-y dark:divide-zinc-700">
            <li class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <div class="font-semibold leading-6">Envoyer</div>
                            <div x-data x-popover class="flex items-center">
                                <button x-ref="anchor" x-popover:button>
                                    <x-cachet::icons.info class="h-5 w-5 text-zinc-500 dark:text-zinc-300" />
                                </button>
                                <div x-popover:panel x-cloak x-transition.opacity x-anchor.right.offset.8="$refs.anchor" class="rounded bg-white px-2 py-1 text-xs font-medium text-zinc-800 drop-shadow dark:text-zinc-800">
                                    <span class="pointer-events-none absolute -left-1 top-1.5 h-3 w-3 rotate-45 bg-white"></span>
                                    <p class="relative">Updated Thursday 7th September 2023 13:30:03</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-1 text-sm text-zinc-500">
                            <a href="#" class="text-zinc-700 underline dark:text-zinc-300">View Details</a>
                        </div>
                    </div>
                    <x-cachet::badge type="operational" />
                </div>
            </li>
            <li class="px-4 py-3">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <div class="font-semibold leading-6">Forge</div>
                        <div class="mt-1 text-sm text-zinc-500">Deployments made easy.</div>
                    </div>
                    <x-cachet::badge type="operational" />
                </div>
            </li>
            <li class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <div class="font-semibold leading-6">Vapor</div>
                        <div class="mt-1 text-sm text-zinc-500">Deployments made easy.</div>
                    </div>
                    <x-cachet::badge type="operational" />
                </div>
            </li>
        </ul>
    </div>
</div>
