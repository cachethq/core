<?php

namespace Cachet\Console\Commands;

use Cachet\Jobs\SendBeaconJob;
use Illuminate\Console\Command;

class SendBeaconCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cachet:beacon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Communicate with the Cachet Beacon server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Do not send the beacon if it's not enabled.
        if (! config('cachet.beacon')) {
            return;
        }

        dispatch(new SendBeaconJob);
    }
}
