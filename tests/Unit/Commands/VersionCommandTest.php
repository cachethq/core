<?php

use Cachet\Cachet;

it('can get the version', function () {
    $this->artisan('cachet:version')
        ->expectsOutput('Cachet '.Cachet::version().' is installed ⚡')
        ->assertExitCode(0);
});
