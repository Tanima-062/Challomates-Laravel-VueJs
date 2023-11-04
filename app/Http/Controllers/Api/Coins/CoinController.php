<?php

namespace App\Http\Controllers\Api\Coins;

use App\Models\Sweepstake;
use App\Models\StoreVisits;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Models\Participation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Coin\CoinDebitResource;
use App\Http\Resources\Coin\CoinCreditResource;
use App\Http\Resources\Store\SalesPartnerResource;
use App\Rules\ValidationArrayLengthByAnotherField;
use App\Http\Resources\SweepStakes\SweepStakeResource;

class CoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function credit(Request $request)
    {
        $user = $request->user();

        $storeVisits = StoreVisits::query()
            ->with('salesPartner:id,company_name,profile_picture')
            ->where('mobile_app_user_id', $user->id)
            ->whereNotNull('check_out_time')
            ->when(request('store_id'), function($q){
                $q->where('sales_partner_id', request('store_id'));
            })
            ->when(request('start_date') && request('end_date'), fn($q)=>$q->TimeBetween())
            ->when(request('last_week'), function ($q) {
                $start_date = now()->startOfWeek()->subWeek();
                $end_date = now()->startOfWeek()->subDay();

                $q->where('created_at', '>=', $start_date)
                    ->where('created_at', '<=', $end_date)
                ;
            })
            ->when(request('last_month'), function ($q) {
                $start_date = now()->startOfMonth()->subMonth();
                $end_date = now()->startOfMonth()->subDay();

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
            ->orderBy('created_at', 'DESC')
            ->paginate(request('per_page', 20))
        ;

        $stores =    SalesPartner::query()
        ->select(['id','company_name', 'profile_picture'])
        ->whereHas('storeVisits', function($q)use($user){
            $q->where('mobile_app_user_id', $user->id)
            ->whereNotNull('check_out_time')
            ;
        })
         ->get();

        //  $sweepstake_min_coin = Sweepstake::query()
        //     ->where(function ($q) {
        //         $q->where(function ($q) {
        //             $q->where('publish_status', 'published')
        //                 ->where('status',  '!=', 'canceled')
        //                 ;
        //         })
        //         ->orWhereNull('status');
        //     })
        //     ->min('number_of_coins_for_participation')
        //     ;

        // $sweepstake_min_coin = Sweepstake::query()
        // ->where(function ($q) {
        //     $q->ActiveNotCancel();
        //     $start_date = now()->startOfDay();
        //     $end_date = now()->endOfDay();
        //         $q->where('runtime_from', '<=',$start_date)
        //         ->where('runtime_to', '>=', $end_date)
        //     ;

        //     $q->ActiveNotCancel();
        //     $q->whereNull('status')
        //     ;
        // })
        // ->min('number_of_coins_for_participation');

        $redeemSweepstake = getRedeemSweepstake();

        return (CoinCreditResource::collection($storeVisits))->additional([
            'status'            =>  true,
            'current_balance'   =>  $user->coin,
            'can_redeem'        => ($redeemSweepstake && ($user->coin >= $redeemSweepstake->number_of_coins_for_participation))  ? true : false,
            'sweepstake_active' =>  $redeemSweepstake ? true : false,
            'stores'            =>  SalesPartnerResource::collection($stores)
        ]);
    }


    public function debit(Request $request)
    {
        $user = $request->user();

        $sweepstakes = Sweepstake::query()
            ->select('sweepstakes.*', 'participations.mobile_app_user_id', 'participations.participation_id', 'participations.created_at as participations_created_at' ,DB::raw('count(participations.id) as count_participation'))
            ->leftJoin('participations', 'sweepstakes.id', '=', 'participations.sweepstake_id')
            ->where('participations.mobile_app_user_id', $user->id)
            ->when(request('start_date') && request('end_date'), function ($query) {
                // $q->whereDate('participations.created_at', '>=', request('start_date'))
                //     ->whereDate('participations.created_at', '<=', request('end_date'))
                // ;

                $start_date = Carbon::parse(request('start_date'))->startOfDay();
                $end_date = Carbon::parse(request('end_date'))->endOfDay();

                $query->where('participations.created_at', '>=',$start_date)
                    ->where('participations.created_at', '<=', $end_date)
                ;
            })
            ->orderBy('participations.id', request('direction', 'DESC'))
            // ->groupBy('sweepstakes.id', 'participations.mobile_app_user_id')
            ->groupBy('participations.participation_id')

            ->paginate(request('per_page', 10))
        ;

        // return $sweepstakes;

        return CoinDebitResource::collection($sweepstakes)->additional([
            'status'    =>  true
        ]);
    }



    /**
     * Get reedem sweepstake
     *
     * @param Request $request
     * @return void
     */
    public function getReedemSweepstake(Request $request)
    {
        $user = request()->user();

        // return now()->toDateTimeString();
    //    $sweepstake = Sweepstake::query()
    //     ->where(function($query){
    //         $start_date = now()->startOfDay();
    //         $end_date = now()->endOfDay();
    //         $now = now();

    //         $query->where('runtime_from', '<=', $now)
    //             ->where('runtime_to', '>=', $now)
    //         ;

    //         $query->ActiveNotCancel();

    //         $query->whereNull('status')
    //         ;
    //     })
    //     ->first()
    //    ;

        $sweepstake= getRedeemSweepstake();

    //    return $sweepstake;
    if(!$sweepstake){
        return response([
            'status'    =>  false,
            'message'   =>  'No Sweepstake found',
            'balance'   =>  $user->coin
        ], 404);
    }

    // return $sweepstake;
        return (new SweepStakeResource($sweepstake))->additional([
            'status'    =>  true,
            'balance'   =>  $user->coin
        ]);
    }


    public function reedem(Request $request)
    {
        $request->validate([
            'sweepstake_id'         =>  ['required'],
            'number_of_entries'     =>  ['required', 'min:1'],
            'wining_numbers'        =>  ['required', 'array', new ValidationArrayLengthByAnotherField()],
            // 'wining_numbers.*'        =>  ['required', 'size:5','alpha_num']
            'wining_numbers.*'        =>  ['required', 'max_digits:12','integer']
        ]);

        $user = $request->user();

        $sweepstake = Sweepstake::findOrFail($request->sweepstake_id);

        $totalCoinRequired = $sweepstake->number_of_coins_for_participation * $request->number_of_entries;

        if($user->coin < $totalCoinRequired){
            return response([
                'status'    =>  false
            ]);
        }

        $participation_id =  (int) Participation::query()
            ->max('participation_id')
            ;

        $participation_id = sprintf("%08d", $participation_id + 1);

        for($i = 0; $i < $request->number_of_entries; $i++){
            $winning_number = join('-', str_split($request->wining_numbers[$i]));
            $data =  ['mobile_app_user_id'=> $user->id, 'winning_number'=>$winning_number, 'sweepstake_id' => $request->sweepstake_id , 'participation_id' => $participation_id ];

            $participation = Participation::create($data);
        }

        $user->update([
            'coin'      =>  $user->coin - $totalCoinRequired
        ]);

        return response([
            'status'    =>  true
        ]);
    }
}
