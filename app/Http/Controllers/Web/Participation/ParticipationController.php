<?php

namespace App\Http\Controllers\Web\Participation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participation\StoreParticipationRequest;
use App\Http\Requests\Participation\UpdateParticipationRequest;
use App\Models\Participation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ParticipationController extends Controller
{
    private array $status = array(
        'active_published' => 'Aktiv (Publiziert)',
        'finished_published' => 'Beendet (Publiziert)',
        'drawn_published' => 'Ausgelost (Drawn)',
        'drawn_not_published' => 'Ausgelost (Drawn)',
    );

    public function __construct()
    {
        $this->middleware('permission:participation.view')->only(['index', 'show', 'filterData']);
        $this->middleware('permission:participation.create')->only(['create', 'store']);
        $this->middleware('permission:participation.edit')->only(['edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        //dd($participation);
        $currentJackpotAmount = getCurrentJackpotAmount();

        return Inertia::render(
            'Participations/Index',
            array(
                'participations'    => $this->participationQuery()->paginate(request('per_page', 25))->withQueryString(),
                'current_jackpot_amount' => $currentJackpotAmount,
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Participation\StoreParticipationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreParticipationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Participation  $participation
     * @return \Inertia\Response
     */
    public function show($participationId): \Inertia\Response
    {
        $participationQuery = $this->participationQuery()
            ->where( 'id',  $participationId);
        $participation = $participationQuery->first();

        //dd($participationId);
        return Inertia::render(
            'Participations/Show',
            array(
                'participation' => $participation
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Participation  $participation
     * @return \Illuminate\Http\Response
     */
    public function edit(Participation $participation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Participation\UpdateParticipationRequest  $request
     * @param  \App\Models\Participation  $participation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateParticipationRequest $request, Participation $participation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Participation  $participation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Participation $participation)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterData(Request $request): \Illuminate\Http\JsonResponse
    {
        $columnName = $request->get( 'column' );
        $participationQuery = $this->participationQuery();
        $filterData = [];

        //dd($participationQuery->toSql());

        if ( $columnName == 'sweepstake_name' ) {
            $participationQuery->pluck('sweepstake_name', 'sweepstake_id');
            $filterData = $participationQuery->pluck('sweepstake_name', 'sweepstake_id')
                ->unique()
                ->map( function($sweepstake_name, $sweepstake_id) {
                    return [
                        'name'  =>  $sweepstake_name,
                        'value' =>  $sweepstake_id
                    ];
                } );
        }

        if( $columnName == 'participant_name' ) {
            $filterData = $participationQuery->pluck('participant', 'mobile_app_user_id' )
                ->unique()
                ->map( function($participant, $mobile_app_user_id) {
                    return [
                        'name'  =>  $participant,
                        'value' =>  $mobile_app_user_id
                    ];
                } );
        }

        if( $columnName == 'status' ) {
            $filterData = $participationQuery->pluck('status_with_published_status' )
                ->unique()
                ->filter( function ($status) {
                    //$allowed_status = array_keys( $this->status );
                    //return $status != null;
                    return array_key_exists( $status, $this->status );
                } )
                ->map( function($status) {
                    return [
                        'name'  =>  $this->status[$status],
                        'value' =>  $status
                    ];
                } );
        }

        $filterData = $filterData->toArray();

        return response()->json(array_values($filterData));
    }

    private function participationQuery()
    {
        $query = Participation::query()
            ->select(
                'participations.id AS id',
                'participations.winning_number AS winning_number',
                'participations.created_at AS created_at',
                'participations.sweepstake_id AS sweepstake_id',
                'participations.mobile_app_user_id AS mobile_app_user_id',
                'mobile_app_users.prefix_id as mobile_app_users_prefix_id',
                'mobile_app_users.first_name as mobile_app_users_first_name',
                'mobile_app_users.last_name as mobile_app_users_last_name',
                DB::raw(
                    'CONCAT(mobile_app_users.first_name, " ", mobile_app_users.last_name) as participant'
                ),
                'sweepstakes.prefix_id AS sweepstake_prefix_id',
                'sweepstakes.name AS sweepstake_name',
                'sweepstakes.type AS sweepstake_type',
                'sweepstakes.runtime_from AS sweepstakes_runtime_from',
                'sweepstakes.runtime_to AS sweepstakes_runtime_to',
                'sweepstakes.raffle_time AS sweepstakes_raffle_time',
                'sweepstakes.status AS sweepstakes_status',
                DB::raw(
                    '
                    CONCAT(
                        (
                            CASE
                                when sweepstakes.runtime_from <= NOW() && sweepstakes.runtime_to > NOW() && ( sweepstakes.`status` = "" || sweepstakes.`status` IS NULL ) then "active"
                                when sweepstakes.runtime_to <= NOW() && ( sweepstakes.`status` = "" || sweepstakes.`status` IS NULL ) then "finished"
                                when sweepstakes.`status` = "drawn" then "drawn"
                            END
                        ),
                        "_",
                        sweepstakes.publish_status
                    ) AS status_with_published_status
                    '
                )
            )
            ->join( 'sweepstakes', 'participations.sweepstake_id', '=', 'sweepstakes.id' )
            ->join( 'mobile_app_users', 'participations.mobile_app_user_id', '=', 'mobile_app_users.id' )
        ;
        //->where( 'sweepstakes.runtime_from', '<=', Carbon::now() )

        $participations = DB::table( DB::raw( "({$query->toSql()}) as s" ) )
            /*->where( function ($q) {
                $q->where( 'sweepstakes.status', 'drawn' )
                    ->orWhereNull( 'sweepstakes.status' );
            } );*/

            //dd( $participations->toSql() )

            ->when( request('query'), function($q) {
                $q->where('sweepstake_name', 'LIKE', "%".request('query')."%");
            } )
            ->when(request('status'), function($q) {
                $sweepstake_id_arr = explode( ",", request('status') );
                $q->whereIn( 'status_with_published_status', $sweepstake_id_arr );
            } )
            ->when(request('sweepstake_id'), function($q) {
                $sweepstake_id_arr = explode( ",", request('sweepstake_id') );
                $q->whereIn( 'sweepstake_id', $sweepstake_id_arr );
            } )
            ->when(request('participant_id'), function($q) {
                $participant_id_arr = explode( ",", request('participant_id') );
                $q->whereIn( 'mobile_app_user_id', $participant_id_arr );
            } )
            ->when(request('start_date') && request('end_date'), function($q) {
                $q->whereBetween(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), [request('start_date'), request('end_date')]);
            } )
            ->when(request('raffle_start_date') && request('raffle_end_date'), function($q) {
                $q->whereBetween(DB::raw('date_format(sweepstakes_raffle_time, \'%Y-%m-%d\')'), [request('raffle_start_date'), request('raffle_end_date')]);
            } )
            ->when( request('order_by'), function($q) {
                if( in_array( request('order_by'), ['created_at', 'participant', 'sweepstake_name', 'sweepstakes_raffle_time' ] ) ) {
                    $q->orderBy(request('order_by'), request('direction', 'asc'));
                } else if ( request('order_by') === 'status' ) {
                    $q->orderBy( 'status_with_published_status', request('direction', 'asc') );
                } else if ( request('order_by') === 'winning_numbers' ) {
                    $q->orderBy( 'winning_number', request('direction', 'asc') );
                }
            }, function($q) {
                $q->orderBy( 'id', 'desc' );
            } );

        return $participations;
    }
}
