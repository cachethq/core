<div class="py-5">
    <form wire:submit.prevent="store">
        {{ $this->form }}

        <div class="mt-5 flex items-center justify-end gap-5">
            <button x-show="subscribe = false" class="px-3 text-sm font-semibold text-gray-600 hover:text-gray-700 dark:text-gray-200 dark:hover:text-gray-400" type="submit">
            </button>

            <button type="submit" class="rounded bg-accent px-3 py-2 text-sm font-semibold text-accent-foreground">
                {{ __('cachet::subscriber.public_form.submit_label') }}
            </button>
        </div>
    </form>
</div>
