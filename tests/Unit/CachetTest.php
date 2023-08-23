<?php

use Cachet\Cachet;

it('can get the current version of Cachet', function () {
    expect(Cachet::version())->toBeString()->toBe('3.x-dev');
});
