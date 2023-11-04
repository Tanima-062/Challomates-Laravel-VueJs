<?php

namespace App\Http\Requests\SalesPartner;

use App\Models\MarketingFee;
use App\Models\Package;
use App\Models\SalesPartner;
use App\Rules\ImageOrTempPath;
use Illuminate\Foundation\Http\FormRequest;

class SalesPartnerCreateRequest extends FormRequest
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
            'company_name' => "required|string|max:40",
            'profile_picture' => ["nullable", new ImageOrTempPath(['png', 'jpg', 'jpeg'])],
            'receipt_template' => ["required", new ImageOrTempPath(['png', 'jpg', 'jpeg'])],
            'branch_id' => 'required|exists:branches,id',
            'branch_category_id' => 'required|exists:branch_categories,id',
            'website' => 'nullable|string',
            'consultant_id' => 'required|exists:users,id',
            'street' => 'required|string|max:30',
            'house_number' => 'required|string|max:30',
            'zip_code' => "required|numeric",
            'city' => 'required|string|max:30',
            'country' => 'required',
            'contact_person_first_name' => ['required', 'string', 'max:30', 'regex:/^[\p{L}\p{M}\-\s]+$/u'],
            'contact_person_last_name' => ['required', 'string', 'max:30', 'regex:/^[\p{L}\p{M}\-\s]+$/u'],
            'contact_person_phone_number' => 'required|phone:contact_person_country_iso_code',
            'contact_person_country_iso_code' => 'required|string|max:5',
            'contact_person_email' => 'required|email:filter',
            'status' => 'required|in:active,new',
            'no_information' => 'required|boolean',
            'opening_hours' => "array|min:0",
            'opening_hours.*.day' => "in:saturday,sunday,monday,tuesday,wednesday,thursday,friday",
            'opening_hours.*.first_start_time' => ['regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/i'],
            'opening_hours.*.first_end_time' => ['regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/i'],
            'opening_hours.*.last_start_time' => ['nullable', 'regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/i'],
            'opening_hours.*.last_end_time' => ['nullable', 'regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/i'],
            'map_address' => 'required|string|max:500',
            'latitude' => 'required|between:-90,90|numeric',
            'longitude' => 'required|between:-180,180|numeric',
            'contract' => ['nullable', 'array'],
            'contract.name' => 'string|max:40|required_unless:contract,null',
            'contract.contract_term_from' => 'required_unless:contract,null|date',
            'contract.contract_term_to' => 'required_unless:contract,null|date',
            'contract.package_id' => 'required_unless:contract,null|exists:package,id,status,' . Package::ACTIVE,
            'contract.marketing_fee_id' => 'required_unless:contract,null|exists:marketing_fees,id,status,' . MarketingFee::STATUS_ACTIVE,
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if(!$this->opening_hours && $this->no_information == false) {
                $validator->errors()->add('opening_hours.required', "Provide at lease 1 date range if no information is false");
                return;
            }

            if ($this->opening_hours) {
                foreach ($this->opening_hours as $key => $value) {
                    if ((isset($value['last_start_time']) && $value['last_start_time']) && (!isset($value['last_end_time']) || !$value['last_end_time'])) {
                        $validator->errors()->add("opening_hours." . $value['day'] . ".last_end_time", 'Last end time is required with last start time');
                    }
                    if ((!isset($value['last_start_time']) || !$value['last_start_time']) && ($value['last_end_time'] && isset($value['last_end_time']))) {
                        $validator->errors()->add("opening_hours." . $value['day'] . ".last_end_time", 'Last start time is required with last end time');
                    }
                }
            }
        });
    }
}
