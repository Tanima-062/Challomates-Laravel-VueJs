<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\MobileAppUser;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\MobileAppUser\MobileAppUserResource;
use App\Http\Resources\MobileAppUser\ProfileResource;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{



    /**
     * Display the specified resource.
     *
     *  @param  \Illuminate\Http\Request $request
     *  @param App\Model\MobileAppUser $username
     *  @return Inertia\Inertia
     */
    public function show(MobileAppUser $user)
    {
        $auth__user = request()->user();

        $follow_by_me = DB::table('followers')
            ->where('followable_id', $user->id)
            ->where('follower_id', $auth__user->id)
            ->where('follower_type', MobileAppUser::class)
            ->first();

        $follow_by_me_status = null;
        if($follow_by_me){
            $follow_by_me_status = $follow_by_me->status;
        }

        $follow_by_mate = DB::table('followers')
        ->where('followable_id', $auth__user->id)
        ->where('followable_type', MobileAppUser::class)
        ->where('follower_id',  $user->id)
        ->where('follower_type', MobileAppUser::class)
        ->first()
        // ->get()
        ;
        $follow_by_mate_status = null;
        if($follow_by_mate){
            $follow_by_mate_status = $follow_by_mate->status;
        }

        return (new ProfileResource($user))->additional([
            'status'    =>  true,
            'data'  =>  [
                'follow_by_mate_status' => $follow_by_mate_status,
                'follow_by_me_status'   => $follow_by_me_status
            ]
        ]);
    }
}
