<?php

namespace App\Rules;

use ErrorException;
use Illuminate\Contracts\Validation\Rule;

class InArrayWhen implements Rule
{
    public $valid_data;
    public $condition;
    /**
     * Create a new rule instance.
     * @param Array $valid_data
     * @param Boolean|String $condition
     * @return void
     */
    public function __construct($valid_data, $condition)
    {
        $this->valid_data = $valid_data;
        $this->condition = $condition;
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
        info('value', [$value]);

        if (gettype($this->condition) == 'boolean' && $this->condition) {
            return in_array($value, $this->valid_data);
        }

        if (gettype($this->condition == 'string')) {
            $key_value = explode(':', $this->condition);
            if (count($key_value) < 2) {
                throw new ErrorException("Invalid formated string provided \"$this->condition\"");
            }
            $key = $key_value[0];
            $values = explode(",", $key_value[1]);

            if (!request()->has($key) || !in_array(request($key), $values)) {
                return true;
            }

            if (request()->has($key) && in_array(request($key), $values) && in_array($value, $this->valid_data)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
