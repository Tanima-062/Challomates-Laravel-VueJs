<?php

namespace App\Http\Controllers\Web\Coins;

use App\Http\Controllers\Controller;
use App\Models\Participation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoinsDebitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $debits = Participation::with([
            'mobileAppUser',
            'sweepstake' => function ($q) {
                return $q->selectRaw("sweepstakes.*, CASE WHEN  sweepstakes.type = 'sweepstake' THEN 'Jackpot- und Zusatz-Gewinnspiel Teilnahme' ELSE 'Jackpot-Gewinnspiel-Teilnahme' END AS description");
            }
        ])
            ->selectRaw('participations.*, ((SELECT COUNT(*) FROM participations as self_participations where self_participations.sweepstake_id = participations.sweepstake_id AND participations.mobile_app_user_id = self_participations.mobile_app_user_id) * (SELECT sweepstakes.number_of_coins_for_participation FROM sweepstakes WHERE sweepstakes.id = participations.sweepstake_id)) as total_coins')
            ->Join('sweepstakes', 'sweepstakes.id', '=', 'participations.sweepstake_id')
            ->search(request('query'))
            ->sweepstakeIn($request->sweepstakes)
            ->mobileAppUserIs($request->mobile_app_users)
            ->participatedAt($request->start_date, $request->end_date)
            ->sortByColumns($request->order_by, $request->direction)
            ->paginate($request->per_page ?? 25)->withQueryString();
        return Inertia::render('Coins/Views/Debit/Index', ['debits' => $debits]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Participation $coins_debit)
    {
        $coins_debit->load([
            'mobileAppUser',
            'sweepstake' => function ($q) {
                return $q->selectRaw("sweepstakes.*, CASE WHEN  sweepstakes.type = 'sweepstake' THEN 'Jackpot- und Zusatz-Gewinnspiel Teilnahme' ELSE 'Jackpot-Gewinnspiel-Teilnahme' END AS description");
            }
        ]);
        $other_related_debits = Participation::with('sweepstake')->where('mobile_app_user_id', $coins_debit->mobile_app_user_id)->where('sweepstake_id', $coins_debit->sweepstake_id)->get();
        $coins_debit->total_coins = $other_related_debits->sum(fn ($debit) => $debit->sweepstake->number_of_coins_for_participation);
        $coins_debit->total_participations = $other_related_debits->count();
        return Inertia::render('Coins/Views/Debit/Show', ['debit' => $coins_debit]);
    }

    /**
     * Get All dynamic filterable data
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Facades\Response
     */

    public function getFilterableData(Request $request)
    {
        $debits = Participation::with(['sweepstake', 'mobileAppUser'])
            ->selectRaw('participations.*, SUM(sweepstakes.number_of_coins_for_participation) over (partition by sweepstake_id) as total_coins')
            ->Join('sweepstakes', 'sweepstakes.id', '=', 'participations.sweepstake_id')
            ->search(request('query'))
            ->sweepstakeIn($request->sweepstakes)
            ->mobileAppUserIs($request->mobile_app_users)
            ->participatedAt($request->start_date, $request->end_date)
            ->sortByColumns($request->order_by, $request->direction);

        if ($request->column == 'mobile_app_users') {
            return $debits->get()->pluck('mobileAppUser')->unique('id')->values()->map(fn ($item) => ['name' => "$item->first_name $item->last_name", 'value' => $item->id]);
        }

        if ($request->column == 'sweepstakes') {
            return $debits->get()->pluck('sweepstake')->unique('id')->values()->map(fn ($item) => ['name' => $item->name, 'value' => $item->id]);
        }
        return response([]);
    }
}
