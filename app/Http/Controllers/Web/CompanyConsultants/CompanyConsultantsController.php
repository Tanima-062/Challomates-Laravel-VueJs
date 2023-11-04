<?php

namespace App\Http\Controllers\Web\CompanyConsultants;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyConsultants\CompanyConsultantCreateRequest;
use App\Http\Requests\CompanyConsultants\CompanyConsultantUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Models\User;

class CompanyConsultantsController extends Controller
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

    public function __construct()
    {
        $this->middleware('permission:company_consultants.view')->only(['index', 'filterData']);
        $this->middleware('permission:company_consultants.create')->only(['create', 'store']);
        $this->middleware('permission:company_consultants.edit')->only(['edit', 'update', 'toggleStatus']);
    }

    public function index(Request $request) {
        $query = $request->get('query');
        $order_by = $request->get('order_by', 'id');
        $status = $request->get('status', null);
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $direction = strtolower($request->get('direction', 'ASC'));
        $per_page = $request->get('per_page', 25);

        $companyConsultants = User::query()->where('type', 'company_consultant')
            ->where(function ($q) use ($query) {
                if (!is_null($query)) {
                    //old code
                    // $queryArr = explode( ' ',  $query );

                    // if ( count( $queryArr ) > 1 ) {
                    //     $q->where('first_name', 'like', "%${queryArr[0]}%")
                    //         ->where('last_name', 'like', "%${queryArr[1]}%");
                    // } else {
                    //     $q->where('first_name', 'like', "%${query}%")
                    //         ->orWhere('last_name', 'like', "%${query}%")
                    //         ->orWhere('email', 'like', "%${query}%")
                    //         ->orWhere('prefix_id', 'like', "%${query}%")
                    //         ->orWhere('phone_number', 'like', "%${query}%")
                    //         ->orWhere('full_phone_number', 'like', "%${query}%");
                    // }

                    $q->where('first_name', 'like', "${query}%")
                    ->orWhere('last_name', 'like', "${query}%")
                    ->orWhere('email', 'like', "%${query}%")
                    ->orWhere('prefix_id', 'like', "%${query}%")
                    ->orWhere('phone_number', 'like', "%${query}%")
                    ->orWhere('full_phone_number', 'like', "%${query}%")
                    ->orWhereRaw("CONCAT(`first_name`, ' ', `last_name`) LIKE ?", [$query . '%'])
                    ;
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
            ->orderBy($order_by, $direction) //->toSql();
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('CompanyConsultants/Index', [
            'company_consultants' => $companyConsultants
        ]);
    }

    public function create()
    {
        return Inertia::render('CompanyConsultants/Create', [
            'latest_id' => getUserPrefix('company_consultant') . User::getNextPrefixID('company_consultant')
        ]);
    }

    public function store(CompanyConsultantCreateRequest $companyConsultantCreateRequest)
    {
        $message = 'Der Company Consultant wurde erfolgreich erstellt.';

        $validated = $companyConsultantCreateRequest->validated();
        //dd($validated);

        User::create(
            array(
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email_address'],
                'phone_number' => $validated['phone_number'],
                'country_iso_code' => strtoupper($validated['country_iso_code']),
                'type' => 'company_consultant',
                'status' => User::STATUS_ACTIVE,
            )
        );

        return redirect()->route('company-consultants.index')->with('success', $message);
    }

    public function edit(User $company_consultant)
    {
        //dd($challo_mates_admin);
        return Inertia::render('CompanyConsultants/Edit', [
            'company_consultant' => $company_consultant
        ]);
    }

    public function update(CompanyConsultantUpdateRequest $companyConsultantUpdateRequest, User $company_consultant)
    {
        $validated = $companyConsultantUpdateRequest->validated();

        //dd($companyConsultantUpdateRequest->all(), $output, $validated, $company_consultant, $company_consultant->name, "Der Company Consultant \"{$company_consultant->name}\" wurde erfolgreich aktualisiert.");

        $company_consultant->first_name = $validated['first_name'];
        $company_consultant->last_name = $validated['last_name'];
        $company_consultant->email = $validated['email_address'];
        $company_consultant->phone_number = $validated['phone_number'];
        $company_consultant->country_iso_code = $validated['country_iso_code'];
        $company_consultant->save();

        $parsedUrl = parse_url(URL::previous());
        parse_str( ! empty( $parsedUrl['query'] ) ? $parsedUrl['query'] : '', $output);

        return redirect()->route( 'company-consultants.index', $output )->with('success', "Der Company Consultant \"{$company_consultant->name}\" wurde erfolgreich aktualisiert.");
    }

    public function toggleStatus(Request $request, User $consultant)
    {
        $consultant->status = $consultant->status == User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $consultant->save();
        $status = $consultant->status == User::STATUS_ACTIVE ? 'aktiviert' : 'deaktiviert';
        $consultant_name = $consultant->name;
        return redirect()->route('company-consultants.index', $request->except('_method'))->with('success', "Der Company Consultant \"${consultant_name}\" wurde erfolgreich ${status}.");
    }

    public function filterData(Request $request)
    {
        $columnName = $request->get('column');
        $query = $request->get('query');
        $status = $request->get('status', null);
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);

        $companyConsultants = User::query()->where('type', 'company_consultant')
            ->where(function ($q) use ($query) {
                if (!is_null($query)) {
                    $queryArr = explode( ' ',  $query );

                    if ( count( $queryArr ) > 1 ) {
                        $q->where('first_name', 'like', "%${queryArr[0]}%")
                            ->where('last_name', 'like', "%${queryArr[1]}%");
                    } else {
                        $q->where('first_name', 'like', "%${query}%")
                            ->orWhere('last_name', 'like', "%${query}%")
                            ->orWhere('email', 'like', "%${query}%")
                            ->orWhere('prefix_id', 'like', "%${query}%")
                            ->orWhere('phone_number', 'like', "%${query}%")
                            ->orWhere('full_phone_number', 'like', "%${query}%");
                    }
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
            });

        $filterData = [];

        if ($columnName === 'status') {
            $filterData = collect( $companyConsultants->get()->unique('status' ) )->map( function ($item) {
                $status = $item->status;
                return self::$status[$status];
            } );
        }

        return response()->json($filterData);
    }
}
