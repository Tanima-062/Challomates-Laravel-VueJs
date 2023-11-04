<?php

namespace App\Http\Controllers\Api\Map;

use App\Models\Follower;
use App\Models\StoreVisits;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Map\MapStoreResource;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Http\Resources\Map\MapMobileAppUserResource;
use App\Http\Resources\Map\MapMobileAppUserSearchResource;
use App\Http\Resources\Map\MapStoreWithUserResource;
use App\Http\Resources\Store\StoreBranchResource;
use App\Models\Branch;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'lat'       =>  ['required', 'numeric'],
            'lon'       =>  ['required', 'numeric'],
        ]);

        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        $follower_ids = DB::table('followers')
            ->where('followable_id', $user->id)
            ->where('followable_type', MobileAppUser::class)
            ->where('status', Follower::ACCEPT)
            ->whereNotIn('follower_id', $blocked_users_id)
            ->pluck('follower_id')
            ->toArray();

        // return $follower_ids;


        $followings_ids =  DB::table('followers')
            ->where('followable_type', MobileAppUser::class)
            ->where('follower_id', $user->id)
            ->where('status', Follower::ACCEPT)
            ->whereNotIn('followable_id', $blocked_users_id)
            ->pluck('followable_id')
            ->toArray();
        $ids = array_merge($follower_ids, $followings_ids);



        $userLocation = new Point($request->lat, $request->lon);
        $radious = request('radius', 5); //km
        $stores = SalesPartner::query()
            // ->whereDistance('coordinates',  $userLocation, '<=', ($radious * 1000))
            ->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radious * 1000))
            // ->withDistanceSphere('coordinates', $userLocation)
            ->active()
            ->select(['id', 'company_name',  'profile_picture', 'coordinates', 'website', 'street', 'house_number', 'zip_code', 'city', 'country'])
            ->with(['storeVisits' => function ($q) use ($ids) {
                $q->select(['id', 'sales_partner_id', 'mobile_app_user_id'])
                    ->whereNull('check_out_time');

                $q->whereHas('mobileAppUser', function ($q) use ($ids) {
                    $q->whereIn('id', $ids)
                        ->active();
                });
                $q->with(['mobileAppUser' => function ($q) use ($ids) {
                    $q->select(['id', 'first_name', 'last_name', 'username', 'privacy', 'photo', 'location'])
                        ->whereIn('id', $ids);
                }]);
            }])
            ->withCount(['storeVisits' => function ($q) use ($ids) {
                $q->whereNull('check_out_time')
                    ->whereHas('mobileAppUser', function ($q) use ($ids) {
                        $q->whereIn('id', $ids)
                            ->active();
                    });
            }])
            ->get();

        // return $stores;

        return MapStoreWithUserResource::collection($stores);



        // $stores = SalesPartner::query()
        // ->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radious * 1000))
        // ->active()
        // ->select(['id','company_name',  'profile_picture', 'coordinates'])
        // ->get();


        // $salesPartnerId = $stores->pluck('id')->toArray();

        // $checkInIds = DB::table('store_visits')
        //     ->whereNull('check_out_time')
        //     ->whereIn('sales_partner_id', $salesPartnerId)
        //     ->pluck('mobile_app_user_id')
        //     ->unique()
        //     ->flatten()
        //     ;


        // $user = $request->user();

        // $follower_ids = DB::table('followers')
        //     ->where('followable_id', $user->id)
        //     ->where('followable_type', MobileAppUser::class)
        //     ->where('status', Follower::ACCEPT)
        //     ->pluck('follower_id')
        //     ->toArray()
        //     ;



        // $followings_ids =  DB::table('followers')
        // ->where('followable_type', MobileAppUser::class)
        // ->where('follower_id', $user->id)
        // ->where('status', Follower::ACCEPT)
        // ->pluck('followable_id')
        // ->toArray()
        // ;
        // $ids = array_merge($follower_ids, $followings_ids);


        // $mobile_app_users = MobileAppUser::active()
        //     ->select(['id','first_name', 'last_name', 'username', 'privacy', 'photo', 'location'])
        //     ->whereIn('id', $ids)
        //     ->orderByRaw("field(id," . implode(',', $ids) . ")")
        //     ->get()
        // ;

        // return response([
        //     'stores'    =>  MapStoreResource::collection($stores),
        //     'mates'     =>  MapMobileAppUserResource::collection($mobile_app_users)
        // ]);

    }



    /**
     * Search mobile app user OR Stores
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $request->validate([
            'type'      =>  ['required', 'in:user,store'],
            // 'query'      =>  ['required',],
            'radius'      =>  ['nullable', 'numeric'],
            'lat'       =>  ['required', 'numeric'],
            'lon'       =>  ['required', 'numeric'],
        ]);

        if ($request->type == 'user') {
            $userLocation = new Point($request->lat, $request->lon);
            $radious = request('radius', 5); //km

            $user = $request->user();
            $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));


            $follower_ids = DB::table('followers')
                ->where('followable_id', $user->id)
                ->where('followable_type', MobileAppUser::class)
                ->where('status', Follower::ACCEPT)
                ->pluck('follower_id')
                ->toArray();

            $followings_ids =  DB::table('followers')
                ->where('followable_type', MobileAppUser::class)
                ->where('follower_id', $user->id)
                ->where('status', Follower::ACCEPT)
                ->pluck('followable_id')
                ->toArray();
            $ids = array_merge($follower_ids, $followings_ids);
            if (count($ids) < 1) {
                return response([
                    'data'  =>  [],
                    'stores'    => []
                ]);
            }


            $mobile_app_users = MobileAppUser::query()
                ->whereDistanceSphere('location',  $userLocation, '<=', ($radious * 1000))
                ->where(function ($q) {
                    $query = request('query');
                    $q->where('first_name', 'LIKE', "$query%")
                        ->orWhere('last_name', 'LIKE', "$query%")
                        ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", [$query . '%'])
                        ->orWhere('username', 'LIKE', "$query%");
                })
                ->select(['id', 'first_name', 'last_name', 'username', 'privacy', 'photo', 'location',])
                ->whereIn('id', $ids)
                // ->withCount(['storeVisits'=>function($q){
                //     $q->whereNull('check_out_time');
                // }])
                ->where('id', '!=', $user->id)
                ->whereNotIn('id', $blocked_users_id)
                ->active()
                ->CheckInNow()
                ->CheckInVisits()
                ->orderByRaw("field(id," . implode(',', $ids) . ")")
                ->get();

            // return $mobile_app_users;

            return MapMobileAppUserSearchResource::collection($mobile_app_users)->additional([
                'stores'    => []
            ]);
        } else if ($request->type == 'store') {
            $userLocation = new Point($request->lat, $request->lon);
            $radious = request('radius', 5); //km
            $query = request('query');
            $stores = SalesPartner::query()
                ->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radious * 1000))
                ->where('company_name', 'LIKE', "$query%")
                ->when(request('branch'), fn ($q) => $q->where('branch_id', request('branch')))
                ->when(request('branch_category'), fn ($q) => $q->where('branch_category_id', request('branch_category')))
                ->active()
                ->select(['id', 'company_name',  'profile_picture', 'coordinates', 'website', 'street', 'house_number', 'zip_code', 'city', 'country'])
                ->get();

            // $industryCategories = Branch::with('categories')->get();

            // $stores;

            // dd('ok');
            // return $stores;
            return response([
                'stores'    =>  MapStoreResource::collection($stores),
                'data'     =>  []
                // 'branch'    =>  StoreBranchResource::collection($industryCategories),
            ]);
        }
    }


    public function searchBranch(Request $request)
    {
        $request->validate([
            'radius'      =>  ['nullable', 'numeric'],
            'lat'       =>  ['required', 'numeric'],
            'lon'       =>  ['required', 'numeric'],
        ]);

        $userLocation = new Point($request->lat, $request->lon);
        $radius = request('radius', 5); //km

        $industryCategories = Branch::with('categories')
            ->whereHas('salesPartners', function ($q) use ($userLocation, $radius) {
                $q->whereDistanceSphere('coordinates',  $userLocation, '<=', ($radius * 1000));
            })
            ->get();

        return StoreBranchResource::collection($industryCategories);
    }
}
