<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'package_name' => ['required', 'string', 'max:40'],
            'services' => ['nullable', 'string', 'max:100'],
            'registration_fee' => ['required', 'numeric', 'max:999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'first_year_fee' => ['required', 'numeric', 'max:999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'yearly_fee' => ['required', 'numeric', 'max:999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'coin_factor' => ['required', 'numeric', 'max:999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            //'coin_factor' => ['required', 'integer', 'max:2147483647'],
            'consulting' => ['required', 'string', 'max:40'],
            'booster' => ['required', 'integer', 'max:2147483647'],
            'number_of_registration' => ['required', 'integer', 'max:2147483647'],
        ];
    }

    /**
     * @return array|void
     */
    public function messages() {
        return [
            'registration_fee.regex' => trans( 'Value must be up-to 2 point decimal.' ),
            'first_year_fee.regex' => trans( 'Value must be up-to 2 point decimal.' ),
            'yearly_fee.regex' => trans( 'Value must be up-to 2 point decimal.' ),
            'coin_factor.regex' => trans( 'Value must be up-to 2 point decimal.' ),
            //'coin_factor.integer' => trans( 'Only non decimal numbers are allowed' ),
            'booster.integer' => trans( 'Only non decimal numbers are allowed' ),
            'number_of_registration.integer' => trans( 'Only non decimal numbers are allowed' ),
        ];
    }
}
