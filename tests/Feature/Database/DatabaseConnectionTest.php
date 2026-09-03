<?php

use Illuminate\Support\Facades\DB;

it('uses the requested database connection', function () {
    $connection = getenv('DB_CONNECTION');

    expect($connection)
        ->not->toBeFalse()
        ->and(config('database.default'))->toBe($connection)
        ->and(DB::connection()->getDriverName())->toBe(config("database.connections.{$connection}.driver"));
});
