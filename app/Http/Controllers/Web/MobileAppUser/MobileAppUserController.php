<?php

namespace App\Http\Controllers\Web\MobileAppUser;

use App\Http\Controllers\Controller;
use App\Models\MobileAppUser;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MobileAppUserController extends Controller
{
    use UploadAble;


    public function __construct()
    {
        $this->middleware('permission:mobile_app_user.view')->only(['index', 'show']);
        $this->middleware('permission:mobile_app_user.edit')->only(['toggleStatus']);
        $this->middleware('permission:mobile_app_user.delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mobile_app_users = MobileAppUser::query()
            ->search(request('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->statusIn($request->status)
            ->sortByOwnTableColumns($request->order_by, $request->direction);

        $mobile_app_users = $mobile_app_users->paginate($request->per_page ?? 25)->withQueryString();
        $mobile_app_users->each(fn($item) => $item->append('photo_url'));
        // return $mobile_app_users;
        return Inertia::render('MobileAppUser/Index', [
            'mobile_app_users' => $mobile_app_users
        ]);
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
     *  @param  \Illuminate\Http\Request $request
     *  @param App\Model\MobileAppUser $mobile_app_user
     *  @return Inertia\Inertia
     */
    public function show(Request $request, MobileAppUser $mobile_app_user)
    {
        $mobile_app_user->append('photo_url');
        return Inertia::render('MobileAppUser/Show', [
            'mobile_app_user' => $mobile_app_user
        ]);
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
     * @param \App\Models\MobileAppUser
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function destroy(MobileAppUser $mobile_app_user)
    {
        $this->deleteOne($mobile_app_user->photo, env('FILESYSTEM_DISK', 'public'));
        $mobile_app_user->forceDelete();
        // return redirect()->back()->with('success', "Der Mobile App-Benutzer $mobile_app_user->first_name $mobile_app_user->last_name wurde erfolgreich gelöscht.",);
        return redirect()->back()->with('success', sprintf('Der Mobile App-Benutzer "%s %s" wurde erfolgreich gelöscht.', $mobile_app_user->first_name, $mobile_app_user->last_name));
    }

    /**
     * Update User Status
     * @param Request $request
     * @param MobileAppUser $mobile_app_user
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function toggleStatus(Request $request, MobileAppUser $mobile_app_user)
    {
        $mobile_app_user->status = $mobile_app_user->status == MobileAppUser::STATUS_INACTIVE ? MobileAppUser::STATUS_ACTIVE : MobileAppUser::STATUS_INACTIVE;
        $mobile_app_user->save();

        $status_text = $mobile_app_user->status == MobileAppUser::STATUS_ACTIVE ? "aktiviert" : "deaktiviert";
        $message = "Der Mobile App-Benutzer \"$mobile_app_user->first_name $mobile_app_user->last_name\" wurde erfolgreich $status_text.";

        if ($request->status !== MobileAppUser::STATUS_ACTIVE) {
            $mobile_app_user->tokens()->delete();
        }

        return back()->with('success', $message);
    }

    /**
     * Get Filterable Data
     * @param Request $request
     * @return Illuminate\Support\Facades\Response
     *
     */

    public function getFilterableData(Request $request)
    {
        $mobile_app_users = MobileAppUser::query()
            ->search(request('query'))
            ->createdAtBetween($request->start_date, $request->end_date)
            ->statusIn($request->status)
            ->sortByOwnTableColumns($request->order_by, $request->direction);
        if ($request->column == 'status') {
            return $mobile_app_users->select('status')->get()->map(fn ($item) => ['name' =>  $item->status == 'pending' ? 'Registration pending' : ucfirst($item->status), 'value' => $item->status])->unique()->values();
        }
    }
}
