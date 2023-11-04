<?php

namespace App\Http\Controllers\Api\StoreVisit;

use App\Models\StoreVisits;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Store\SalesPartnerResource;
use App\Http\Resources\StoreVisits\StoreVisitResource;
use Carbon\Carbon;

class StoreVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $storeVisits = StoreVisits::query()
            ->with('salesPartner:id,company_name,profile_picture')
            ->where('mobile_app_user_id', $user->id)
            ->whereNotNull('check_out_time')
            ->when(request('store_id'), function($q){
                // $q->where('sales_partner_id', request('store_id'));
                $store_id_arr = explode( ',', request('store_id') );
                $q->whereIn( 'sales_partner_id', $store_id_arr );
            })
            ->when(request('start_date') && request('end_date'), fn($q)=>$q->TimeBetween())
            ->when(request('last_week'), function ($q) {
                // $start_date = now()->subWeek();
                $start_date = now()->startOfWeek()->subWeek();
                $end_date = now()->startOfWeek()->subDay();
                // $q->whereDate('created_at', '>=', $start_date)
                //     ->whereDate('created_at', '<=', $end_date)
                // ;
                $q->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                ;
            })
            ->when(request('last_month'), function ($q) {
                // $start_date = now()->subMonth();
                $start_date = now()->startOfMonth()->subMonth();
                $end_date = now()->startOfMonth()->subDay();

                // $q->whereDate('created_at', '>=', $start_date)
                //     ->whereDate('created_at', '<=', $end_date)
                // ;
                $q->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                ;
            })
            ->when(request('query'), function($q){
                $q->whereHas('salesPartner', function($q){
                    $query = request('query');
                    $q->where('company_name', 'LIKE', "$query%");
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(request('per_page', 20))
        ;

        // return $storeVisits;

        $stores =    SalesPartner::query()
        ->select(['id','company_name', 'profile_picture'])
        ->whereHas('storeVisits', function($q)use($user){
            $q->where('mobile_app_user_id', $user->id)
            ->whereNotNull('check_out_time')
            ;
        })
         ->get();

        return (StoreVisitResource::collection($storeVisits))->additional([
            'stores'            =>  SalesPartnerResource::collection($stores),
            'status'            =>  true,
        ]);
    }
}
