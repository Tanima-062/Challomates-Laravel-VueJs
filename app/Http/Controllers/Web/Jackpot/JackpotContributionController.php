<?php

namespace App\Http\Controllers\Web\Jackpot;

use App\Http\Controllers\Controller;
use App\Models\MobileAppUser;
use App\Models\Raffle;
use App\Models\StoreVisits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use function now;

class JackpotContributionController extends Controller
{
    private array $mobile_app_user_type = array(
        'distribution_consumer' => 'Vertriebskonsument',
        'direct_consumer' => 'Direkter Konsument',
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $jackpot_contributions = $this->jackpotQuery();

        return Inertia::render(
            'Jackpot/Index',
            [
                'jackpot_contributions' => $jackpot_contributions->paginate( request('per_page', 25) )->withQueryString(),
            ]
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
    public function show($storeVisitId)
    {
        $jackpot_contribution = $this->jackpotQuery()->where( 'visit_id', $storeVisitId )->first();

        //return response()->json( $jackpot_contribution );

        return Inertia::render(
            'Jackpot/Show',
            [
                'jackpot_contribution' => $jackpot_contribution
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
        $columnName = $request->get( 'column' );
        //$raffles = $this->raffleQuery();
        //dd($raffles->toSql());
        $filterData = [];

        if ( $columnName == 'mobile_app_userss' ) {
            $raffles = $this->jackpotQuery();
            $filterData = $raffles->pluck('mobile_app_user_id')
                ->unique()
                ->map( function($type) {
                    return [
                        'name'  =>  $this->type[$type],
                        'value' =>  $type
                    ];
                } );
        }

        if ( $columnName == 'mobile_app_user' ) {
            $raffles = $this->jackpotQuery();
            $filterData = $raffles->pluck('mobile_app_user_full_name', 'mobile_app_user_id')
                ->unique()
                ->map( function($mobile_app_user_full_name, $mobile_app_user_id) {
                    return [
                        'name'  =>  $mobile_app_user_full_name,
                        'value' =>  $mobile_app_user_id
                    ];
                } );
        }

        if ( $columnName == 'store' ) {
            $raffles = $this->jackpotQuery();
            $filterData = $raffles->pluck('store_name', 'store_id')
                ->unique()
                ->map( function($store_name, $store_id) {
                    return [
                        'name'  =>  $store_name,
                        'value' =>  $store_id
                    ];
                } );
        }

        if ( $columnName == 'mobile_user_type' ) {
            $raffles = $this->jackpotQuery();
            $filterData = $raffles->pluck('mobile_app_user_type' )
                ->unique()
                ->map( function($mobile_app_user_type) {
                    return [
                        'name'  =>  $this->mobile_app_user_type[$mobile_app_user_type],
                        'value' =>  $mobile_app_user_type
                    ];
                } );
        }

        $filterData = $filterData->toArray();

        return response()->json( array_values($filterData) );
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function jackpotQuery(): \Illuminate\Database\Query\Builder
    {
        $query = StoreVisits::query()
            ->select(
                'store_visits.id AS visit_id',
                'store_visits.prefix_id AS store_visit_prefix_id',
                'store_visits.check_out_time AS check_out_time',
                'store_visits.turnover AS turn_over',
                'store_visits.jackpot_share AS jackpot_share',
                'm.id AS mobile_app_user_id',
                'm.prefix_id AS mobile_app_user_prefix_id',
                'm.type AS mobile_app_user_type',
                DB::raw(
                    'CONCAT(m.first_name, " ", m.last_name) as mobile_app_user_full_name'
                ),
                'sp.prefix_id AS distributor_id',
                'sp.id AS store_id',
                'sp.company_name AS store_name',
                'c.prefix_id AS contract_prefix_id',
                'mf.prefix_id AS marketing_fees_prefix_id',
                'mf.direct_consumers_share_jackpot AS direct_consumers_share_jackpot',
                'mf.distribution_consumers_share_jackpot AS distribution_consumers_share_jackpot',
            )
            ->leftJoin( 'mobile_app_users as m', 'store_visits.mobile_app_user_id', '=', 'm.id' )
            ->leftJoin( 'contracts as c', 'store_visits.contract_id', '=', 'c.id' )
            ->leftJoin( 'sales_partners as sp', 'c.sales_partner_id', '=', 'sp.id' )
            ->leftJoin( 'marketing_fees as mf', 'c.marketing_fee_id', '=', 'mf.id' )
            ->whereNotNull( 'store_visits.check_out_time' )
            /*->when(request('winner'), function($q) {
                $sweepstake_winner = explode( ',', request('winner') );
                $q->whereRaw( 'm.id IN (' . implode( ',', $sweepstake_winner) . ')' );
            } )
            ->groupBy( array(
                'raffles.id'
            ) )*/;

        return DB::table( DB::raw( "({$query->toSql()}) as table1" ) )
            ->when( request('query'), function($q) {
                $q->where('store_name', 'LIKE', "%".request('query')."%");
                $q->orWhere('mobile_app_user_full_name', 'LIKE', "%".request('query')."%");
            } )
            ->when( request('store'), function($q) {
                $store_id_arr = explode( ',', request('store') );
                $q->whereIn( 'store_id', $store_id_arr );
            } )
            ->when( request('mobile_app_user'), function($q) {
                /*$mobile_app_user_id_arr = explode( ',', request('mobile_app_user') );
                $q->whereIn( 'mobile_app_user_id', $mobile_app_user_id_arr );*/

                $q->where( 'mobile_app_user_id', request('mobile_app_user') );
            } )
            ->when( request('mobile_user_type'), function($q) {
                $mobile_user_type_arr = explode( ',', request('mobile_user_type') );
                $q->whereIn( 'mobile_app_user_type', $mobile_user_type_arr );
            } )
            ->when(request('check_out_start_date') && request('check_out_end_date'), function($q) {
                $q->whereBetween(DB::raw('date_format(check_out_time, \'%Y-%m-%d\')'), [request('check_out_start_date'), request('check_out_end_date')]);
            } )
            ->when( request('order_by'), function($q) {
                $orderBy = request('order_by');
                if( $orderBy == 'checkout_time' ) {
                    $q->orderBy( 'check_out_time', request('direction', 'asc'));
                }

                if ( $orderBy === 'mobile_app_users' ) {
                    $q->orderBy( 'mobile_app_user_full_name', request('direction', 'asc') );
                }

                if ( $orderBy === 'user_type' ) {
                    $q->orderBy( 'mobile_app_user_type', request('direction', 'asc') );
                }

                if ( $orderBy === 'store' ) {
                    $q->orderBy( 'store_name', request('direction', 'asc') );
                }

                if ( $orderBy === 'turn_over' ) {
                    $q->orderBy( 'turn_over', request('direction', 'asc') );
                }

                if ( $orderBy === 'jackpot' ) {
                    $q->orderBy( 'jackpot_share', request('direction', 'asc') );
                }
            }, function($q) {
                $q->orderBy( 'check_out_time', 'desc' );
                //$q->orderBy( 'visit_id', 'asc' );
            } );
    }
}
