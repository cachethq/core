<?php

namespace Cachet\Commands;

use Cachet\Cachet;
use Illuminate\Console\Command;

class VersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cachet:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the version of Cachet';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Cachet '.Cachet::version().' is installed ⚡');
    }
}
