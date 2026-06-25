<?php

namespace Cachet\Commands;

use Cachet\Jobs\CheckComponent;
use Cachet\Models\Component;
use Illuminate\Console\Command;

class CheckComponentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of all components.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Component::query()
            ->enabled()
            ->checked()
            ->whereNotNull('link')
            ->get()
            ->each(fn (Component $component) => CheckComponent::dispatch($component));

        $this->components->success('Component check dispatched.');

        return self::SUCCESS;
    }
}
