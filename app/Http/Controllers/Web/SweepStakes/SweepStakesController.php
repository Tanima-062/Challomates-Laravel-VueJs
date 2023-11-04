<?php

namespace App\Http\Controllers\Web\SweepStakes;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Sweepstake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SweepStakes\SweepStakesCreateRequest;
use App\Http\Requests\SweepStakes\SweepStakesUpdateRequest;

class SweepStakesController extends Controller
{
    private array $type = array(
        'sweepstake' => 'Gewinnspiel',
        'jackpot' => 'Jackpot'
    );

    private array $status = array(
        'new_published' => 'Neu (Publiziert)',
        'new_not_published' => 'Neu (NICHT Publiziert)',
        'active_published' => 'Aktiv (Publiziert)',
        'active_not_published' => 'Aktiv (NICHT Publiziert)',
        'finished_published' => 'Beendet (Publiziert)',
        'finished_not_published' => 'Beendet (NICHT Publiziert)',
        'canceled_published' => 'Abgebrochen (Publiziert)',
        'canceled_not_published' => 'Abgebrochen (NICHT Publiziert)',
        'drawn_published' => 'Ausgelost (Drawn)',
    );

    public function __construct()
    {
        $this->middleware('permission:sweepstakes.view')->only(['index', 'show', 'filterData']);
        $this->middleware('permission:sweepstakes.create')->only(['create', 'store']);
        $this->middleware('permission:sweepstakes.edit')->only(['edit', 'update', 'publish', 'cancel']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $sweepstakes = $this->sweepstakesQuery();
        //dd($sweepstakes->toSql());
        $sweepstakes = $sweepstakes->paginate(request('per_page', 25))->withQueryString();
        $currentJackpotAmount = getCurrentJackpotAmount();

        return Inertia::render(
            'Sweepstakes/Index',
            [
                'sweepstakes'   =>  $sweepstakes,
                'current_jackpot_amount' => $currentJackpotAmount,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Sweepstakes/Create', [
            'latest_id' => nextId('sweepstakes', Sweepstake::PREFIX)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SweepStakesCreateRequest $sweepStakesCreateRequest
     * @return RedirectResponse
     */
    public function store(SweepStakesCreateRequest $sweepStakesCreateRequest): RedirectResponse
    {
        $validated = $sweepStakesCreateRequest->validated();

        $validated['publish_status'] = $validated['published'] ? 'published' : 'not_published';
        $validated['challomates_admin_id'] = $sweepStakesCreateRequest->user()->id;
        $message = ($validated['published']) ? "Das Gewinnspiel \"${validated['name']}\" wurde erfolgreich erstellt und publiziert." : "Das Gewinnspiel \"${validated['name']}\" wurde erfolgreich erstellt.";

        unset($validated['published']);
        //dd($message, $validated);

        Sweepstake::create( $validated );
        return redirect()->route('sweepstakes.index' )->with( 'success', $message );
    }

    /**
     * Display the specified resource.
     *
     * @param $sweepstakeId
     * @return \Inertia\Response
     */
    public function show($sweepstakeId): \Inertia\Response
    {
        $sweepstake = $this->sweepstakesQuery()->where( 'id', $sweepstakeId )->first();
        $sweepstake->status = $this->status[$sweepstake->status_with_published_status];
        //dd($sweepstake);
        return Inertia::render('Sweepstakes/Show',[
            'sweepstake'   =>  $sweepstake
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Sweepstake $sweepstake
     * @return \Inertia\Response
     */
    public function edit(Sweepstake $sweepstake): \Inertia\Response
    {
        return Inertia::render('Sweepstakes/Edit', [
            'sweepstake' => $sweepstake
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  \App\Models\Sweepstake  $sweepstake
     * @return RedirectResponse
     */
    public function update(SweepStakesUpdateRequest $sweepStakesUpdateRequest, Sweepstake $sweepstake)
    {
        $validated = $sweepStakesUpdateRequest->validated();

        $sweepstake->name = $validated['name'];
        $sweepstake->challomates_admin_id = $sweepStakesUpdateRequest->user()->id;;
        $sweepstake->type = $validated['type'];
        $sweepstake->runtime_from = $validated['runtime_from'];
        $sweepstake->runtime_to = $validated['runtime_to'];
        $sweepstake->raffle_time = $validated['raffle_time'];
        $sweepstake->price = $validated['price'];
        $sweepstake->number_of_winners = $validated['number_of_winners'];
        $sweepstake->total_sweepstake_number_position = $validated['total_sweepstake_number_position'];
        $sweepstake->winning_number_position_from = $validated['winning_number_position_from'];
        $sweepstake->winning_number_position_to = $validated['winning_number_position_to'];
        $sweepstake->number_of_coins_for_participation = $validated['number_of_coins_for_participation'];
        $sweepstake->publish_status = $validated['published'] ? 'published' : 'not_published';

        $sweepstake->save();

        $parsedUrl = parse_url(URL::previous());
        parse_str( ! empty( $parsedUrl['query'] ) ? $parsedUrl['query'] : '', $output);

        $message = ($validated['published']) ? "Das Gewinnspiel \"${validated['name']}\" wurde erfolgreich aktualisiert und publiziert." : "Das Gewinnspiel \"${validated['name']}\" wurde erfolgreich aktualisiert.";

        return redirect()->route( 'sweepstakes.index', $output )->with('success', $message);

    }

    /**
     * Published Sweepstake
     *
     * @param Request $request
     * @param Sweepstake $sweepstake
     * @return RedirectResponse
     */
    public function publish(Request $request, Sweepstake $sweepstake): RedirectResponse
    {
        $now = Carbon::now();
        $sweepstakeName = $sweepstake->name;
        $sweepstakeStartDate = Carbon::createFromFormat( 'Y-m-d H:i:s', $sweepstake->runtime_from );
        if ( $sweepstakeStartDate->gt( $now ) && ( $sweepstake->status === null || $sweepstake->status === '' ) && $sweepstake->publish_status === 'not_published' ) {
            $sweepstake->publish_status = 'published';
            $sweepstake->save();
            $message = "Das Gewinnspiel \"${sweepstakeName}\" wurde erfolgreich publiziert.";

            return redirect()->route('sweepstakes.index', $request->except('_method') )->with( 'success', $message );
        }

        return redirect()->route('sweepstakes.index', $request->except('_method') )->withErrors('error');
    }

    /**
     * Cancel Sweepstakes
     *
     * @param Request $request
     * @param Sweepstake $sweepstake
     * @return RedirectResponse
     */
    public function cancel(Request $request, Sweepstake $sweepstake): RedirectResponse
    {
        $now = Carbon::now();
        $sweepstakeName = $sweepstake->name;
        $sweepstakeStartDate = Carbon::createFromFormat( 'Y-m-d H:i:s', $sweepstake->runtime_from );
        if (
            ($sweepstakeStartDate->gt( $now ) && ( $sweepstake->status === null || $sweepstake->status === '')) ||
            ($sweepstakeStartDate->lte( $now ) && $sweepstake->publish_status === 'not_published')
        ) {
            $sweepstake->status = 'canceled';
            $sweepstake->save();
            $message = "Das Gewinnspiel \"${sweepstakeName}\" wurde erfolgreich abgebrochen.";

            return redirect()->route('sweepstakes.index', $request->except('_method') )->with( 'success', $message );
        }

        return redirect()->route('sweepstakes.index', $request->except('_method') );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterData(Request $request): \Illuminate\Http\JsonResponse
    {
        $columnName = $request->get( 'column' );
        $sweepstakes = $this->sweepstakesQuery();
        $filterData = [];

        if ( $columnName == 'type' ) {
            $sweepstakes->select('type');
            $filterData = $sweepstakes->pluck('type')
                ->unique()
                ->map( function($type) {
                    return [
                        'name'  =>  $this->type[$type],
                        'value' =>  $type
                    ];
                } );
        }

        if( $columnName == 'status' ) {
            $filterData = $sweepstakes->pluck('status_with_published_status' )
                ->unique()
                ->map( function($status) {
                    return [
                        'name'  =>  $this->status[$status],
                        'value' =>  $status
                    ];
                } );
        }

        return response()->json($filterData);
    }

    private function sweepstakesQuery()
    {
        $query = Sweepstake::query();
        $now = Carbon::now();

        $query->select(
            'id',
            'prefix_id',
            'name',
            'type',
            'runtime_from',
            'runtime_to',
            'raffle_time',
            'price',
            'total_sweepstake_number_position',
            'winning_number_position_from',
            'winning_number_position_to',
            'number_of_coins_for_participation',
            'publish_status as published_status',
            'created_at',
            DB::raw(
                "
                (
                    CASE
                        when runtime_from > '$now' && ( `status` = '' || `status` IS NULL )  then 'new'
                        when runtime_from <= '$now' && runtime_to > '$now' && ( `status` = '' || `status` IS NULL ) then 'active'
                        when runtime_to <= '$now' && ( `status` = '' || `status` IS NULL ) then 'finished'
                        when `status` = 'canceled' then 'canceled'
                        when `status` = 'drawn' then 'drawn'
                    END
                ) AS `status`
                "
            )
        );

        $sweepstakes = DB::table( DB::raw( "({$query->toSql()}) as s" ) )
            ->select(
                '*',
                DB::raw('CONCAT(STATUS, "_", published_status) AS status_with_published_status')
            )
            ->when(request('start_date') && request('end_date'), function($q) {
                $q->whereBetween( DB::raw('date_format(created_at, \'%Y-%m-%d\')'), [ request('start_date'), request('end_date') ] );
            } )
            ->when(request('runtime_start_date') && request('runtime_end_date'), function($q) {
                $q->whereBetween( DB::raw('date_format(runtime_from, \'%Y-%m-%d\')'), [ request('runtime_start_date'), request('runtime_end_date') ] );
                    //->orWhereBetween( DB::raw('date_format(runtime_to, \'%Y-%m-%d\')'), [ request('runtime_start_date'), request('runtime_end_date') ] );
            } )
            ->when(request('raffle_time_start_date') && request('raffle_time_end_date'), function($q) {
                $q->whereBetween( DB::raw('date_format(raffle_time, \'%Y-%m-%d\')'), [ request('raffle_time_start_date'), request('raffle_time_end_date') ] );
            } )
            ->when(request('status'), function($q) {
                $status_arr = explode( ",", request('status') );
                $q->where( function ($q) use ($status_arr) {
                    foreach ($status_arr as $combined_status) {
                        $status = explode('_', $combined_status, 2);
                        $q->orWhere(function ($q) use ($status) {
                            $q->where( 'status', $status[0])
                                ->where( 'published_status', $status[1] );
                        });
                    }
                } );
            } )
            ->when( request('type'), function($q) {
                $types = explode(',', request('type'));
                $q->whereIn( 'type', $types );
            } )
            ->when( request('query'), function($q) {
                $q->where('name', 'LIKE', "%".request('query')."%");
            } )
            ->when(request('order_by'), function($q) {
                if(in_array(request('order_by'), ['name', 'type', 'runtime_from', 'runtime_to', 'raffle_time'])) {
                    $q->orderBy(request('order_by'), request('direction', 'DESC'));
                } else if ( request('order_by') === 'status' ) {
                    $q->orderBy( 'status', request('direction', 'asc') )
                        ->orderBy( 'published_status', 'asc' );
                }
            }, function($q) {
                $q->orderBy( 'status', 'desc' )
                    ->orderBy( 'published_status', 'asc' )
                    ->orderBy( 'runtime_from', 'asc' );
            } );

        return $sweepstakes;
    }
}
