<?php

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DailyUsersCount;
use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use App\Models\StoreVisits;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function index(Request $request)
    {
        $UserChart = $this->getUserChartData($request);
        $TurnOverChart = $this->getTurnOverChart($request);
        $SalesPartnerConsumerChart = $this->getSalesPartnerConsumerChart($request);
        $DirectConsumerChart = $this->getDirectConsumerShareChart($request);
        return Inertia::render('Dashboard/Dashboard', compact('UserChart', 'TurnOverChart', 'DirectConsumerChart', 'SalesPartnerConsumerChart'));
    }


    public function getFilterableData(Request $request)
    {
        $column = $request->column;

        // $query = DailyUsersCount::query()
        //     ->createdAtBetween($request->start_date, $request->end_date, $request->date_selection)
        //     ->salesPartnersIn($request->sales_partners)
        //     ->consultantsIn($request->company_consultant);

        if ($column == 'sales_partners') {
            $sales_partners = SalesPartner::whereIn('status', [SalesPartner::STATUS_ACTIVE, SalesPartner::STATUS_INACTIVE])
                ->select('status', 'id as value', 'company_name as name')
                ->get()
                ->toArray();

            return $sales_partners;
        }

        if ($column == 'company_consultant') {
            $consultants = User::whereIn('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])
                ->select("status", "id as value", "first_name", "last_name")
                ->isConsultant()
                ->whereIn('status', [User::STATUS_ACTIVE, User::STATUS_INACTIVE])
                ->get()
                ->toArray();
            return $consultants;
        }
        return [];
    }

    private function getUserChartData(Request $request)
    {
        $query = DailyUsersCount::query()
            ->salesPartnersIn($request->sales_partners)
            ->consultantsIn($request->company_consultant)
            ->createdAtBetween($request->start_date, $request->end_date, $request->date_selection);

        $directConsumerCount = clone $query;
        $directConsumerCount = $directConsumerCount->selectRaw('SUM(mobile_app_users_count) as mobile_app_users_count')->where('type', MobileAppUser::DIRECT_CONSUMER)->groupBy('created_at')->get()->max()?->mobile_app_users_count;

        $salesPartnerConsumerCount = clone $query;
        $salesPartnerConsumerCount = $salesPartnerConsumerCount->selectRaw('SUM(mobile_app_users_count) as mobile_app_users_count')->where('type', MobileAppUser::DISTRIBUTION_CONSUMER)->groupBy('created_at')->get()->max()?->mobile_app_users_count;

        $total = $directConsumerCount + $salesPartnerConsumerCount;
        return ['data' => [$directConsumerCount, $salesPartnerConsumerCount], 'labels' => ['Direkte Konsumenten', 'Vertriebskonsument'], 'total' => $total];
    }

    private function getTurnOverChart(Request $request)
    {
        $query = StoreVisits::query()
            ->salesPartnerIn($request->sales_partners)
            ->consultantIn($request->company_consultant)
            ->mobileAppUserWithCondition($request->mobile_app_users)
            ->createdAtBetween($request->start_date, $request->end_date, $request->date_selection)
            ->whereNotNull('check_out_time');

        $directConsumerTurnoverCount = clone $query;
        $directConsumerTurnoverCount = $directConsumerTurnoverCount->whereHas('mobileAppUser', fn ($q) => $q->where('type', MobileAppUser::DIRECT_CONSUMER))->sum('turnover');

        $salesPartnerConsumerTurnoverCount = clone $query;
        $salesPartnerConsumerTurnoverCount = $salesPartnerConsumerTurnoverCount->whereHas('mobileAppUser', fn ($q) => $q->where('type', MobileAppUser::DISTRIBUTION_CONSUMER))->sum('turnover');

        $total = "CHF " . number_format($directConsumerTurnoverCount + $salesPartnerConsumerTurnoverCount, 2, ".", "");
        return ['data' => [$directConsumerTurnoverCount, $salesPartnerConsumerTurnoverCount], 'labels' => ['Direkte Konsumenten', 'Vertriebskonsument'], 'total' => $total];
    }

    private function getDirectConsumerShareChart(Request $request)
    {
        $query = StoreVisits::query()
            ->salesPartnerIn($request->sales_partners)
            ->consultantIn($request->company_consultant)
            ->mobileAppUserWithCondition($request->mobile_app_users)
            ->createdAtBetween($request->start_date, $request->end_date, $request->date_selection)
            ->whereHas('mobileAppUser', fn ($q) => $q->where('type', MobileAppUser::DIRECT_CONSUMER))
            ->whereNotNull('check_out_time');

        $shares = array_values($query->selectRaw("SUM(senior_partner_share) as senior_partner_share, SUM(jackpot_share) as jackpot_share, SUM(challomate_share) as challomate_share, SUM(challomate_marketing_ag_share) as challomate_marketing_ag_share")->get()->each->setHidden(['check_out_prefix_id', 'check_in_prefix_id'])->first()->toArray());
        $total = "CHF " . number_format(array_sum($shares), 2, '.', "");
        return ['data' => $shares, 'labels' => ['Anteil Senior Partner', 'Anteil Jackpot', 'Anteil Gebühr CHalloMates', 'Anteil ChalloMates Marketing AG'], 'total' => $total];
    }

    private function getSalesPartnerConsumerChart(Request $request)
    {
        $query = StoreVisits::query()
            ->salesPartnerIn($request->sales_partners)
            ->consultantIn($request->company_consultant)
            ->mobileAppUserWithCondition($request->mobile_app_users)
            ->createdAtBetween($request->start_date, $request->end_date, $request->date_selection)
            ->whereHas('mobileAppUser', fn ($q) => $q->where('type', MobileAppUser::DISTRIBUTION_CONSUMER))
            ->whereNotNull('check_out_time');

        $shares = array_values($query->selectRaw("SUM(consultant_share) as consultant_share, SUM(jackpot_share) as jackpot_share, SUM(sales_partner_share) as sales_partner_share, SUM(challomate_marketing_ag_share) as challomate_marketing_ag_share")->get()->each->setHidden(['check_out_prefix_id', 'check_in_prefix_id'])->first()->toArray());
        $total = "CHF " . number_format(array_sum($shares), 2, '.', "");
        return ['data' => $shares, 'labels' => ['Anteil Berater', 'Anteil Jackpot', 'Anteil Vertriebspartner', 'Anteil ChalloMates Marketing AG'], 'total' => $total];
    }
}
