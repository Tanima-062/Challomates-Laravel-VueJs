<?php

namespace App\Http\Requests\MobileAppUser;

use App\Models\MobileAppUser;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\PhoneNumber;
class UpdateProfileRequest extends FormRequest
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
        $user = request()->user();
        return [
            'username'                  =>  ['required', 'unique:mobile_app_users,username,'.$user->id],
            // 'username'                  =>  ['nullable', 'unique:mobile_app_users,username,'.$user->id],
            // 'email'                     =>  ['nullable', 'email:rfc', 'unique:mobile_app_users,email,'.$user->id,],
            'email'                     =>  ['required', 'email:filter',],
            'first_name'                =>  ['required','regex:/^[\p{L}\p{M}\p{Zs}-]+$/u'],
            'last_name'                 =>  ['required','regex:/^[\p{L}\p{M}\p{Zs}-]+$/u'],
            'date_of_birth'             =>  ['nullable','date','date_format:Y-m-d'],
            'street'                    =>  ['nullable',],
            'house_number'              =>  ['nullable',],
            // 'zip_code'                  =>  ['nullable','regex:/^[\p{L}\p{M}\p{Zs}0-9]+$/'],
            'zip_code'                  =>  ['nullable','regex:/^[0-9]+$/'],
            'city'                      =>  ['nullable',],
            'country'                   =>  ['nullable',],
            'country_iso_code'          =>  ['nullable','required_with:phone_number', 'max:10'],
            // 'phone_number'              =>  ['nullable',"phone:country_iso_code", "unique:mobile_app_users,phone_number,".$user->id, new UniquePhoneNumber(MobileAppUser::class, $this->country_iso_code, 'full_phone_number', $user->id)],
            'phone_number'              =>  ['nullable',"phone:country_iso_code",],
            'privacy'                   =>  ['nullable', 'boolean'],
            'language_id'               =>  ['nullable','exists:language,id'],
            // 'photo'                     =>  ['nullable', 'mimes:png,jpg', 'max:2048']
            'photo'                     =>  ['nullable', 'mimes:png,jpg', ]
        ];
    }

    public function messages()
    {
        return [
            'phone_number.phone'    =>  'Invalid phone number.',
            'date_of_birth.date_format' =>  'The date of birth does not match the format yyyy-mm-dd.'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator){
            $user = request()->user();

            if (MobileAppUser::where('email', $this->email)->where('id', '!=', $user->id )->whereNotNull('email')->exists()) {
                $validator->errors()->add('email_unique', 'Email already exists');
            }
            if (MobileAppUser::where('username', $this->username)->where('id', '!=', $user->id )->whereNotNull('username')->exists()) {
                $validator->errors()->add('username_unique', 'Username already exists');
            }

            // try {
            //     $full_phone_number = PhoneNumber::make($this->phone_number, $this->country_iso_code)->formatE164();

            //     if(MobileAppUser::where('full_phone_number', '=', $full_phone_number)->where('id', '!=', $user->id )->exists()){
            //         $validator->errors()->add('phone_number_unique', 'Phone Number already exists');
            //     }
            //     else if (MobileAppUser::where('phone_number', $this->phone_number)->where('id', '!=', $user->id )->whereNotNull('phone_number')->exists()) {
            //         $validator->errors()->add('phone_number_unique', 'Phone Number already exists');
            //     }
            // } catch (\Throwable $th) {

            // }
        });
    }
}
