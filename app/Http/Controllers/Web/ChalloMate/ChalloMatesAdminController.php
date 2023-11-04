<?php

namespace App\Http\Controllers\Web\ChalloMate;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChalloMatesAdmin\ChalloMatesCreateRequest;
use App\Http\Requests\ChalloMatesAdmin\ChalloMatesUpdateRequest;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ChalloMatesAdmin\SendInvitationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Propaganistas\LaravelPhone\PhoneNumber;

class ChalloMatesAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:challo_mates_admin.view')->only(['index', 'show']);
        $this->middleware('permission:challo_mates_admin.create')->only(['create', 'store']);
        $this->middleware('permission:challo_mates_admin.edit')->only(['edit', 'update', 'toggleStatus']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->get('query');
        $order_by = $request->get('order_by', 'created_at');
        $status = $request->get('status', null);
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $direction = strtolower($request->get('direction', 'DESC'));
        $per_page = $request->get('per_page', 25);

        $challoMatesAdmins = User::query()->where('type', 'challo_mates_admin')
            ->where(function ($q) use ($query) {
                if (!is_null($query)) {
                    $q->where('first_name', 'like', "%${query}%")
                        ->orWhere('last_name', 'like', "%${query}%")
                        ->orWhereRaw("CONCAT(`first_name`,' ', `last_name`) LIKE ?", ["%$query%"])
                        ->orWhere('email', 'like', "%${query}%")
                        ->orWhere('phone_number', 'like', "%${query}%")
                        ->orWhere('full_phone_number', 'like', "%${query}%")
                        ->orWhere('prefix_id', 'like', "%${query}%");
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

        //dd($challoMatesAdmins);

        return Inertia::render('ChalloMatesAdmin/Index', [
            'challo_mates_admins' => $challoMatesAdmins
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('ChalloMatesAdmin/Create', [
            'latest_id' => getUserPrefix('challo_mates_admin') . User::getNextPrefixID('challo_mates_admin')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChalloMatesCreateRequest $request)
    {
        //dd('sdsd');
        $message = 'Der CHalloMates Admin wurde erfolgreich erstellt.';

        $user = User::create($request->validated() + ['type' => 'challo_mates_admin']);

        if ($request->invitation) {
            $user->status = User::STATUS_PENDING;
            $user->verification_token = Hash::make(uniqid() . rand(1000, 9000));
            $user->save();
            $user->notify(new SendInvitationNotification($user->verification_token));
            $message = 'Der CHalloMates Admin wurde erfolgreich erstellt und die
            E-Mail Einladung gesendet.';
        }

        return redirect()->route('challo-mates-admins.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $challo_mates_admin)
    {
        //dd($challo_mates_admin);
        return Inertia::render('ChalloMatesAdmin/Edit', [
            'challo_mates_admin' => $challo_mates_admin
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ChalloMatesUpdateRequest $request, User $challo_mates_admin)
    {


        $challo_mates_admin->update($request->safe()->only(['first_name', 'last_name', 'email', 'country_iso_code', 'phone_number']));
        $message = "Der CHalloMates Admin $challo_mates_admin->first_name $challo_mates_admin->last_name wurde erfolgreich aktualisiert.";

        return redirect()->route('challo-mates-admins.index')->with('success', $message);
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

    function toggleStatus(User $challo_mates_admin)
    {
        $challo_mates_admin->status = $challo_mates_admin->status == User::STATUS_INACTIVE ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
        $challo_mates_admin->save();

        $status = ($challo_mates_admin->status == User::STATUS_ACTIVE) ? 'aktiviert' : 'deaktiviert';
        $message = "Der CHalloMates Admin \"$challo_mates_admin->first_name $challo_mates_admin->last_name\" wurde erfolgreich $status.";
        return redirect()->back()->with('success', $message);
    }

    public function resendInvitation(User $challo_mates_admin)
    {
        if ($challo_mates_admin->status == User::STATUS_NEW || $challo_mates_admin->status == User::STATUS_PENDING) {
            $challo_mates_admin->status = User::STATUS_PENDING;
            $challo_mates_admin->verification_token = Hash::make(uniqid() . rand(1000, 9000));
            $challo_mates_admin->save();
            $challo_mates_admin->notify(new SendInvitationNotification($challo_mates_admin->verification_token));
            return redirect()->back()->with('success', "Die E-Mail-Einladung wurde erfolgreich an den ChalloMates Admin $challo_mates_admin->name gesendet.");
        }
    }

    /**
     * Get Filterable Data
     * @param Request $request
     * @return Illuminate\Support\Facades\Response
     *
     */
    public function getFilterableData(Request $request)
    {

        $query = $request->get('query');
        $order_by = $request->get('order_by', 'id');
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $status = $request->status;
        $direction = strtolower($request->get('direction', 'ASC'));

        $challoMatesAdmins = User::query()->where('type', 'challo_mates_admin')
            ->where(function ($q) use ($query) {
                if (!is_null($query)) {
                    $q->where('first_name', 'like', "%${query}%")
                        ->orWhere('last_name', 'like', "%${query}%")
                        ->orWhere('email', 'like', "%${query}%")
                        ->orWhere('phone_number', 'like', "%${query}%")
                        ->orWhere('prefix_id', 'like', "%${query}%");
                }
            })

            ->where(function ($q) use ($start_date, $end_date) {
                if (!is_null($start_date) && !is_null($end_date)) {
                    $q->whereBetween(DB::raw('date_format(created_at, \'%Y-%m-%d\')'), [$start_date, $end_date]);
                }
            })
            ->when($status,  fn ($q) => $q->where('status', $status))
            ->orderBy($order_by, $direction);

        if ($request->column == 'status') {
            return $challoMatesAdmins->select('status')->get()->map(fn ($item) => ['name' =>  $item->status == 'pending' ? 'Registration pending' : ucfirst($item->status), 'value' => $item->status])->unique()->values();
        }
    }
}
