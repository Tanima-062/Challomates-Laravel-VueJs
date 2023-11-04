<?php

namespace App\Http\Controllers\Web\SalesPartner;

use App\Models\User;
use Inertia\Inertia;
use App\Mail\TestMail;
use App\Models\Branch;
use App\Models\Contract;
use Illuminate\Support\Str;
use App\Models\SalesPartner;
use Illuminate\Http\Request;
use App\Mail\SendQrCodeEmail;
use App\Models\BranchCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\SalesPartnerOpeningHours;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Http\Requests\SalesPartner\SalesPartnerCreateRequest;
use App\Http\Requests\SalesPartner\SalesPartnerUpdateRequest;

class SalesPartnerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:sales_partner.view')->only(['index', 'show', 'getFilterableData']);
        $this->middleware('permission:sales_partner.create')->only(['create', 'store']);
        $this->middleware('permission:sales_partner.edit')->only(['edit', 'update', 'updateStatus', 'deleteProfilePicture', 'assignConsultant']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sales_partners = SalesPartner::with(['branchCategory', 'consultant', 'openingHours', 'contracts'])
            ->search($request->get('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->companyConsultantIn($request->company_consultant)
            ->branchIn($request->branches)
            ->branchCategoryIn($request->branch_catagories)
            ->cityIn($request->cities)
            ->statusIn($request->status)
            ->sortByColumns($request->order_by, $request->direction);

        $sales_partners = $sales_partners
            ->paginate($request->per_page ?? 25)
            ->withQueryString();

        $sales_partners->each(fn ($sales_partner) => $sales_partner->append('current_contract'));

        $consultants = User::isConsultant()->where('status', 'active')->select('id', 'first_name', 'last_name', 'prefix_id')->get();
        return Inertia::render('SalesPartner/Views/SalesPartner/Index', ['sales_partners' => $sales_partners, 'company_consultants' => $consultants, 'select_all' => false]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $latest_id = SalesPartner::getNextPrefixId();
        $branch = Branch::all();
        $branch_categories = BranchCategory::all();
        $company_consultants = User::isConsultant()->where('status', 'active')->get();
        return Inertia::render('SalesPartner/Views/SalesPartner/Create', ['branch' => $branch, 'branch_categories' => $branch_categories, 'company_consultants' => $company_consultants, 'latest_id' => $latest_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SalesPartnerCreateRequest $request)
    {
        $input = $request->validated();
        $opening_hours = isset($input['opening_hours']) && !$input['no_information'] ? $input['opening_hours'] : [];
        $contract = isset($input['contract']) && $input['contract'] ? $input['contract'] : null;

        unset($input['contract']);
        unset($input['opening_hours']);

        $input['challo_mates_admin_id'] = auth()->id();

        $input['profile_picture'] = $request->file('profile_picture')?->store('sales_partner/profile_picture', env('FILESYSTEM_DISK', 'public'));
        $input['receipt_template'] = $request->file('receipt_template')?->store('sales_partner/receipt_template', env('FILESYSTEM_DISK', 'public'));
        $input['receipt_template_name'] = $request->file('receipt_template')?->getClientOriginalName();

        if (gettype($request->profile_picture) == 'string' && Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($request->profile_picture)) {
            $name = Str::random(15) . time() . substr($request->profile_picture, strripos($request->profile_picture, '.'));
            $path = 'sales_partner/profile_picture';
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->move($request->profile_picture, "$path/$name");
            $input['profile_picture'] = "$path/$name";
        }

        if (gettype($request->receipt_template) == 'string' && Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($request->receipt_template)) {
            $name = Str::random(15) . time() . substr($request->receipt_template, strripos($request->receipt_template, '.'));
            $path = 'sales_partner/receipt_template';
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->move($request->receipt_template, "$path/$name");
            $input['receipt_template'] = "$path/$name";
            $input['receipt_template_name'] = $request->receipt_template_name;
        }


        $sales_partner = SalesPartner::create($input + ['coordinates' => new Point($input['latitude'], $input['longitude'])]);

        $opening_hours = array_map(fn ($item) => [...$item, 'sales_partner_id' => $sales_partner->id, 'created_at' => now(), 'updated_at' => now()], $opening_hours);
        SalesPartnerOpeningHours::insert($opening_hours);

        if ($contract) {
            $sales_partner->contracts()->create($contract);
        }

        $sales_partner = $sales_partner->fresh('openingHours');
        $message = 'Der Vertriebspartner wurde erfolgreich erstellt.';

        if ($input['status'] == 'active') {
            $message = 'Der Vertriebspartner wurde erfolgreich erstellt und aktiviert.';
        }
        if ($input['status'] == 'new') {
            $message = "Der Vertriebspartner wurde erfolgreich erstellt.";
        }

        if ($input['status'] == 'active' && $contract) {
            $message = "Der Vertriebspartner wurde erfolgreich erstellt und aktiviert sowie der entsprechende Vertrag ebenfalls erfolgreich erstellt.";
        }

        if ($input['status'] == 'new' && $contract) {
            $message = "Der Vertriebspartner und der entsprechende Vertrag wurden erfolgreich erstellt.";
        }


        return redirect()->route('sales-partner.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesPartner  $sales_partner
     * @return \Illuminate\Http\Response
     */
    public function show(SalesPartner $sales_partner)
    {
        $sales_partner->load('openingHours', 'branch', 'branchCategory', 'consultant', 'contracts.package', 'contracts.marketingFee');
        $sales_partner->append(['profile_picture_url', 'receipt_template_url', 'current_contract']);
        $sales_partner->consultant = null;
        return Inertia::render('SalesPartner/Views/SalesPartner/Show', ['sales_partner' => $sales_partner]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\SalesPartner  $sales_partner
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, SalesPartner $sales_partner)
    {
        $sales_partner->load(['openingHours', 'contracts.package', 'contracts.marketingFee', 'consultant']);
        $sales_partner->append(['profile_picture_url', 'receipt_template_url']);
        $sales_partner->contract = isset($request->session()->get('data')['sales_partner']) ? $request->session()->get('data')['sales_partner']['contract'] : $sales_partner->current_contract;

        $branch = Branch::all();
        $branch_categories = BranchCategory::all();
        $company_consultants = User::isConsultant()->where('status', 'active')->get();

        return Inertia::render('SalesPartner/Views/SalesPartner/Edit', ['sales_partner' => $sales_partner, 'branch' => $branch, 'branch_categories' => $branch_categories, 'company_consultants' => $company_consultants,]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesPartner  $sales_partner
     * @return \Illuminate\Http\Response
     */
    public function update(SalesPartnerUpdateRequest $request, SalesPartner $sales_partner)
    {
        $input = $request->validated();
        $opening_hours = isset($input['opening_hours']) && !$input['no_information'] ? $input['opening_hours'] : [];
        $contract = isset($input['contract']) && $input['contract'] ? $input['contract'] : null;
        unset($input['contract']);
        unset($input['opening_hours']);
        $input['challo_mates_admin_id'] = auth()->id();

        $input['profile_picture'] = $request->file('profile_picture')?->store('sales_partner/profile_picture', env('FILESYSTEM_DISK', 'public')) ?: $sales_partner->profile_picture;
        $input['receipt_template'] = $request->file('receipt_template')?->store('sales_partner/receipt_template', env('FILESYSTEM_DISK', 'public')) ?: $sales_partner->receipt_template;
        $input['receipt_template_name'] = $request->file('receipt_template')?->getClientOriginalName() ?: $sales_partner->receipt_template_name;

        if (gettype($request->profile_picture) == 'string' && Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($request->profile_picture)) {
            $name = Str::random(15) . time() . substr($request->profile_picture, strripos($request->profile_picture, '.'));
            $path = 'sales_partner/profile_picture';
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->move($request->profile_picture, "$path/$name");
            $input['profile_picture'] = "$path/$name";
        }

        if (gettype($request->receipt_template) == 'string' && Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($request->receipt_template)) {
            $name = Str::random(15) . time() . substr($request->receipt_template, strripos($request->receipt_template, '.'));
            $path = 'sales_partner/receipt_template';
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->move($request->receipt_template, "$path/$name");
            $input['receipt_template'] = "$path/$name";
            $input['receipt_template_name'] = $request->receipt_template_name;
        }

        if ($sales_partner->receipt_template && $request->receipt_template)
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($sales_partner->receipt_template);

        if ($sales_partner->profile_picture && $request->profile_picture)
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($sales_partner->profile_picture);

        if ($contract)
            Contract::where('id', $contract['id'])->update($contract);

        $sales_partner->update($input + ['coordinates' => new Point($input['latitude'], $input['longitude'])]);

        $opening_hours = array_map(fn ($item) => [...$item, 'sales_partner_id' => $sales_partner->id, 'created_at' => now(), 'updated_at' => now()], $opening_hours);
        $sales_partner->openingHours()->delete();
        SalesPartnerOpeningHours::insert($opening_hours);

        $sales_partner = $sales_partner->fresh();
        $message = "Der Vertriebspartner \"$sales_partner->company_name\" wurde erfolgreich aktualisiert.";

        return redirect()->route('sales-partner.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesPartner  $sales_partner
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesPartner $sales_partner)
    {
        //
    }

    /**
     * Get Filterable Dynamic Data
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function getFilterableData(Request $request)
    {
        $sales_partners = SalesPartner::with(['branchCategory', 'consultant', 'openingHours', 'branch'])
            ->search($request->get('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->companyConsultantIn($request->company_consultant)
            ->branchIn($request->branches)
            ->branchCategoryIn($request->branch_catagories)
            ->cityIn($request->cities)
            ->statusIn($request->status)
            ->sortByColumns($request->order_by, $request->direction);

        if ($request->column == 'status') {
            return $sales_partners->get()->map(fn ($item) => ['name' => ucfirst($item->status), 'value' => $item->status])->unique()->values();
        }

        if ($request->column == 'company_consultant') {
            return $sales_partners->get()->map(fn ($item) => ['name' => $item->consultant->first_name . " " . $item->consultant->last_name, 'id' => $item->consultant_id])->unique('id')->values();
        }

        if ($request->column == 'branches') {
            return $sales_partners->get()->map(fn ($item) => ['id' => $item->branch_id, 'name' => $item->branch->name])->unique('id')->values();
        }

        if ($request->column == 'branch_catagories') {
            return $sales_partners->get()->map(fn ($item) => ['id' => $item->branch_category_id, 'name' => $item->branchCategory->name])->unique('id')->values();
        }

        if ($request->column == 'cities') {
            return $sales_partners->get()->map(fn ($item) => ['id' => $item->city, 'name' => $item->city])->unique('id')->values();
        }

        if ($request->column == 'satus') {
            return $sales_partners->get()->map(fn ($item) => ['value' => $item->status, 'name' => strtolower($item->status)])->unique('id')->values();
        }
    }

    /**
     * Mass Assign consultant to slaes partner
     *
     */

    public function assignConsultant(Request $request)
    {
        $request->validate([
            'sales_partners' => 'required|array',
            'consultant' => 'required|integer|exists:users,id',
        ]);

        $consultant = User::isConsultant()->where('id', $request->consultant)->first();

        SalesPartner::whereIn('id', $request->sales_partners)->update([
            'consultant_id' => $request->consultant,
        ]);

        return redirect()->back()->with('success', "Der Company Consultant \"$consultant->name\" wurde allen von Ihnen selektierten Vertriebspartnern erfolgreich zugeteilt.");
    }

    /**
     * Update SalesPartner Status
     *
     */

    public function updateStatus(Request $request, SalesPartner $sales_partner)
    {
        $request->validate([
            'status' => "in:active,inactive"
        ]);

        $sales_partner->update(['status' => $request->status]);
        $message = $sales_partner->fresh()->status == 'active' ? "Der Vertriebspartner \"$sales_partner->company_name\" wurde erfolgreich aktiviert." : "Der Vertriebspartner \"$sales_partner->company_name\" wurde erfolgreich deaktiviert.";
        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete Profile Picture of sales partner
     *
     */
    public function deleteProfilePicture(SalesPartner $sales_partner)
    {
        Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($sales_partner->profile_picture);
        $sales_partner->update(['profile_picture' => null]);
        return response('', 204);
    }

    /**
     * Create Sales Partner Session And Redirect to route Edit|Create Contract
     *
     */

    public function setSalesPartnerSessionAndRedirect(Request $request, $to_edit = 0)
    {
        $request->validate(['to_edit' => 'boolean']);
        $input = $request->all();
        $input['receipt_template'] = $request->file('receipt_template')?->store('temp', env('FILESYSTEM_DISK', 'public')) ?? $request->receipt_template;
        $input['profile_picture'] = $request->file('profile_picture')?->store('temp', env('FILESYSTEM_DISK', 'public')) ?? $request->profile_picture;
        if ($input['receipt_template']) {
            $input['receipt_template_name'] = $request->file('receipt_template')?->getClientOriginalName() ?? $input['receipt_template_name'] ?? $request->receipt_template;
        }
        if ($input['profile_picture'] && Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($input['profile_picture'])) {

            // $input['temp_profile_picture_url'] = Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($input['profile_picture']);

            if (config('filesystems.default')  == 'exoscale' ||  env('FILESYSTEM_DISK') == 'exoscale') {
                $input['temp_profile_picture_url'] = Storage::disk(env('FILESYSTEM_DISK', 'public'))->publicUrl($input['profile_picture']);
            } else {
                $input['temp_profile_picture_url'] = Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($input['profile_picture']);
            }
        } else {
            $input['temp_profile_picture_url'] = $input['profile_picture'];
        }
        return $to_edit && isset($input['contract']['id']) ? redirect()->route('contract.edit', ['contract' => $input['contract']['id']])->with('data', ['sales_partner' => $input]) :  redirect()->route('contract.create')->with('data', ['sales_partner' => $input]);
    }


    public function emailQrCode(Request $request)
    {
        $request->validate([
            'image' =>  ['required'],
            'sales_partner_id'      =>  ['required']
        ]);

        $image = str_replace('data:image/png;base64,', '', $request->image);
        $image = str_replace(' ', '+', $image);


        $file_name = sprintf('qr/%s.png', uniqid());

        $file = Storage::disk(env('FILESYSTEM_DISK', 'public'))->put($file_name, base64_decode($image));



        $sales_partner = SalesPartner::where('id', $request->sales_partner_id)->firstOrFail();

        Mail::to($sales_partner->contact_person_email)->send(new SendQrCodeEmail($sales_partner, $file_name));
        // Mail::to($sales_partner->contact_person_email)->send(new SendQrCodeEmail($sales_partner, $image));
        return $file_name;
    }
}
