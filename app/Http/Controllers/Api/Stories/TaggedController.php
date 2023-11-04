<?php

namespace App\Http\Controllers\Api\Stories;

use App\Http\Controllers\Controller;
use App\Http\Resources\Store\SalesPartnerResource;
use App\Http\Resources\Story\TaggedUserResource;
use App\Models\Story;
use Illuminate\Http\Request;

class TaggedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Story $story)
    {
        $user = $request->user();
        $blocked_users_id = $user->blockedMobileAppUsers->pluck('id')->merge($user->blockedByMobileAppUsers->pluck('id'));
        $tagged =  $story->tagged()
            ->wherePivotNotIn('mobile_app_user_id', $blocked_users_id)
            // ->paginate(request('per_page', 10))
            ->FollowByMe($user->id)
            ->get()
            ;
        $story->load('salesPartner:id,company_name,profile_picture,website,street,house_number,city,zip_code,country');

        return TaggedUserResource::collection($tagged)->additional([
            'status' =>   true,
            'store' => new SalesPartnerResource($story->salesPartner)
        ]);
    }




    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\Story  $story
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Story $story)
    // {
    //     //
    // }
}
