<?php

namespace Cachet\Rules;

use Closure;
use DivisionByZeroError;
use Illuminate\Contracts\Validation\InvokableRule;

class FactorOfSixty implements InvokableRule
{
    /**
     * Run the validation rule.
     */
    public function __invoke($attribute, mixed $value, Closure $fail): void
    {
        try {
            if (60 % (int) $value !== 0) {
                $fail('The :attribute must be a factor of 60.');
            }
        } catch (DivisionByZeroError $e) {
            $fail('The :attribute must be greater than 0.');
        }
    }
}
