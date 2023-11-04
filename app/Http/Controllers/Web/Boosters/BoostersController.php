<?php

namespace App\Http\Controllers\Web\Boosters;

use App\Http\Controllers\Controller     ;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Booster;
use App\Models\BoosterPost;
use App\Models\BoosterBoosterType;
use App\Models\SalesPartner;
use App\Models\Contract;
use App\Traits\UploadAble;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BoostersController extends Controller
{
    use UploadAble;

    public function index(Request $request){

        $result = $this->boostersQuery($request);

        $boosters = $result['boosters']
                    ->paginate(request('per_page', 25))
                    ->withQueryString();

        return Inertia::render('Boosters/Index', [
            'boosters' => $boosters,
            'sales_partners' => $result['sales_partners'],
        ]);
    }

    public function create(){
        $sales_partners = SalesPartner::where('status','active')->orderBy('company_name','asc')->get();
        return Inertia::render('Boosters/Create', [
            'sales_partners' => $sales_partners,
            'latest_id' => nextId('boosters', Booster::PREFIX)
        ]);
    }

    public function store(Request $request){

        $message = 'Der Booster wurde erfolgreich erstellt.';

        $booster = new Booster();
        $booster->sales_partner_id = $request->sales_partner;
        $booster->contract_id = $request->contract;
        $booster->title = $request->title;
        $booster->body = $request->body;

        if($request->file('file')){
            $booster->file = $this->uploadOne($request->file, 'boosters', env('FILESYSTEM_DISK'));
            $booster->file_name = $request->file('file')->getClientOriginalName();
        }

        $booster->range = $request->range;
        $booster->type = $request->booster_type;
        $booster->save();

        $booster_booster_types = [];

        if($request->booster_type == 'One Time'){

            $booster_booster_type = new BoosterBoosterType();
            $booster_booster_type->booster_id = $booster->id;
            $booster_booster_type->save();

            $booster->posting_time = $request->posting_time;
            $booster->start = date('Y-m-d', strtotime($request->posting_time));

            if($booster->start > date('Y-m-d')){
                $booster->status = "new";
            }elseif($booster->start == date('Y-m-d')){
                $booster->status = "posted";
            }

            $booster->save();

        }else if($request->booster_type == 'Recurring'){

            for($i = 0 ; $i < count($request->weeks); $i++){

                array_push($booster_booster_types, [
                    'booster_id' => $booster->id,
                    'number_of_boosters_month' => $request->number_of_boosters_month,
                    'week' => $request->weeks[$i],
                    'weekday' => $request->weekdays[$i],
                    'time' => $request->times[$i],
                ]);

            }

            DB::table('booster_booster_types')->insert($booster_booster_types);

            $booster->start = $request->start;
            $booster->end = $request->end;

            if($booster->start > date('Y-m-d')){
                $booster->status = "new";
            }elseif($booster->end < date('Y-m-d')){
                $booster->status = "expired";
            }

            $booster->save();
        }

        return redirect()->route('boosters.index')->with('success', $message);
    }

    public function edit(Booster $booster)
    {
        $sales_partners = SalesPartner::orderBy('company_name','asc')->get();
        $contracts = Contract::where('sales_partner_id',$booster->sales_partner_id)->orderBy('name','asc')->get();
        $booster->append('file_url');

        return Inertia::render('Boosters/Edit', [
            'sales_partners' => $sales_partners,
            'contracts' => $contracts,
            'booster' => $booster
        ]);
    }

    public function update(Request $request, Booster $booster)
    {
        $booster->sales_partner_id = $request->sales_partner;
        $booster->contract_id = $request->contract;
        $booster->title = $request->title;
        $booster->body = $request->body;

        if($request->file('file')){
            $booster->file = $this->uploadOne($request->file, 'boosters', env('FILESYSTEM_DISK'));
            $booster->file_name = $request->file('file')->getClientOriginalName();
        }

        if($request->file == null){
            $booster->file = $request->file;
            $booster->file_name = $request->file_name;
        }

        $booster->range = $request->range;
        $booster->type = $request->booster_type;
        $booster->save();

        BoosterBoosterType::where('booster_id',$booster->id)->delete();

        $booster_booster_types = [];
        if($request->booster_type == 'One Time'){

            $booster_booster_type = new BoosterBoosterType();
            $booster_booster_type->booster_id = $booster->id;
            $booster_booster_type->save();

            $booster->posting_time = $request->posting_time;
            $booster->start = date('Y-m-d', strtotime($request->posting_time));

            if($booster->start > date('Y-m-d')){
                $booster->status = "new";
            }elseif($booster->start == date('Y-m-d')){
                $booster->status = "posted";
            }

            $booster->save();

        }else if($request->booster_type == 'Recurring'){

            for($i =0 ; $i < count($request->weeks); $i++){

                array_push($booster_booster_types, [
                    'booster_id' => $booster->id,
                    'number_of_boosters_month' => $request->number_of_boosters_month,
                    'week' => $request->weeks[$i],
                    'weekday' => $request->weekdays[$i],
                    'time' => $request->times[$i]
                ]);

            }
            DB::table('booster_booster_types')->insert($booster_booster_types);

            $booster->start = $request->start;
            $booster->end = $request->end;

            if($booster->start > date('Y-m-d')){
                $booster->status = "new";
            }elseif($booster->end < date('Y-m-d')){
                $booster->status = "expired";
            }
            $booster->save();
        }
        $message = sprintf('Der Booster "%s" für den Vertriebspartner "%s" wurde erfolgreich aktualisiert.',$booster->title, $booster->salesPartner->company_name);
        return redirect()->route('boosters.index')->with('success', $message);
    }
    function toggleStatus(Booster $booster)
    {
        $booster->status = $booster->status == Booster::STATUS_INACTIVE ? Booster::STATUS_ACTIVE : Booster::STATUS_INACTIVE;
        $booster->save();
        $status = ($booster->status == Booster::STATUS_ACTIVE) ? 'aktiviert' : 'deaktiviert';
        $company_name = $booster->salesPartner->company_name;
        $message = sprintf('Der Booster "%s" für den Vertriebspartner "%s" wurde erfolgreich %s.', $booster->title, $company_name, $status);
        return redirect()->back()->with('success', $message);
    }

    public function show(Request $request, Booster $booster)
    {
        $booster->append('file_url');
        return Inertia::render('Boosters/Show', [
            'booster' => $booster
        ]);
    }

    public function getContracts($sales_partner_id){

        $boosters = Booster::where('sales_partner_id', $sales_partner_id)
                    ->whereIn('status',['active','new'])
                    ->get();
        $contracts = Contract::where('sales_partner_id', $sales_partner_id)->orderBy('name','asc')->get();
        $sales_partner = SalesPartner::find($sales_partner_id);
        return ['contract' => $contracts, 'booster' => $boosters, 'sales_partner' => $sales_partner];

    }

    public function filterData(Request $request)
    {
        $columnName = $request->get('column');
        $result = $this->boostersQuery($request);
        $filterData = [];

        if( $columnName == 'status' ) {
            $bIds = $result['boosters']->pluck('status')->unique()->toArray();
            foreach($bIds as $bId){
                if($bId == 'active'){
                    array_push($filterData, ['name'=>  'Aktiv', 'value' => $bId]);
                }
                elseif($bId == 'inactive'){
                    array_push($filterData, ['name'=>  'Inaktiv', 'value' => $bId]);
                }
                elseif($bId == 'new'){
                    array_push($filterData, ['name'=>  'Neu', 'value' => $bId]);
                }
                elseif($bId == 'expired'){
                    array_push($filterData, ['name'=>  'Abgelaufen', 'value' => $bId]);
                }
                elseif($bId == 'posted'){
                    array_push($filterData, ['name'=>  'Posted', 'value' => $bId]);
                }
            }
        }

        if( $columnName == 'sales_partner' ) {
            $filterData = $result['sales_partners'];
        }

        if( $columnName == 'booster_type' ) {
            $bIds = $result['boosters']->pluck('type')->unique()->toArray();
            foreach($bIds as $bId){
                if($bId == 'One Time'){
                    array_push($filterData, ['name'=>  'Einmalig', 'value' => $bId]);
                }
                elseif($bId == 'Recurring'){
                    array_push($filterData, ['name'=>  'Wiederkehrend', 'value' => $bId]);
                }
            }
        }

        return response()->json($filterData);
    }

    public function boostersQuery(Request $request){
        $query = $request->get('query');
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $start = $request->get('runtime_start_date');
        $end = $request->get('runtime_end_date');
        $status = $request->get('status', null);
        $booster_type = $request->get('booster_type', null);
        $order_by = $request->get('order_by', 'id');
        $direction = strtolower($request->get('direction', 'ASC'));
        $query = $request->get('query');
        $sales_partner = $request->get('sales_partner', null);

        DB::table('boosters')->where('status', '!=', 'inactive')->where('start', '>', date('Y-m-d'))->update(['status' => 'new']);
        DB::table('boosters')->where('status', '!=', 'inactive')->where('type','Recurring')->where('start', '<=', date('Y-m-d'))->where('end', '>', date('Y-m-d'))->update(['status' => 'active']);
        DB::table('boosters')->where('type','Recurring')->where('end', '<', date('Y-m-d'))->update(['status' => 'expired']);
        DB::table('boosters')->where('type','One Time')->where('start', '<=', date('Y-m-d'))->update(['status' => 'posted']);

        $boosterQuery = Booster::query()
                ->where(function ($q) use ($query) {
                    if (!is_null($query)) {
                        $q->where('prefix_id', 'like', "%${query}%")
                        ->orWhere('title', 'like', "%${query}%")
                        ->orWhereHas('salesPartner', function($q) use ($query){
                            $q->where('company_name', 'like', "%${query}%");
                        });
                    }
                })
                ->where(function ($q) use ($status) {
                    if (!is_null($status)) {
                        $statusArr = explode(',', $status);
                        foreach ($statusArr as $term) {
                            $q->orWhere('status', $term);
                        }
                    }
                })
                ->where(function ($q) use ($start_date, $end_date) {
                    if (!is_null($start_date) && !is_null($end_date)) {
                        $q->whereBetween(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), [$start_date, $end_date]);
                    }
                })
                ->where(function ($q) use ($start, $end) {
                    if (!is_null($start) && !is_null($end)) {
                        $q->whereBetween('start', [$start, $end]);
                    }
                })
                ->where(function ($q) use ($booster_type) {
                    if (!is_null($booster_type)) {
                        $booster_type = $booster_type == 'Wiederkehrend' ? 'Recurring' : 'One Time';
                        $q->where('type', $booster_type);
                    }
                })
                ->whereHas('salesPartner',function ($q) use ($sales_partner){
                    if(!is_null($sales_partner)){
                        $sales_partner = explode(',', $sales_partner);
                        $q->whereIn('id', $sales_partner);
                    }
                });

        $booster = clone $boosterQuery;
        $boosterS = clone $boosterQuery;

        $sIds = $boosterS->groupBy('sales_partner_id')->pluck('sales_partner_id')->toArray();
        $sales_partners = SalesPartner::whereIn('id', $sIds)->orderBy('company_name','asc')->get()->map(function($item, $index) {
            return [
            "value" => $item["id"],
            "name" => $item["company_name"]
            ];
        })->toArray();

        $boosters = clone $boosterQuery->when(request('order_by') == 'sales_partner_id', function($q) use ($direction){
                        $q->orderBy(
                            SalesPartner::select('company_name')
                                ->whereColumn('boosters.sales_partner_id','sales_partners.id'), $direction);
                    })
                    ->when(in_array(request('order_by'), ['title', 'status', 'type', 'start','created_at']), function($q) use ($order_by,$direction){
                        $q->orderBy($order_by,$direction);
                    });
        return ['boosters' => $boosters, 'sales_partners' => $sales_partners];
    }

}
