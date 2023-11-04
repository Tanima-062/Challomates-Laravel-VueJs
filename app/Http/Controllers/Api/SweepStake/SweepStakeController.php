<?php

namespace App\Http\Controllers\Api\SweepStake;

use App\Models\Sweepstake;
use Illuminate\Http\Request;
use App\Models\Participation;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\SweepStakes\SweepStakeResource;
use App\Http\Resources\SweepStakes\MyParticipationResource;

class SweepStakeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $sweepStakes = Sweepstake::query()
            ->where(function ($q) {
                $q->ActiveNotCancel();
            })
            ->where('runtime_from', '<=', now())
            ->when(request('runtime_start_date') && request('runtime_end_date'), function ($q) {
                // $q->where('runtime_from', '>=', request('runtime_start_date'))
                //     ->where('runtime_to', '<=', request('runtime_end_date'));

                $start_date = Carbon::parse(request('runtime_start_date'))->startOfDay();
                $end_date = Carbon::parse(request('runtime_end_date'))->endOfDay();

                $q->where('runtime_from', '>=',$start_date)
                    ->where('runtime_to', '<=', $end_date)
                ;
            })
            ->when(request('sweepstakes'), function ($q) {
                $sweepstakes = explode(',', request('sweepstakes'));
                $q->whereIn("name", $sweepstakes);
            })
            ->when(request('raffle_start_date') && request('raffle_end_date'), function ($q) {
                // $q->where('raffle_time', '>=', request('raffle_start_date'))
                //     ->where('raffle_time', '<=', request('raffle_end_date'));


                $start_date = Carbon::parse(request('raffle_start_date'))->startOfDay();
                $end_date = Carbon::parse(request('raffle_end_date'))->endOfDay();

                $q->where('raffle_time', '>=',$start_date)
                    ->where('raffle_time', '<=', $end_date)
                ;
            })
            ->when(request('status'), function ($q) {
                $status = request('status');
                if ($status == 'drawn') {
                    $q->where("status", 'drawn');
                } else if ($status == 'dadeline_over') {
                    $q->where('runtime_to', '<', now())
                    ->whereNull('status')
                    ;
                } else if ($status == 'active') {
                    $q
                        ->where('runtime_from', '<=', now())
                        ->where('runtime_to', '>=', now())
                        ->whereNull('status')
                    ;
                }
            })
            ->when(request('query'), function ($q) {
                $query = request('query');
                $q->where('name', 'LIKE', "$query%");
            })
            ->when(request('participated'), function ($q) use ($user) {
                $q->whereHas('participations', fn ($query) => $query->where('participations.mobile_app_user_id', $user->id));
            })
            ->orderByDesc('id')
            ->paginate(request('per_page', 10));

        $redeemSweepstake =  getRedeemSweepstake();


        return SweepStakeResource::collection($sweepStakes)->additional([
            // 'can_redeem'        => ($sweepstake_min_coin && ($user->coin >= $sweepstake_min_coin))  ? true : false,
            'can_redeem'        => ($redeemSweepstake && ($user->coin >= $redeemSweepstake->number_of_coins_for_participation))  ? true : false,
            'status'            =>  true,
            'sweepstake_active' =>  $redeemSweepstake ? true : false
        ]);
    }

    public function getFilterData(Request $request)
    {
        $allSweepStakes = Sweepstake::select(['id', 'name', 'status', 'runtime_from', 'runtime_to',])
            ->where(function ($q) {
                $q->ActiveNotCancel();
            })
            ->where('runtime_from', '<=', now())
            ->get()
            ;
        // return $allSweepStakes;

        $sweepStakeName = $allSweepStakes->pluck('name')->unique()->values()->toArray();

        $status = $status = $this->getSweepstakeStatus($allSweepStakes);

        return response([
            'sweepsake_names'   =>  $sweepStakeName,
            'sweepsake_status'  =>  $status,
            'status'            =>  true,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Sweepstake $sweepstake)
    {
        $user = request()->user();

        return (new SweepStakeResource($sweepstake))->additional([
            'can_redeem'            => ($user->coin >= $sweepstake->number_of_coins_for_participation) ? true : false,
            'status'                =>  true,
        ]);
    }


    public function getMyPerticipations(Request $request)
    {
        $user = $request->user();

        $participations =  $user->participations()
            ->select('id', 'mobile_app_user_id', 'sweepstake_id')
            ->with('sweepstake')
            ->whereHas('sweepstake', function ($q) {
                    $q->where(function ($q) {
                        $q->ActiveNotCancel();
                    })
                    ->whereDate('runtime_from', '<=', now())

                    // Old code //Old version requested by shadman.sakib.info@gmail.com 29-09-2022
                    // ->when(request('runtime_start_date') && request('runtime_end_date'), function ($q) {
                    //     $q->whereDate('runtime_from', '>=', request('runtime_start_date'))
                    //         ->whereDate('runtime_to', '<=', request('runtime_end_date'));
                    // })
                    ->when(request('sweepstakes'), function ($q) {
                        $sweepstakes = explode(',', request('sweepstakes'));
                        $q->whereIn("name", $sweepstakes);
                    })
                    ->when(request('raffle_start_date') && request('raffle_end_date'), function ($q) {
                        // $q->wheree('raffle_time', '>=', request('raffle_start_date'))
                        //     ->where('raffle_time', '<=', request('raffle_end_date'));

                        $start_date = Carbon::parse(request('raffle_start_date'))->startOfDay();
                        $end_date = Carbon::parse(request('raffle_end_date'))->endOfDay();

                        $q->where('raffle_time', '>=',$start_date)
                            ->where('raffle_time', '<=', $end_date)
                        ;
                    })
                    ->when(request('status'), function ($q) {
                        $status = request('status');
                        if ($status == 'drawn') {
                            $q->where("status", 'drawn');
                        } else if ($status == 'dadeline_over') {
                            $q->where('runtime_to', '<', now())
                            ->whereNull('status')
                            ;
                        } else if ($status == 'active') {
                            $q
                            ->where('runtime_from', '<=', now())
                            ->where('runtime_to', '>=', now())
                            ->whereNull('status')
                            ;
                        }
                    })
                    ->when(request('query'), function ($q) {
                        $query = request('query');
                        $q->where('name', 'LIKE', "$query%");
                    });
            })
            //New version requested by shadman.sakib.info@gmail.com 29-09-2022
            ->when(request('runtime_start_date') && request('runtime_end_date'), function ($q) {
                // $q->whereDate('created_at', '>=', request('runtime_start_date'))
                //     ->whereDate('created_at', '<=', request('runtime_end_date'))
                //     ;
                // $q->where('created_at', '>=', request('runtime_start_date'))
                //     ->where('created_at', '<=', request('runtime_end_date'))
                //     ;

                $start_date = Carbon::parse(request('runtime_start_date'))->startOfDay();
                $end_date = Carbon::parse(request('runtime_end_date'))->endOfDay();

                $q->where('created_at', '>=',$start_date)
                    ->where('created_at', '<=', $end_date)
                ;

            })
            ->orderBy('id', 'DESC')
            ->get();

        // return $participations;

        $sweepstakesId = $participations->pluck('sweepstake_id')->toArray();

        $participationsId = $participations->pluck('id')->toArray();

        $sweepstake_min_coin = Sweepstake::query()
            ->whereIn('id', $sweepstakesId)
            ->min('number_of_coins_for_participation');
        $participations = Participation::query()
            ->with('sweepstake')
            ->whereIn('id', $participationsId)
            ->orderByRaw("field(id," . implode(',', $participationsId) . ")")
            ->paginate(request('per_page', 10));


        return MyParticipationResource::collection($participations)->additional([
            'can_redeem'        => ($user->coin >= $sweepstake_min_coin) ? true : false,
            'can_redeem'        => ($sweepstake_min_coin && ($user->coin >= $sweepstake_min_coin))  ? true : false,
            'status'            =>  true,
        ]);
    }


    public function getMyPerticipationsFilterData(Request $request)
    {
        $user = $request->user();

        $participations =  $user->participations()
            ->select('id', 'mobile_app_user_id', 'sweepstake_id')
            ->whereHas('sweepstake', function ($q) {
                $q->where(function ($q) {
                    $q->ActiveNotCancel();
                })
                ;
            })
            ->get();

        $sweepstakesId = $participations->pluck('sweepstake_id')->toArray();

        $allSweepStakes = Sweepstake::select(['id', 'name', 'status', 'runtime_from', 'runtime_to',])
            ->whereIn('id', $sweepstakesId)
            ->orWhereNull('status')
            ->get();

        $sweepStakeName = $allSweepStakes->pluck('name')->unique()->toArray();

        $status = $this->getSweepstakeStatus($allSweepStakes);

        return response([
            'sweepsake_names'   =>  $sweepStakeName,
            'sweepsake_status'  =>  $status,
            'status'            =>  true,
        ]);
    }



    public function getSweepstakeStatus($sweepstakes)
    {
        $status = [];
        foreach ($sweepstakes as $sweepstake) {
            if ($sweepstake->status == 'drawn' && !in_array('drawn', $status)) {
                $status[] = [
                    'name'      =>  'drawn',
                    'value'     =>  'Ausgelost'
                ];
            } else if (($sweepstake->runtime_from <=  now() && $sweepstake->runtime_to >= now()) && !in_array('dadeline_over', $status)) {
                $status[] = [
                    'name'      =>  'active',
                    'value'     =>  'Aktiv'
                ];
            } else if (!in_array('active', $status)) {
                $status[] = [
                    'name'      =>  'dadeline_over',
                    'value'     =>  'Teilnahmeschluss vorbei'
                ];
            }

            // else {
            //     $status[] = [
            //         'name'      =>  'other',
            //         'value'     =>  'Other'
            //     ];
            // }
        }

        $status =  collect($status)->unique('name')->values();

        return $status;
    }
}
