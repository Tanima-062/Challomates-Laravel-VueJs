<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if($request->fcm_token){
            DB::table('fcm_tokens')
                ->where('mobile_app_user_id', $user->id)
                ->where('fcm_token', $request->fcm_token)
                ->delete()
            ;
        }

        $request->user()->currentAccessToken()->delete();

        return response(null, 200);
    }
}
