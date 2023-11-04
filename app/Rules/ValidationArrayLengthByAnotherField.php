<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidationArrayLengthByAnotherField implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return  count($value) == request()->number_of_entries;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Winning numbers array must be contain '.request('number_of_entries').' wining numbers';
    }
}
