<?php

use Cachet\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

pest()->tia()
    ->locally()
    ->baselined()
    ->filtered();
