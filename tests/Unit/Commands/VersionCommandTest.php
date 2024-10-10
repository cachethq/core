<?php

it('can get the version', function () {
    $this->artisan('cachet:version')
        ->expectsOutput('Cachet '.\Cachet\Cachet::version().' is installed ⚡')
        ->assertExitCode(0);
});
