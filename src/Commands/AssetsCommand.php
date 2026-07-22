<?php

namespace Cachet\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish Cachet's assets";

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $filesystem): int
    {
        $filesystem->copyDirectory(
            __DIR__.'/../../public',
            public_path('vendor/cachethq/cachet'),
        );

        $this->info('Cachet assets published.');

        return self::SUCCESS;
    }
}
