<?php

namespace App\Http\Requests\Contract;

use App\Models\Contract;
use App\Models\MarketingFee;
use App\Models\Package;
use App\Models\SalesPartner;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ContractUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:40',
            'contract_term_from' => 'required|date',
            'contract_term_to' => 'required|date',
            'sales_partner_id' => 'required|exists:sales_partners,id,status,' . SalesPartner::STATUS_ACTIVE,
            'package_id' => 'required|exists:package,id,status,' . Package::ACTIVE,
            'marketing_fee_id' => 'required|exists:marketing_fees,id,status,' . MarketingFee::STATUS_ACTIVE,
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start_date = Carbon::parse($this->contract_term_from);
            $end_date = Carbon::parse($this->contract_term_to);
            $contract_already_exists = Contract::where('sales_partner_id', $this->sales_partner_id)->where(fn ($q) => $q->whereDate('contract_term_from', '<=', $end_date)->whereDate('contract_term_to', '>=',$start_date))->whereNot('id', $this->contract->id)->exists();
            if ($contract_already_exists) {
                $validator->errors()->add('contract_already_exists', 'A sales partner already exists with the provided contract term preiod');
            }
        });
    }
}
