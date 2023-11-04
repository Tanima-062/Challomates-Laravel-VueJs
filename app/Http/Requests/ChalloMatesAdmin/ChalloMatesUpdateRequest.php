<?php

namespace App\Http\Requests\ChalloMatesAdmin;

use App\Models\User;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ChalloMatesUpdateRequest extends FormRequest
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
            'first_name'             => ["required", "string", "max:30", 'regex:/^[\p{L}\p{M}\-\s]+$/u'],
            'last_name'              => ["required", "string", "max:30", 'regex:/^[\p{L}\p{M}\-\s]+$/u'],
            'email'                  => "required|email:filter|unique:users,email," . $this->challo_mates_admin->id,
            'phone_number'           => ["required", "phone:country_iso_code"],
            'country_iso_code'       => 'required|string|size:2',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'Invalid Email Format',
            "phone" => "Invalid Phone Number",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user_types = [
                'challo_mates_admin' => "CHalloMates Administrator",
                "company_consultant" => "Company Consultant",
                'sales_partners' => "Sales Parter",
                'mobile_app_user' => "Mobile App User",
            ];

            if (User::where('email', $this->email)->exists() && User::where('email', $this->email)->first()->id != $this->challo_mates_admin->id) {
                $user_type = $user_types[User::where('email', $this->email)->first()->type] ?? "User";
                $message = "Für die eingegebene E-Mail Adresse besteht bereits ein $user_type. Dieselbe E-Mail Adresse kann nur für einen Benutzer verwendet werden.";
                $validator->errors()->add('email.unique', $message);
            }
        });
    }
}
