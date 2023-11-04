<?php

namespace App\Http\Controllers\Web\Coins;

use App\Http\Controllers\Controller;
use App\Models\StoreVisits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoinsCreditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $credits = StoreVisits::with(['salesPartner', 'mobileAppUser'])
            ->select('store_visits.*', DB::raw("(coalesce(posting_coins, 0) + coalesce(coin, 0)) AS total_coins"))
            ->search(request('query'))
            ->salesPartnerIn($request->sales_partners)
            ->mobileAppUserIn($request->mobile_app_users)
            ->checkedInOrOutBetween($request->start_date, $request->end_date)
            ->sortByColumns($request->order_by ?? 'check_in_time', $request->direction ?? 'DESC')
            ->whereNotNull('check_out_time')
            ->paginate($request->per_page ?? 25)
            ->withQueryString();

        return Inertia::render('Coins/Views/Credit/Index', ['credits' => $credits]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(StoreVisits $coins_credit)
    {
        $coins_credit->load(['mobileAppUser', 'salesPartner', 'contract' => ['package', 'marketingFee']]);

        $coins_credit->total_coins = $coins_credit->posting_coins + $coins_credit->coin;
        $coins_credit->salesPartner->append('current_contract');
        $coins_credit->contract?->package;
        $coins_credit->contract?->marketingFee;

        return Inertia::render('Coins/Views/Credit/Show', ['credit' => $coins_credit]);
    }

    /**
     * Get All dynamic filterable data
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Facades\Response
     */

    public function getFilterableData(Request $request)
    {
        $credits = StoreVisits::with(['salesPartner', 'mobileAppUser'])
            ->select('store_visits.*', DB::raw("(coalesce(posting_coins, 0) + coalesce(coin, 0)) AS total_coins"))
            ->search(request('query'))
            ->salesPartnerIn($request->sales_partners)
            ->mobileAppUserIn($request->mobile_app_users)
            ->checkedInOrOutBetween($request->start_date, $request->end_date)
            ->whereNotNull('check_out_time')
            ->sortByColumns($request->order_by ?: 'check_in_time', $request->direction ?: 'DESC');

        if ($request->column == 'mobile_app_users') {
            return $credits->get()->pluck('mobileAppUser')->unique('id')->values()->map(fn ($item) => ['name' => "$item->first_name $item->last_name", 'value' => $item->id]);
        }

        if ($request->column == 'sales_partners') {
            return $credits->get()->pluck('salesPartner')->unique('id')->values()->map(fn ($item) => ['name' => $item->company_name, 'value' => $item->id]);
        }
        return response([]);
    }
}
