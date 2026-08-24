@if ($componentGroups->isNotEmpty() || $ungroupedComponents->isNotEmpty())
    <div class="status-components group relative">
        <ul class="divide-y divide-zinc-900/10 dark:divide-white/15">
            @foreach ($componentGroups as $componentGroup)
                <x-cachet::component-group :component-group="$componentGroup" />
            @endforeach

            @foreach ($ungroupedComponents as $component)
                <x-cachet::component :component="$component" />
            @endforeach
        </ul>
    </div>
@endif
