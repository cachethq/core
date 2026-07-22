<?php

use Illuminate\Support\Facades\File;

it('publishes the assets', function () {
    $target = public_path('vendor/cachethq/cachet');
    File::deleteDirectory($target);

    $this->artisan('cachet:assets')
        ->expectsOutput('Cachet assets published.')
        ->assertExitCode(0);

    expect(File::isDirectory($target))->toBeTrue()
        ->and(File::exists($target.'/favicon.ico'))->toBeTrue();

    File::deleteDirectory($target);
});
