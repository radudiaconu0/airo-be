<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;


class ListOfPositiveNumbersRule implements ValidationRule
{

    protected function isInteger($string) {
        return is_numeric($string) && ctype_digit($string);
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // validate that the current value is a number or a comma-separated list of positive numbers
        $numbers = explode(',', $value);

        foreach ($numbers as $number) {
            if (!$this->isInteger($number) || $number < 0) {
                $fail("The $attribute must be a number or a comma-separated list of positive numbers.");
            } else {
                if ($number < 18 || $number > 70) {
                    $fail("The $attribute must be a number or a comma-separated list of positive numbers between 18 and 70.");
                }
            }
        }
    }
}
