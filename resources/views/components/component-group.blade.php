@props([
    'title' => null,
])

<div class="rounded-md shadow border dark:border-zinc-700 overflow-hidden">
    <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800 border-b dark:border-zinc-700">
        <h3 class="text-base font-semibold tracking-tight">
            {{ $title }}
        </h3>

        <button class="text-zinc-500">
            <PlusCircleIcon class="h-6 w-6" />
        </button>
    </div>

    <div class="flex flex-col divide-y bg-white dark:bg-zinc-800">
        <ul class="divide-y dark:divide-zinc-700">
            <li class="px-4 py-2">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <div class="font-semibold">Envoyer</div>
                        <div class="text-xs">Deployments made easy.</div>
                    </div>
                    <x-cachet::badge />
                </div>
            </li>
            <li class="px-4 py-2">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <div class="font-semibold">Forge</div>
                        <div class="text-xs">Deployments made easy.</div>
                    </div>
                    <div>Operational</div>
                </div>
            </li>
            <li class="px-4 py-2">
                <div class="flex justify-between items-center">
                    <div class="flex flex-col">
                        <div class="font-semibold">Vapor</div>
                        <div class="text-xs">Deployments made easy.</div>
                    </div>
                    <div>Operational</div>
                </div>
            </li>
        </ul>
    </div>
</div>
