<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckPhoneNumberLengthRule implements Rule
{

    public $length = 12;
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
        if(strlen((string) $value ) > $this->length){
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute may not greater than '.$this->length;
    }
}
