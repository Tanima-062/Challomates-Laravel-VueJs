<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Propaganistas\LaravelPhone\PhoneNumber;

class UniquePhoneNumber implements Rule
{

    public $model;
    public $country_iso_code;
    public $field_name;
    public $except;
    public $except_field;

    /**
     * Create a new rule instance.
     * @param Model $model
     * @param String $country_iso_code
     * @param String $field_name
     * @param Numeric|Null $except
     * @return void
     */
    public function __construct($model, $country_iso_code, $field_name = 'full_phone_number', $except = null, $except_field = 'id')
    {
        $this->model = $model;
        $this->country_iso_code = $country_iso_code;
        $this->field_name = $field_name;
        $this->except = $except;
        $this->except_field = $except_field;
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
        try {
            $full_phone_number = PhoneNumber::make($value, $this->country_iso_code)->formatE164();
        } catch (\Throwable $th) {
            $full_phone_number = '+' . getCountryCodeByAbbreviation($this->country_iso_code) . $value;
        }

        //dd($attribute, $value, $full_phone_number);
        if ($this->except) {
            return !$this->model::where($this->except_field, '!=', $this->except)
                ->where($this->field_name, '=', $full_phone_number)
                ->where( function ($q) {
                    $q->where( 'type', 'challo_mates_admin' )
                        ->orWhere( 'type', 'company_consultant' );
                } )
                ->exists();
        } else {
            return !$this->model::where($this->field_name, '=', $full_phone_number)
                ->where( function ($q) {
                    $q->where( 'type', 'challo_mates_admin' )
                        ->orWhere( 'type', 'company_consultant' );
                } )
                ->exists();
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The phone number has already been taken.";
    }
}
