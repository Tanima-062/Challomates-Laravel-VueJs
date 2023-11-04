<?php

namespace App\Jobs;

use App\Models\DailyUsersCount;
use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChartDataSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesPartnerConsumerCountBySalesPartner = SalesPartner::select('id', 'consultant_id')
            ->withCount(['mobileAppUsers' => fn ($q) => $q->where('type', MobileAppUser::DISTRIBUTION_CONSUMER)->where('status', MobileAppUser::STATUS_ACTIVE)])
            ->get()
            ->map(fn ($item) => ['mobile_app_users_count' => $item['mobile_app_users_count'],'type' => MobileAppUser::DISTRIBUTION_CONSUMER, 'sales_partner_id' => $item['id'], 'consultant_id' => $item['consultant_id'], 'created_at' => Carbon::now()])
            ->toArray();

        $directConsumerCountBySalesPartner = SalesPartner::select('id', 'consultant_id')
            ->withCount(['mobileAppUsers' => fn ($q) => $q->where('type', MobileAppUser::DIRECT_CONSUMER)->where('status', MobileAppUser::STATUS_ACTIVE)])
            ->get()
            ->map(fn ($item) => ['mobile_app_users_count' => $item['mobile_app_users_count'],'type' => MobileAppUser::DIRECT_CONSUMER, 'sales_partner_id' => $item['id'], 'consultant_id' => $item['consultant_id'], 'created_at' => Carbon::now()])
            ->toArray();

        $salesPartnerConsumerCountWithoutSalesPartner = MobileAppUser::where('type', MobileAppUser::DISTRIBUTION_CONSUMER)->whereNull('sales_partner_id')->where('status', MobileAppUser::STATUS_ACTIVE)->count();
        $directConsumerCountWithotSalesPartner = MobileAppUser::where('type', MobileAppUser::DIRECT_CONSUMER)->whereNull('sales_partner_id')->where('status', MobileAppUser::STATUS_ACTIVE)->count();

        DailyUsersCount::insert($salesPartnerConsumerCountBySalesPartner);
        DailyUsersCount::insert($directConsumerCountBySalesPartner);
        DailyUsersCount::insert(['mobile_app_users_count' => $salesPartnerConsumerCountWithoutSalesPartner, 'type' => MobileAppUser::DISTRIBUTION_CONSUMER, 'created_at' => Carbon::now()]);
        DailyUsersCount::insert(['mobile_app_users_count' => $directConsumerCountWithotSalesPartner,'type' => MobileAppUser::DIRECT_CONSUMER, 'created_at' => Carbon::now()]);
    }
}
