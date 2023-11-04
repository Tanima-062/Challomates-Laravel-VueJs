<?php

namespace App\Http\Controllers\Web\Packages;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\Package;
use App\Models\PackageFirstYearFee;
use App\Models\PackageOneTimeRegistrationFee;
use App\Models\PackageYearlyFee;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class PackageController extends Controller
{
    private static $status = array(
        'active' => array(
            'name' => 'Aktiv',
            'value' => 'active',
        ),

        'inactive' => array(
            'name' => 'Inaktiv',
            'value' => 'inactive',
        )
    );

    private $priceRange, $min_fee, $max_fee;

    public function __construct()
    {
        $this->middleware('permission:package.view')->only(['index', 'show', 'showMultiple', 'filterData']);
        $this->middleware('permission:package.create')->only(['create', 'store']);
        $this->middleware('permission:package.edit')->only(['edit', 'update', 'toggleStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $per_page    = request('per_page', 25);
        $packageList = $this->packageQuery();
        //dd( $packageList->toSql() );

        return Inertia::render('Package/Index', [
            'packages' => $packageList->paginate( $per_page )->withQueryString(),
            'priceRanges' => $this->priceRange,
            'minValue' => $this->min_fee,
            'maxValue' => $this->max_fee
            /*'filter_status' => $packageList->get()->map( function ($package) {
                $status = $package->status;
                return self::$status[$status];
            } )->unique(),*/
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render(
            'Package/Create',
            [
                'latest_id' => nextId('package', 'P')
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PackageRequest $packageRequest): \Illuminate\Http\RedirectResponse
    {
        $validated = $packageRequest->validated();
        //dd($validated);
        Package::create(
            array(
                'package_name'           => $validated['package_name'],
                'services'               => $validated['services'],
                'registration_fee'       => $validated['registration_fee'],
                'first_year_fee'         => $validated['first_year_fee'],
                'yearly_fee'             => $validated['yearly_fee'],
                'coin_factor'            => $validated['coin_factor'],
                'consulting'             => $validated['consulting'],
                'booster'                => $validated['booster'],
                'number_of_registration' => $validated['number_of_registration'],
            )
        );

        return redirect()->route('package.index' )->with( 'success', "Das Paket wurde erfolgreich erstellt." );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($packageId): \Inertia\Response
    {
        $package = DB::table('package as p')
            ->select(
                'p.id as id',
                DB::raw( 'COUNT(c.package_id) AS package_assigned_count' ),
                'p.package_prefix_id as package_prefix_id',
                'p.package_name as package_name',
                'p.services as services',
                DB::raw( 'round(p.registration_fee, 2) as registration_fee' ),
                DB::raw( 'round(p.first_year_fee, 2) as first_year_package_fee' ),
                DB::raw( 'round(p.yearly_fee, 2) as package_fee' ),
                'p.coin_factor as coin_factor',
                'p.consulting as consulting',
                'p.booster as booster',
                'p.number_of_registration as number_of_registration',
                'p.status as status',
                'p.created_at as created_at'
            )
            ->leftJoin( 'contracts AS c', 'p.id', '=', 'c.package_id' )
            ->whereNull('p.deleted_at')
            ->where('p.id', $packageId)
            ->groupBy( 'p.id' )
            ->get()->first();

        return Inertia::render(
            'Package/Show',
            [
                'pkg'      => $package,
            ]
        );
    }

    /**
     * Display and compare the specified resources.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function showMultiple(Request $request): \Inertia\Response
    {
        $packages = [];
        if (!is_null($request->packages) && !empty($request->packages) && isset($request->packages)) {
            $packages = DB::table('package as p')
                ->select(
                    'p.id as id',
                    DB::raw( 'COUNT(c.package_id) AS package_assigned_count' ),
                    'p.package_prefix_id as package_prefix_id',
                    'p.package_name as package_name',
                    'p.services as services',
                    DB::raw( 'round(p.registration_fee, 2) as registration_fee' ),
                    DB::raw( 'round(p.first_year_fee, 2) as first_year_package_fee' ),
                    DB::raw( 'round(p.yearly_fee, 2) as package_fee' ),
                    'p.coin_factor as coin_factor',
                    'p.consulting as consulting',
                    'p.booster as booster',
                    'p.number_of_registration as number_of_registration',
                    'p.status as status',
                    'p.created_at as created_at'
                )
                ->leftJoin( 'contracts AS c', 'p.id', '=', 'c.package_id' )
                ->whereNull('p.deleted_at')
                ->whereIn('p.id', $request->packages)
                ->orderBy( 'p.yearly_fee' )
                ->groupBy( 'p.id' )
                ->get();
        }

        return Inertia::render('Package/ShowMultiple', [
            'packages' => $packages,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($packageId): \Inertia\Response
    {
        $package = DB::table('package as p')
            ->select(
                'p.id as id',
                'p.package_prefix_id as package_prefix_id',
                'p.package_name as package_name',
                'p.services as services',
                DB::raw( 'round(p.registration_fee, 2) as registration_fee' ),
                DB::raw( 'round(p.first_year_fee, 2) as first_year_package_fee' ),
                DB::raw( 'round(p.yearly_fee, 2) as package_fee' ),
                'p.coin_factor as coin_factor',
                'p.consulting as consulting',
                'p.booster as booster',
                'p.number_of_registration as number_of_registration',
                'p.status as status',
                DB::raw('date_format(p.created_at, \'%d.%m.%Y\') as created_at')
            )
            ->whereNull('p.deleted_at')
            ->where('p.id', $packageId)
            ->get()->first();

        return Inertia::render(
            'Package/Edit',
            [
                'package'  => $package,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PackageRequest $packageRequest, $packageId): \Illuminate\Http\RedirectResponse
    {
        $validated = $packageRequest->validated();
        DB::transaction(function () use ($packageId, $validated) {

            $service = [];
            $package = Package::find($packageId);

            $package->package_name           = $validated['package_name'];
            $package->services               = $validated['services'];
            $package->registration_fee       = $validated['registration_fee'];
            $package->first_year_fee         = $validated['first_year_fee'];
            $package->yearly_fee             = $validated['yearly_fee'];
            $package->coin_factor            = $validated['coin_factor'];
            $package->consulting             = $validated['consulting'];
            $package->booster                = $validated['booster'];
            $package->number_of_registration = $validated['number_of_registration'];
            $package->save();
        });

        $parsedUrl = parse_url(URL::previous());
        parse_str( ! empty( $parsedUrl['query'] ) ? $parsedUrl['query'] : '', $output);

        return redirect()->route('package.index', $output)->with('success', "Das Paket \"${validated['package_name']}\" wurde erfolgreich aktualisiert.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the status of the specific resource in storage.
     *
     * @param Package $package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Package $package): \Illuminate\Http\RedirectResponse
    {
        $package->status = $package->status == Package::ACTIVE ? Package::INACTIVE : Package::ACTIVE;
        $package->save();

        $status = $package->status == Package::ACTIVE ? 'aktiviert' : 'deaktiviert';
        $package_name = $package->package_name;

        $parsedUrl = parse_url(URL::previous());
        parse_str( ! empty( $parsedUrl['query'] ) ? $parsedUrl['query'] : '', $output);

        return redirect()->route('package.index', $output)->with('success', "Das Paket \"${package_name}\" wurde erfolgreich ${status}.");
    }

    /**
     * Collect the available filter status based on requested param.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterData(Request $request): \Illuminate\Http\JsonResponse
    {
        $filterData = [];
        $columnName = request('column');
        /*$query       = $request->get('query');
        $status      = $request->get('status', null);
        $start_date  = $request->get('start_date', null);
        $end_date    = $request->get('end_date', null);
        $package_fee = $request->get('package_fee');*/

        //dd($package_fee);

        /*$packages = DB::table('package as p')
            ->select(
                'p.id as id',
                'p.package_prefix_id as package_prefix_id',
                'p.package_name as package_name',
                DB::raw( 'round(p.registration_fee, 2) as registration_fee' ),
                DB::raw( 'round(p.first_year_fee, 2) as first_year_package_fee' ),
                DB::raw( 'round(p.yearly_fee, 2) as package_fee' ),
                DB::raw( 'CONCAT( "CHF ", p.yearly_fee ) as package_fee_label' ),
                'p.coin_factor as coin_factor',
                'p.consulting as consulting',
                'p.booster as booster',
                'p.number_of_registration as number_of_registration',
                'p.status as status',
                'p.created_at as created_at'
            )
            ->whereNull('p.deleted_at')
            ->where(function ($q) use ($status) {
                if (!is_null($status)) {
                    $statusArr = explode(',', $status);
                    foreach ($statusArr as $term) {
                        $q->orWhere('p.status', $term);
                    }
                }
            })
            ->where(function ($q) use ($package_fee) {
                if (!is_null($package_fee)) {
                    $package_fee_arr = explode(',', $package_fee);
                    foreach ($package_fee_arr as $fee_id) {
                        $q->orWhere('pyf.package_fee', $fee_id);
                    }
                }
            })
            ->where(function ($q) use ($start_date, $end_date) {
                if (!is_null($start_date) && !is_null($end_date)) {
                    $q->whereBetween(DB::raw('date_format(p.created_at, \'%Y-%m-%d\')'), [$start_date, $end_date]);
                }
            })
            ->when($query, function ($q, $query) {
                $q->where('p.package_name', 'like', "%${query}%");
            });*/

        $packages = $this->packageQuery();

        if ( $columnName === 'package_fee' ) {
            $filterData = $packages->get()->unique('package_fee' );
        } else if ($columnName === 'status') {
            $filterData = collect( $packages->get()->unique('status' ) )->map( function ($item) {
                $status = $item->status;
                return self::$status[$status];
            } )->sortBy(
                ['value', 'asc']
            );
        }

        return response()->json($filterData);
    }


    public function packageQuery()
    {
        $query       = request('query');
        $order_by    = request('order_by');
        $status      = request('status', null);
        $start_date  = request('start_date', null);
        $end_date    = request('end_date', null);
        $direction   = strtolower(request('direction', 'ASC'));
        $start_price = request('start_price');
        $end_price   = request('end_price');

        //dd($package_fee);

        $packageList = DB::table('package as p')
            ->select(
                'p.id as id',
                DB::raw( 'COUNT(c.package_id) AS package_assigned_count' ),
                'p.package_prefix_id as package_prefix_id',
                'p.package_name as package_name',
                DB::raw( 'round(p.registration_fee, 2) as registration_fee' ),
                DB::raw( 'round(p.first_year_fee, 2) as first_year_package_fee' ),
                DB::raw( 'round(p.yearly_fee, 2) as package_fee' ),
                'p.coin_factor as coin_factor',
                'p.consulting as consulting',
                'p.booster as booster',
                'p.number_of_registration as number_of_registration',
                'p.status as status',
                'p.created_at as created_at'
            )
            ->leftJoin( 'contracts AS c', 'p.id', '=', 'c.package_id' )
            ->whereNull('p.deleted_at')
            ->where(function ($q) use ($status) {
                if (!is_null($status)) {
                    $statusArr = explode(',', $status);
                    foreach ($statusArr as $term) {
                        $q->orWhere('p.status', $term);
                    }
                }
            })
            ->where(function ($q) use ($start_date, $end_date) {
                if (!is_null($start_date) && !is_null($end_date)) {
                    $q->whereBetween(DB::raw('date_format(p.created_at, \'%Y-%m-%d\')'), [$start_date, $end_date]);
                }
            })
            ->when($order_by, function ($q, $order_by) use ($direction) {
                $q->orderBy($order_by, $direction);
            }, function ($q) {
                $q->orderBy('status', 'asc')->orderBy('package_fee', 'asc');
            })
            ->when($query, function ($q, $query) {
                $q->where('p.package_name', 'like', "%${query}%");
            });

        $this->min_fee = $packageList->min('p.yearly_fee');
        $this->max_fee = $packageList->max('p.yearly_fee');
        $this->priceRange = getPriceRanges($this->min_fee, $this->max_fee, 50.00);

        return $packageList
            ->where(function ($q) use ($start_price, $end_price) {
                if (!is_null($start_price) && !is_null($end_price)) {
                    $q->whereBetween('p.yearly_fee', [$start_price, $end_price]);
                }
            })
            ->groupBy( 'p.id' );
    }
}
