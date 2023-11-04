<?php

namespace App\Http\Controllers\Web\StoreVisits;

use App\Http\Controllers\Controller;
use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use App\Models\StoreVisits;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreVisitsController extends Controller
{


    /**
     * Show All Store Visits
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Inertia
     */

    public function index(Request $request)
    {
        $store_visits = StoreVisits::with(['salesPartner', 'mobileAppUser'])
            ->search(request('query'))
            ->salesPartnerIn($request->sales_partners)
            ->mobileAppUserIn($request->mobile_app_users)
            ->sortByColumns($request->order_by ?: 'check_in_time', $request->direction ?: 'DESC')
            ->checkedInOrOutBetween($request->start_date, $request->end_date)
            ->paginate($request->per_page ?? 25)->withQueryString();

        return Inertia::render('StoreVisits/Index', ['store_visits' => $store_visits]);
    }

    /**
     * Show a store visit datils
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\StoreVisits $store_visit
     * @return \Inertia\Inertia
     */

    public function show(Request $request, StoreVisits $store_visit)
    {
        $store_visit->load(['mobileAppUser', 'salesPartner']);
        return Inertia::render('StoreVisits/Show', ['store_visit' => $store_visit]);
    }

    /**
     * Get All dynamic filterable data
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Facades\Response
     */

    public function getFilterableData(Request $request)
    {
        $store_visits = StoreVisits::query()
            ->search(request('query'))
            ->salesPartnerIn($request->sales_partners)
            ->mobileAppUserIn($request->mobile_app_users)
            ->sortByColumns($request->order_by ?: 'check_in_time', $request->direction ?: 'DESC')
            ->checkedInOrOutBetween($request->start_date, $request->end_date);

        if ($request->column == 'mobile_app_users') {

            $ids =  $store_visits->pluck('mobile_app_user_id')->unique()->values()->toArray();
            $mobile_app_users = MobileAppUser::whereIn('id', $ids)->orderByRaw("field(id," . implode(',', $ids) . ")")->get()->map(fn ($item) => ['name' => "$item->first_name $item->last_name", 'value' => $item->id]);
            return $mobile_app_users;
        }

        if ($request->column == 'sales_partners') {
            $ids =  $store_visits->pluck('sales_partner_id')->unique()->values()->toArray();
            $sales_partners = SalesPartner::whereIn('id', $ids)->orderByRaw("field(id," . implode(',', $ids) . ")")->get()->map(fn ($item) => ['name' => $item->company_name, 'value' => $item->id]);
            return $sales_partners;
        }
        return response([]);
    }
}
