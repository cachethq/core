<?php

use Illuminate\Support\Facades\File;

it('publishes the assets', function () {
    $originalPublicPath = public_path();
    $testingPublicPath = storage_path('framework/testing/cachet-assets-'.getmypid());

    $this->app->usePublicPath($testingPublicPath);

    $target = public_path('vendor/cachethq/cachet');

    try {
        $this->artisan('cachet:assets')
            ->expectsOutput('Cachet assets published.')
            ->assertExitCode(0);

        expect(File::isDirectory($target))->toBeTrue()
            ->and(File::exists($target.'/favicon.ico'))->toBeTrue();
    } finally {
        $this->app->usePublicPath($originalPublicPath);

        File::deleteDirectory($testingPublicPath);
    }
});
