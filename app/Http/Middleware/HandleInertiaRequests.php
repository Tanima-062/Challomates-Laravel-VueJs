<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth_user'     =>  auth()->check() ? auth()->user()->load('language')->append('avatar_url') : [],
            'languages'     =>  Language::all(),
            /*'permissions'   => [
                'challo_mates_admin.view',
                'challo_mates_admin.create'
            ],*/
            'permissions'   => auth()->check() ? auth()->user()->getAllPermissions() : [],
            'flash'       => [
                'message' => fn () => $request->session()->pull('success'),
                'data' => fn() => $request->session()->pull('data')
            ],

            'prev_url'	=> function() {
                if (url()->previous() !== route('login') && url()->previous() !== '' && url()->previous() !== url()->current()) {
                    return url()->previous();
                }else {
                    return null; // used in javascript to disable back button behavior
                }
            },
        ]);
    }
}
