<?php

namespace App\Http\Controllers\Web\Raffles;

use App\Events\NewTrade;
use App\Events\StreamAnswer;
use App\Events\StreamOffer;
use App\Http\Controllers\Controller;
use App\Models\Participation;
use App\Models\Raffle;
use App\Models\RaffleWinner;
use App\Models\Sweepstake;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RaffleController extends Controller
{
    private array $type = array(
        'sweepstake' => 'Gewinnspiel',
        'jackpot' => 'Jackpot'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    // : \Inertia\Response
    public function index()
    {
        $currentJackpotAmount = getCurrentJackpotAmount();

        return Inertia::render(
            'Raffles/Index',
            array(
                'raffles' => $this->raffleQuery()->paginate(request('per_page', 25))->withQueryString(),
                'current_jackpot_amount' => $currentJackpotAmount,
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($raffleId)
    {
        $raffleWinnersByPosition = [];
        $redraw_active = true;
        $raffle = $this->raffleQuery()->where('raffle_id',  $raffleId)->first();
        $raffleWinners = $this->raffleWinnerQuery($raffleId)->get();

        // return response()->json($raffleWinners);

        $maxPosition = (($raffle->raffle_winning_number_position_to - $raffle->raffle_winning_number_position_from) + 1);
        /*for ( $i = $maxPosition, $winnerCount = 0; $i >= 0 && $winnerCount < $raffle->raffle_number_of_winners; $i--, $winnerCount++ ) {
            $winners = $raffleWinners->where( 'winning_position', $i );
            //$raffleWinnersByPosition[$winnerCount] = $winners;
            if ( count( $winners ) ) {
                $raffleWinnersByPosition[$winnerCount] = $winners;
            } else {
                $raffleWinnersByPosition[$winnerCount] = [];
            }
        }*/

        for ($i = $maxPosition, $winnerCount = 0; $i > 0; $i--, $winnerCount++) {
            $winners = $raffleWinners->where('winning_position', $i);
            //$raffleWinnersByPosition[$winnerCount] = $winners;
            if (count($winners)) {
                $redraw_active = false;
                $raffleWinnersByPosition[$winnerCount] = $winners;
            } else {
                $raffleWinnersByPosition[$winnerCount] = [];
            }
        }

        //dd($raffleWinnersByPosition);

		if ( $raffle->raffle_video_src_path != null ) {
			$redraw_active = false;
		}

        return Inertia::render(
            'Raffles/Show',
            array(
                'raffle' => $raffle,
                'raffle_winners' => $raffleWinnersByPosition,
                'redraw_active' => $redraw_active,
            )
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterData(Request $request): \Illuminate\Http\JsonResponse
    {
        $columnName = $request->get('column');
        //$raffles = $this->raffleQuery();
        //dd($raffles->toSql());
        $filterData = [];

        if ($columnName == 'type') {
            $raffles = $this->raffleQuery();
            $filterData = $raffles->pluck('raffle_sweepstake_type')
                ->unique()
                ->map(function ($type) {
                    return [
                        'name'  =>  $this->type[$type],
                        'value' =>  $type
                    ];
                })
                ->toArray();
        }

        if ($columnName == 'sweepstake_id') {
            $raffles = $this->raffleQuery();
            $filterData = $raffles->pluck('raffle_sweepstake_name', 'raffle_sweepstake_id')
                ->unique()
                ->map(function ($raffle_sweepstake_name, $raffle_sweepstake_id) {
                    return [
                        'name'  =>  $raffle_sweepstake_name,
                        'value' =>  $raffle_sweepstake_id
                    ];
                })
                ->toArray();
        }

        if ($columnName == 'winner') {
            $query = Raffle::query()
                ->select(
                    'raffles.id AS raffle_id',
                    'raffles.prefix_id AS raffle_prefix_id',
                    'raffles.winning_number AS raffle_winning_number',
                    'raffles.video_src_path AS raffle_video_src_path',
                    'raffles.started_at AS raffle_started_at',
                    'raffles.stopped_at AS raffle_stopped_at',
                    'raffles.sweepstake_id AS raffle_sweepstake_id',
                    's.name AS raffle_sweepstake_name',
                    's.type AS raffle_sweepstake_type',
                    's.runtime_from AS raffle_runtime_from',
                    's.runtime_to AS raffle_runtime_to',
                    's.raffle_time AS raffle_time',
                    's.winning_number_position_from AS raffle_winning_number_position_from',
                    's.winning_number_position_to AS raffle_winning_number_position_to',
                    'm.id AS mobile_app_user_id',
                    DB::raw(
                        'CONCAT(m.first_name, " ", m.last_name) as mobile_app_user_full_name'
                    ),
                )
                ->leftJoin('sweepstakes as s', 'raffles.sweepstake_id', '=', 's.id')
                ->leftJoin('raffle_winners as rw', 'rw.raffle_id', '=', 'raffles.id')
                ->leftJoin('mobile_app_users as m', 'rw.mobile_app_user_id', '=', 'm.id');

            $raffles = DB::table(DB::raw("({$query->toSql()}) as table1"))
                ->whereNotNull('mobile_app_user_id')
                ->when(request('query'), function ($q) {
                    $q->where('raffle_prefix_id', 'LIKE', "%" . request('query') . "%");
                    $q->orWhere('raffle_sweepstake_name', 'LIKE', "%" . request('query') . "%");
                })
                ->when(request('type'), function ($q) {
                    $sweepstake_type_arr = explode(',', request('type'));
                    $q->whereIn('raffle_sweepstake_type', $sweepstake_type_arr);
                })
                ->when(request('winner'), function ($q) {
                    $sweepstake_winner_arr = explode(',', request('winner'));
                    $q->whereIn('mobile_app_user_id', $sweepstake_winner_arr);

                    //$q->whereIn('mobile_app_user_id', request('winner'));
                })
                ->when(request('raffle_time_start_date') && request('raffle_time_end_date'), function ($q) {
                    $q->whereBetween(DB::raw('date_format(raffle_time, \'%Y-%m-%d\')'), [request('raffle_time_start_date'), request('raffle_time_end_date')]);
                })
                ->when(request('order_by'), function ($q) {
                    $orderBy = request('order_by');
                    if ($orderBy == 'raffle_id') {
                        $q->orderBy('raffle_id', request('direction', 'asc'));
                    }

                    if ($orderBy === 'sweepstake_name') {
                        $q->orderBy('raffle_sweepstake_name', request('direction', 'asc'));
                    }

                    if ($orderBy === 'sweepstake_type') {
                        $q->orderBy('raffle_sweepstake_type', request('direction', 'asc'));
                    }

                    if ($orderBy === 'raffle_time') {
                        $q->orderBy('raffle_time', request('direction', 'asc'));
                    }

                    if ($orderBy === 'winning_numbers_from') {
                        $q->orderBy('raffle_winning_number_position_from', request('direction', 'asc'));
                        $q->orderBy('raffle_winning_number_position_to', 'asc');
                    }

                    if ($orderBy === 'raffle_launch') {
                        $q->orderBy('raffle_started_at', request('direction', 'asc'));
                    }

                    if ($orderBy === 'winner') {
                        $q->orderBy('raffle_mobile_app_user_id', request('direction', 'asc'));
                    }

                    if ($orderBy === 'video') {
                        $q->orderBy('raffle_video_src_path', request('direction', 'asc'));
                        $q->orderBy('raffle_started_at', 'desc');
                    }
                }, function ($q) {
                    $q->orderBy('raffle_time', 'desc');
                })
                ->get();

            $filterData = $raffles
                ->unique('mobile_app_user_id')
                ->pluck('mobile_app_user_full_name', 'mobile_app_user_id')
                ->map(function ($mobile_app_user_full_name, $mobile_app_user_id) {
                    return [
                        'name'  =>  $mobile_app_user_full_name,
                        'value' =>  $mobile_app_user_id
                    ];
                })
                ->sortBy('name')
                ->toArray();

            //dd($filterData);

            return response()->json(array_values($filterData));
        }

        return response()->json(array_values($filterData));
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function raffleQuery(): \Illuminate\Database\Query\Builder
    {
        $query = Raffle::query()
            ->select(
                'raffles.id AS raffle_id',
                'raffles.prefix_id AS raffle_prefix_id',
                'raffles.winning_number AS raffle_winning_number',
                'raffles.video_src_path AS raffle_video_src_path',
                'raffles.started_at AS raffle_started_at',
                'raffles.stopped_at AS raffle_stopped_at',
                'raffles.sweepstake_id AS raffle_sweepstake_id',
                's.name AS raffle_sweepstake_name',
                's.type AS raffle_sweepstake_type',
                's.number_of_winners AS raffle_number_of_winners',
                's.total_sweepstake_number_position AS raffle_total_sweepstake_number_position',
                's.runtime_from AS raffle_runtime_from',
                's.runtime_to AS raffle_runtime_to',
                's.raffle_time AS raffle_time',
                's.winning_number_position_from AS raffle_winning_number_position_from',
                's.winning_number_position_to AS raffle_winning_number_position_to',
                DB::raw(
                    'GROUP_CONCAT(m.id ORDER BY m.id ASC SEPARATOR ",") AS raffle_mobile_app_user_id'
                ),
                DB::raw(
                    'GROUP_CONCAT(CONCAT(m.first_name, " ", m.last_name) ORDER BY m.id ASC SEPARATOR ",") AS mobile_app_user_full_name'
                ),
            )
            ->leftJoin('sweepstakes as s', 'raffles.sweepstake_id', '=', 's.id')
            ->leftJoin('raffle_winners as rw', 'rw.raffle_id', '=', 'raffles.id')
            ->leftJoin('mobile_app_users as m', 'rw.mobile_app_user_id', '=', 'm.id')
            ->when(request('winner'), function ($q) {
                //$sweepstake_winner = request('winner');
                $sweepstake_winner = explode(',', request('winner'));
                //dd( request('winner') );
                //dd( implode(',', $sweepstake_winner) );

                $q->whereRaw('m.id IN (' . implode(',', $sweepstake_winner) . ')');
            })
            ->groupBy(array(
                'raffles.id'
            ));

        return DB::table(DB::raw("({$query->toSql()}) as table1"))
            ->when(request('query'), function ($q) {
                $q->where('raffle_prefix_id', 'LIKE', "%" . request('query') . "%");
                $q->orWhere('raffle_sweepstake_name', 'LIKE', "%" . request('query') . "%");
                $q->orWhere('mobile_app_user_full_name', 'LIKE', "%" . request('query') . "%");
            })
            ->when(request('type'), function ($q) {
                $sweepstake_type_arr = explode(',', request('type'));
                $q->whereIn('raffle_sweepstake_type', $sweepstake_type_arr);
            })
            ->when(request('sweepstake_id'), function ($q) {
                $sweepstake_id_arr = explode(',', request('sweepstake_id'));
                $q->whereIn('raffle_sweepstake_id', $sweepstake_id_arr);
            })
            ->when(request('raffle_time_start_date') && request('raffle_time_end_date'), function ($q) {
                $q->whereBetween(DB::raw('date_format(raffle_time, \'%Y-%m-%d\')'), [request('raffle_time_start_date'), request('raffle_time_end_date')]);
            })
            ->when(request('order_by'), function ($q) {
                $orderBy = request('order_by');
                if ($orderBy == 'raffle_id') {
                    $q->orderBy('raffle_id', request('direction', 'asc'));
                }

                if ($orderBy === 'sweepstake_name') {
                    $q->orderBy('raffle_sweepstake_name', request('direction', 'asc'));
                }

                if ($orderBy === 'sweepstake_type') {
                    $q->orderBy('raffle_sweepstake_type', request('direction', 'asc'));
                }

                if ($orderBy === 'raffle_time') {
                    $q->orderBy('raffle_time', request('direction', 'asc'));
                }

                if ($orderBy === 'winning_numbers_from') {
                    $q->orderBy('raffle_winning_number_position_from', request('direction', 'asc'));
                    $q->orderBy('raffle_winning_number_position_to', 'asc');
                }

                if ($orderBy === 'raffle_launch') {
                    $q->orderBy('raffle_started_at', request('direction', 'asc'));
                }

                if ($orderBy === 'winner') {
                    $q->orderBy('raffle_mobile_app_user_id', request('direction', 'asc'));
                }

                if ($orderBy === 'video') {
                    $q->orderBy('raffle_video_src_path', request('direction', 'asc'));
                    $q->orderBy('raffle_started_at', 'desc');
                }
            }, function ($q) {
                $q->orderBy('raffle_time', 'desc');
            });
    }

    /**
     * @param $raffleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function raffleWinnerQuery($raffleId): \Illuminate\Database\Eloquent\Builder
    {
        return RaffleWinner::query()
            ->select(
                'raffle_winners.id as raffle_winner_id',
                'raffle_winners.winning_number as winning_number',
                'raffle_winners.winning_position as winning_position',
                'm.id as participant_id',
                'm.first_name as first_name',
                'm.last_name as last_name',
            )
            ->leftJoin('mobile_app_users as m', 'raffle_winners.mobile_app_user_id', '=', 'm.id')
            ->where('raffle_winners.raffle_id', $raffleId);
    }

    /**
     * @param $videoName
     * @return \Illuminate\Http\Response
     */
    public function videoStream(Request $request)
    {
        $fileContents = Storage::disk('public')->get($request->video_name);
        $response = Response::make($fileContents, 200);
        $response->header('Content-Type', Storage::disk('public')->mimeType($request->video_name));
        return $response;
    }

    public function webSocket()
    {
        //event ( new \App\Events\NewTrade('Websocket Working....' ) );
        //dd( 'Broadcast OK..' );

        broadcast(new NewTrade('Websocket Working....'));
    }

    public function consuming(Request $request, $streamId)
    {
        return Inertia::render(
            'Raffles/Consume',
            array(
                'streamId' => $streamId,
            )
        );
    }

    public function makeStreamOffer(Request $request)
    {
        //$data['broadcaster'] = $request->broadcaster;
        //$data['receiver'] = $request->receiver;
        //$data['offer'] = $request->offer;

        $data['stream_id'] = '123456';

        event(new StreamOffer($data));
    }

    public function makeStreamAnswer(Request $request)
    {
        //$data['broadcaster'] = $request->broadcaster;
        $data['answer'] = $request->answer;

        $data['stream_id'] = '123456';
        event(new StreamAnswer($data));
    }

    public function captureWinner(Request $request, $raffle_id)
    {
        //dd( $raffle_id, $request->get( 'winning_number' ) );
        $raffle = Raffle::find($raffle_id);
        $winningNumber = $request->get( 'winning_number' );
        //dd( $raffle, $winningNumber, join( '-', $winningNumber ) );

        //dd( $winningNumber );

        if ( $raffle != null ) {
            $sweepstake = Sweepstake::find($raffle->sweepstake_id);
            $raffle->winning_number = join( '-', $winningNumber );
            $sweepstake->status = 'drawn';
            $raffle->save();
            $sweepstake->save();


            //$raffleInfo = (array) $this->raffleWinnerFinderQuery($raffle_id)->get();
            //dd( $raffle, $sweepstake, $raffleInfo );

            // foreach ( $this->raffleWinnerFinderQuery($raffle_id)->get() as $item ) {
            //     $item = (array) $item;
            //     $realRaffleWinningNumber = [];
            //     $winningNumberArr = explode( ',', $item['winning_number'] );
            //     $raffleWinningNumber = explode( '-', $item['raffle_winning_number'] );

            //     for ( $init = ( $item['winning_number_position_from'] - 1 ); $init < $item['winning_number_position_to']; $init++ ) {
            //         $realRaffleWinningNumber[] = $raffleWinningNumber[$init];
            //     }

            //     foreach ( $winningNumberArr as $winningNumber ) {
            //         $realNumberPositionArr = [];
            //         $numberPositionArr = explode( '-', $winningNumber );

            //         for ( $init = ( $item['winning_number_position_from'] - 1 ); $init < $item['winning_number_position_to']; $init++ ) {
            //             $realNumberPositionArr[] = isset($numberPositionArr[$init]) ? $numberPositionArr[$init] : '';
            //         }

            //         $winningPositionCount = 0;
            //         foreach ( $realNumberPositionArr as $index => $value ) {
            //             $winningPositionCount += ( $value == $realRaffleWinningNumber[$index] ) ? 1 : 0;
            //         }

            //         if ( $winningPositionCount > ( count( $realRaffleWinningNumber ) - $item['number_of_winners'] ) ) {
            //             RaffleWinner::create(
            //                 array(
            //                     'participation_id' => $item['participation_id'],
            //                     'raffle_id' => $item['raffle_id'],
            //                     'mobile_app_user_id' => $item['participant_id'],
            //                     'ref_winning_number' => $item['raffle_winning_number'],
            //                     'winning_number' => $winningNumber,
            //                     'winning_position' => $winningPositionCount,
            //                 )
            //             );
            //         }
            //     }
            // }

            $participations = Participation::where('sweepstake_id', $raffle->sweepstake_id)->get();

            info($participations);

            // dd($participations);

            $participations_match_data = [];
            $data = [];
            foreach ($participations as $participation ) {
                $split_participation_winning_numbers = explode('-',  $participation->winning_number);
                $split_winning_numbers =$request->winning_number;

                $matchCount = 0;
                for($i = ($sweepstake->winning_number_position_from - 1) ; $i< $sweepstake->winning_number_position_to; $i++){
                    if($split_winning_numbers[$i] == $split_participation_winning_numbers[$i]){
                        $matchCount++;
                    }
                }
                info('match count '.$matchCount);
                if($matchCount > 0){
                    $participation->match_count = $matchCount;
                    $participations_match_data[] = $participation->toArray();

                    $data[] = [
                        'participation_id' => $participation->id,
                        'raffle_id' => $raffle_id,
                        'mobile_app_user_id' => $participation->mobile_app_user_id,
                        'ref_winning_number' => $raffle->winning_number,
                        'winning_number' => $participation->winning_number,
                        'winning_position' => $matchCount,
                        'created_at'        =>  now()->toDateTimeString(),
                        'updated_at'        =>  now()->toDateTimeString(),
                    ];
                }
            }

            // info($data);

            DB::table('raffle_winners')->insert($data);

            // RaffleWinner::createMany($data);


            // info($participations_match_data);


            $response = array(
                'message' => 'raffle winning number captured.',
                'video_src' => $raffle->video_src_path
            );

            return response()->json($response, 200);
        }

        abort(500);
    }

    private function raffleWinnerFinderQuery($raffle_id)
    {
        $query = Sweepstake::query()
            ->select(
                'sweepstakes.number_of_winners AS number_of_winners',
                'sweepstakes.winning_number_position_from AS winning_number_position_from',
                'sweepstakes.winning_number_position_to AS winning_number_position_to',
                'p.id AS participation_id',
                'p.winning_number AS winning_number',
                'p.mobile_app_user_id AS participant_id',
                'r.id AS raffle_id',
                'r.winning_number AS raffle_winning_number',
            )
            ->leftJoin( 'participations AS p', 'p.sweepstake_id', '=', 'sweepstakes.id' )
            ->leftJoin( 'raffles AS r', 'r.sweepstake_id', '=', 'sweepstakes.id' );

        return DB::table( DB::raw( "({$query->toSql()}) as table1" ) )
            ->where( 'raffle_id', $raffle_id);
        //->whereNotNull( 'raffle_winning_number' )
        //->whereNotNull( 'participation_id' );
    }
}
