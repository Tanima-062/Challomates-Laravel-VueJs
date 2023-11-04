<?php

namespace App\Http\Requests\MobileAppUser;

use App\Models\MobileAppUser;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

use Propaganistas\LaravelPhone\PhoneNumber;

class SelfRegistrationRequest extends FormRequest
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
            'username'                  =>  ['required', 'unique:mobile_app_users,username'],
            'email'                     =>  ['required', 'email:filter',],
            // 'first_name'                =>  ['required','regex:/^[a-zA-Z]+$/'],
            // 'last_name'                 =>  ['required','regex:/^[a-zA-Z]+$/'],
            'first_name'                =>  ['required','regex:/^[\p{L}\p{M}\p{Zs}\-]+$/u'],
            'last_name'                 =>  ['required','regex:/^[\p{L}\p{M}\p{Zs}\-]+$/u'],
            'date_of_birth'             =>  ['required','date','date_format:Y-m-d'],
            'street'                    =>  ['nullable',],
            'house_number'              =>  ['nullable',],
            // 'zip_code'                  =>  ['nullable','regex:/^[\p{L}\p{M}\p{Zs}0-9]+$/'],
            'zip_code'                  =>  ['nullable','regex:/^[0-9]+$/'],
            'city'                      =>  ['nullable',],
            'country'                   =>  ['nullable',],
            'country_iso_code'          =>  ['nullable','required_with:phone_number', 'max:10'],
            'phone_number'              =>  ['nullable',"phone:country_iso_code",],
            'privacy'                   =>  ['required', 'boolean'],
            'language_id'               =>  ['required','exists:language,id'],
            // 'photo'                     =>  ['nullable', 'mimes:png,jpg', 'max:2048']
            'photo'                     =>  ['nullable', 'mimes:png,jpg', ],
            'sales_partner_id'                     =>  ['nullable',]
        ];
    }

    public function messages()
    {
        return [
            'phone_number.phone'    =>  'Invalid phone number.',
            'date_of_birth.date_format' =>  'The date of birth does not match the format yyyy-mm-d.'
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function($validator){
            if (MobileAppUser::where('email', $this->email)->whereNotNull('email')->exists()) {
                $validator->errors()->add('email_unique', 'Email already exists');
            }
            if (MobileAppUser::where('username', $this->username)->whereNotNull('username')->exists()) {
                $validator->errors()->add('username_unique', 'Username already exists');
            }

            // try {
            //     $full_phone_number = PhoneNumber::make($this->phone_number, $this->country_iso_code)->formatE164();

            //     if(MobileAppUser::where('full_phone_number', '=', $full_phone_number)->exists()){
            //         $validator->errors()->add('phone_number_unique', 'Phone Number already exists');
            //     }
            //     else if (MobileAppUser::where('phone_number', $this->phone_number)->whereNotNull('phone_number')->exists()) {
            //         $validator->errors()->add('phone_number_unique', 'Phone Number already exists');
            //     }
            // } catch (\Throwable $th) {

            // }
        });
    }
}
