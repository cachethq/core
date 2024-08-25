<?php

use Cachet\Rules\FactorOfSixty;
use Illuminate\Support\Facades\Validator;

it('validates thresholds correctly', function ($threshold) {
    $errors = Validator::make([
        'threshold' => $threshold,
    ], [
        'threshold' => new FactorOfSixty,
    ])->errors()->all();

    expect($errors)->toBeEmpty();
})->with([
    1, 2, 5, 10, 15, 30, 60,
]);

it('validates invalid thresholds correctly', function ($threshold) {
    $errors = Validator::make([
        'threshold' => $threshold,
    ], [
        'threshold' => new FactorOfSixty,
    ])->errors()->all();

    expect($errors)->toEqual(['The threshold must be a factor of 60.']);
})->with([
    9, 11, 13, 17, 19, 23, 29, 31, 59,
]);
