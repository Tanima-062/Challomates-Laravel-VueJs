<?php

namespace App\Http\Controllers\Api;

use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\Store\SalesPartnerResource;
use App\Http\Resources\MobileAppUser\MobileAppUserSearchResource;

class SearchController extends Controller
{



    /**
     * Search mobile app users by name, username, store name
     *
     * @return void
     */
    public function serachUserAndStore()
    {

        $user = request()->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));

        $mobile_app_users = MobileAppUser::query()
            ->FollowByMe($user->id)
            ->FollowByMate($user->id)
            ->active()
            ->where(function($q){
                $user = request()->user();

                $q->where(function($q){
                    $query = request('query');
                    $q->where('first_name', 'LIKE', "$query%")
                    ->orWhere('last_name', 'LIKE', "$query%")
                    ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", [$query . '%'])
                    ->orWhere('username', 'LIKE', "$query%")
                    ;
                })
            ->where('id', '!=', $user->id);

        })
        ->whereNotIn('id', $blocked_users_id)
        ->get();

        $sales_partners =  SalesPartner::active()->where(function($q){
            $query = request('query');
             $q->where('company_name', 'LIKE', "$query%")
             ;
        })->get();

        // return $mobile_app_users;

        return response([
            'mobile_app_users'  =>  MobileAppUserSearchResource::collection($mobile_app_users),
            'stores'            =>  SalesPartnerResource::collection($sales_partners),
            'status' =>  200
        ]);

    //    return MobileAppUserSearchResource::collection($data)->additional([
    //        'status' =>  200
    //    ]);
    }
}
