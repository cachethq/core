<x-filament-widgets::widget>
    <x-filament::section :heading="__('cachet::cachet.support.section_heading')">
        <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
            {!! $considerSupporting !!}
        </p>
        <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
            {!! $keepUpToDate !!}
        </p>
        <p class="text-sm leading-6 text-gray-500 dark:text-gray-400 font-semibold">
            {{ __('cachet::cachet.support.work_in_progress_text') }}
        </p>
    </x-filament::section>
</x-filament-widgets::widget>

