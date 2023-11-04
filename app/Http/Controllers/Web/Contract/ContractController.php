<?php

namespace App\Http\Controllers\Web\Contract;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\ContractCreateRequest;
use App\Http\Requests\Contract\ContractUpdateRequest;
use App\Models\Contract;
use App\Models\MarketingFee;
use App\Models\Package;
use App\Models\SalesPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ContractController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:contract.view')->only(['index', 'show']);
        $this->middleware('permission:contract.create')->only(['create', 'store']);
        $this->middleware('permission:contract.edit')->only(['edit', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $active = Contract::STATUS_ACTIVE;
        $new = Contract::STATUS_NEW;
        $expired = Contract::STATUS_EXPIRED;
        $canceled = Contract::STATUS_CANCELED;

        $contracts = Contract::with('salesPartner')
            ->select("contracts.*", DB::raw("CASE WHEN '$canceled' = status THEN '$canceled' WHEN '$now' >= contract_term_from AND '$now' <= contract_term_to THEN '$active' WHEN '$now' < contract_term_from THEN '$new' WHEN '$now' > contract_term_to THEN '$expired' ELSE contracts.status END AS current_status"))
            ->search($request->get('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->contractTermBetween($request->contract_term_start_date, $request->contract_term_end_date)
            ->salesPartnerIn($request->sales_partner)
            ->packageIn($request->package)
            ->marketingFeeIn($request->marketing_fee)
            ->statusIs($request->status)
            ->sortByColumns($request->order_by, $request->direction)
            ->paginate($request->per_page ?? 25)
            ->withQueryString();

        return Inertia::render('SalesPartner/Views/Contract/Index', ['contracts' => $contracts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $latest_id = Contract::getNextPrefixId();

        $sales_partners = SalesPartner::select('id', 'company_name', 'prefix_id')->where('status',  SalesPartner::STATUS_ACTIVE)->get();
        $packages = Package::select('id', 'package_name', 'package_prefix_id')->where('status', Package::ACTIVE)->get();
        $marketing_fees = MarketingFee::select('id', 'designation', 'prefix_id')->where('status', MarketingFee::STATUS_ACTIVE)->get();

        return Inertia::render('SalesPartner/Views/Contract/Create', ['latest_id' => $latest_id, 'marketing_fees' => $marketing_fees, 'packages' => $packages, 'sales_partners' => $sales_partners]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractCreateRequest $request)
    {
        $inputs = $request->validated();
        $status = 'new';

        $current_date = Carbon::now();
        $contract_term_from = Carbon::parse($inputs['contract_term_from']);
        $contract_term_end = Carbon::parse($inputs['contract_term_to']);

        if ($contract_term_from->greaterThan($current_date)) {
            $status = Contract::STATUS_NEW;
        }
        if ($contract_term_end->lessThan($current_date)) {
            $status = Contract::STATUS_EXPIRED;
        }
        if ($current_date->between($contract_term_from, $contract_term_end)) {
            $status = Contract::STATUS_ACTIVE;
        }

        $contract = Contract::create($inputs + ['status' => $status]);
        return redirect()->route('contract.index')->with('success', "Der Vertrag wurde erfolgreich erstellt.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function show(Contract $contract)
    {
        $contract->load(['package', 'marketingFee', 'salesPartner']);
        $contract->append('current_status');
        return Inertia::render('SalesPartner/Views/Contract/Show', ['contract' => $contract]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Contract $contract)
    {
        $sales_partners = SalesPartner::select('id', 'company_name', 'prefix_id')->where('status',  SalesPartner::STATUS_ACTIVE)->get();
        $packages = Package::select('id', 'package_name', 'package_prefix_id')->where('status', Package::ACTIVE)->get();
        $marketing_fees = MarketingFee::select('id', 'designation', 'prefix_id')->where('status', MarketingFee::STATUS_ACTIVE)->get();
        return Inertia::render("SalesPartner/Views/Contract/Edit", ["contract" => $contract, 'marketing_fees' => $marketing_fees, 'packages' => $packages, 'sales_partners' => $sales_partners]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function update(ContractUpdateRequest $request, Contract $contract)
    {
        $inputs = $request->validated();
        $status = 'new';

        $current_date = Carbon::now();
        $contract_term_from = Carbon::parse($inputs['contract_term_from']);
        $contract_term_end = Carbon::parse($inputs['contract_term_to']);

        if ($contract_term_from->greaterThan($current_date)) {
            $status = Contract::STATUS_NEW;
        }
        if ($contract_term_end->lessThan($current_date)) {
            $status = Contract::STATUS_EXPIRED;
        }
        if ($current_date->between($contract_term_from, $contract_term_end)) {
            $status = Contract::STATUS_ACTIVE;
        }

        $contract->update($inputs + ['status' => $status]);
        $contract = $contract->fresh();
        return redirect()->route('contract.index')->with('success', "Der Vertrag \"$contract->name\" wurde erfolgreich aktualisiert.");
    }

    /**
     * Cancel Contract
     *
     * @param \App\Models\Contract $contract
     * @return \Illuminate\Support\Facades\Redirect
     */

    public function cancel(Contract $contract)
    {
        if ($contract->status == Contract::STATUS_EXPIRED || $contract->status == Contract::STATUS_CANCELED)
            return;

        $contract->update(['status' => Contract::STATUS_CANCELED]);
        return redirect()->back()->with('success', "Der Vertrag \"$contract->name\" wurde erfolgreich abgebrochen.");
    }

    /**
     * Get Filterable Data
     *
     */

    public function getFilterableData(Request $request)
    {
        $now = Carbon::now()->toDateString();
        $active = Contract::STATUS_ACTIVE;
        $new = Contract::STATUS_NEW;
        $expired = Contract::STATUS_EXPIRED;
        $canceled = Contract::STATUS_CANCELED;

        $column = $request->column;
        $contracts = Contract::with('salesPartner')
            ->selectRaw("contracts.*, CASE WHEN '$canceled' = status THEN '$canceled'  WHEN '$now' BETWEEN contract_term_from AND contract_term_to THEN '$active' WHEN '$now' < contract_term_from THEN '$new' WHEN '$now' > contract_term_to THEN '$expired' ELSE contracts.status END AS current_status")
            ->search($request->get('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->contractTermBetween($request->contract_term_start_date, $request->contract_term_end_date)
            ->salesPartnerIn($request->sales_partner)
            ->packageIn($request->package)
            ->marketingFeeIn($request->marketing_fee)
            ->statusIs($request->status)
            ->sortByColumns($request->order_by, $request->direction)
            ->get();
        if ($column == 'sales_partner') {
            return $contracts->map(fn ($item) => ['id' => $item->sales_partner_id, 'name' => $item->salesPartner->company_name])->unique('id')->values();
        }

        if ($column == 'package') {
            return $contracts->map(fn ($item) => ['id' => $item->package_id, 'name' => $item->package->package_name])->unique('id')->values();
        }

        if ($column == 'marketing_fee') {
            return $contracts->map(fn ($item) => ['id' => $item->marketing_fee_id, 'name' => $item->marketingFee->designation])->unique('id')->values();
        }

        if ($column == 'status') {
            return $contracts->map(fn ($item) => ['value' => $item->current_status, 'name' => strtolower($item->current_status)])->unique('value')->values();
        }

        return [];
    }

    /**
     * Set SalesPartner with contract data to session and redirect to sales partner create|edit
     * @param \Illuminate\Http\Request $request
     * @param Boolean $to_edit
     */

    public function setContractSessionAndRedirect(Request $request, $to_edit = 0)
    {
        $request->validate([
            'to_edit' => 'in:1,0|boolean',
            'contract' => ['nullable', 'array'],
            'contract.name' => 'string|max:40|required_unless:contract,null',
            'contract.contract_term_from' => 'required_unless:contract,null|date',
            'contract.contract_term_to' => 'required_unless:contract,null|date',
            'contract.package_id' => 'required_unless:contract,null|exists:package,id,status,' . Package::ACTIVE,
            'contract.marketing_fee_id' => 'required_unless:contract,null|exists:marketing_fees,id,status,' . MarketingFee::STATUS_ACTIVE,
        ]);

        $input = $request->all();
        if ($input['contract']) {
            $input['contract']['marketing_fee'] = MarketingFee::find($request->contract['marketing_fee_id']);
            $input['contract']['package'] = Package::find($request->contract['package_id']);
            $input['contract']['prefix_id'] = Contract::getNextPrefixId();
        }
        return $to_edit ? redirect()->route('sales-partner.edit', ['sales_partner' => $input['id']])->with('data', ['sales_partner' => $input]) : redirect()->route('sales-partner.create')->with('data', ['sales_partner' => $input]);
    }
}
