<?php

namespace App\Http\Requests\MarketingFees;

use App\Models\MarketingFee;;
use Illuminate\Foundation\Http\FormRequest;

class MarketingFeeUpdateRequest extends FormRequest
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
            'designation' => "required|string|",
            'marketing_fee' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'direct_consumers_senior_partner_share' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'direct_consumers_share_jackpot' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'direct_consumers_share_fee_challomates' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'direct_consumers_share_challomates_marketing_ag' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'distribution_consumers_share_of_consultants' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'distribution_consumers_proportion_of_sales_partners' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'distribution_consumers_share_jackpot' => "required|regex:/^\d+(\.\d{1,2})?$/",
            'distribution_consumers_share_challomates_marketing_ag' => "required|regex:/^\d+(\.\d{1,2})?$/",
        ];
    }

    public function messages()
    {
        return [];
    }

    public function withValidator($validator)
    {

    }
}
