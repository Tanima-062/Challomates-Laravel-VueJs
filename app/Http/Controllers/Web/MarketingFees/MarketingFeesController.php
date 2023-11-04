<?php

namespace App\Http\Controllers\Web\MarketingFees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\MarketingFee;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ChalloMatesAdmin\SendInvitationNotification;
use App\Http\Requests\MarketingFees\MarketingFeeUpdateRequest;
use Illuminate\Support\Facades\DB;


class MarketingFeesController extends Controller
{
    public function index(Request $request){

        $result  =   $this->marketingFeesQuery($request);
        $marketingFees = $result['marketingFees']->paginate(request('per_page', 25))
                        ->withQueryString();

        return Inertia::render('MarketingFees/Index', [
            'marketing_fees' => $marketingFees,
            'priceRanges' => $result['priceRange'],
            'minValue' => $result['min_fee'],
            'maxValue' => $result['max_fee']
        ]);
    }

    public function create()
    {
        return Inertia::render('MarketingFees/Create', [
            'latest_id' => nextId('marketing_fees', MarketingFee::PREFIX)
        ]);
    }

    public function store(MarketingFeeUpdateRequest $request){

        $message = 'Die Marketinggebühr wurde erfolgreich erstellt.';
        $marketing_fee = MarketingFee::create($request->validated());

        return redirect()->route('marketing-fees.index')->with('success', $message);

    }

    public function edit(MarketingFee $marketing_fee)
    {
        return Inertia::render('MarketingFees/Edit', [
            'marketing_fee' => $marketing_fee
        ]);
    }

    public function update(MarketingFeeUpdateRequest $request, MarketingFee $marketing_fee)
    {
        $marketing_fee->update($request->validated());
        $message = sprintf('Die Marketinggebühr "%s" wurde erfolgreich aktualisiert.',$marketing_fee->designation);

        return redirect()->route('marketing-fees.index')->with('success', $message);
    }

    function toggleStatus(MarketingFee $marketing_fee)
    {
        $marketing_fee->status = $marketing_fee->status == MarketingFee::STATUS_ACTIVE ? MarketingFee::STATUS_INACTIVE : MarketingFee::STATUS_ACTIVE;
        $marketing_fee->save();
        $status = ($marketing_fee->status == MarketingFee::STATUS_ACTIVE) ? 'aktiviert' : 'deaktiviert';
        $message = sprintf('Die Marketinggebühr "%s" wurde erfolgreich %s.',$marketing_fee->designation,$status);
        return redirect()->back()->with('success', $message);
    }

    public function show(Request $request, MarketingFee $marketing_fee)
    {
        $contractCount = DB::table('contracts')->where('marketing_fee_id', $marketing_fee->id)->count();

        return Inertia::render('MarketingFees/Show', [
            'marketing_fee' => $marketing_fee,
            'contract_count' => $contractCount
        ]);
    }

    public function filterData(Request $request)
    {
        $columnName = $request->get('column');
        $result = $this->marketingFeesQuery($request);
        $filterData = [];

        if( $columnName == 'status' ) {
            $mIds = $result['marketingFees']->pluck('marketing_fees.status')->unique()->toArray();
            foreach($mIds as $mId){
                array_push($filterData, ['name'=> ($mId == 'active') ? 'Aktiv' : 'Inaktiv', 'value' => $mId]);
            }
        }

        return response()->json($filterData);
    }

    public function marketingFeesQuery(Request $request){

        $query = $request->get('query');
        $order_by = $request->get('order_by', null);
        $status = $request->get('status', null);
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $start_price = $request->get('start_price');
        $end_price = $request->get('end_price');
        $direction = strtolower($request->get('direction', 'ASC'));

        $query = MarketingFee::leftJoin('contracts', 'marketing_fees.id', '=', 'contracts.marketing_fee_id')
            ->select('marketing_fees.*', DB::raw( 'COUNT(contracts.marketing_fee_id) AS marketing_fee_assigned_count' ))
            ->where(function ($q) use ($query) {
                if (!is_null($query)) {
                    $q->where('marketing_fees.designation', 'like', "%${query}%");
                }
            })
            ->where(function ($q) use ($status) {
                if (!is_null($status)) {
                    $statusArr = explode(',', $status);
                    foreach ($statusArr as $term) {
                        $q->orWhere('marketing_fees.status', $term);
                    }
                }
            })
            ->where(function ($q) use ($start_date, $end_date) {
                if (!is_null($start_date) && !is_null($end_date)) {
                    $q->whereBetween(DB::raw('date_format(marketing_fees.created_at, \'%Y-%m-%d\')'), [$start_date, $end_date]);
                }
            });

        $priceQuery = clone $query;

        $min_fee = $priceQuery->min('marketing_fee');
        $max_fee = $priceQuery->max('marketing_fee');

        $priceRange = getPriceRanges($min_fee, $max_fee, 500.00);

        $marketingFees  =   clone $query->where(function ($q) use ($start_price, $end_price) {
            if (!is_null($start_price) && !is_null($end_price)) {
                $q->whereBetween('marketing_fees.marketing_fee', [$start_price, $end_price]);
            }
        })->when(!is_null(request('order_by')), function($q) use ($order_by, $direction){
            $q->orderBy('marketing_fees.'.$order_by,$direction);
        })->when(is_null(request('order_by')), function($q) {
            $q->orderBy('marketing_fees.status', 'ASC')->orderBy('marketing_fees.designation', 'ASC');
        })
        ->groupBy('marketing_fees.id');

        return ['marketingFees' => $marketingFees, 'priceRange' => $priceRange, 'min_fee' => $min_fee, 'max_fee' => $max_fee];
    }

}
