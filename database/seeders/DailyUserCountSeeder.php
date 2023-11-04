<?php

namespace Database\Seeders;

use App\Models\DailyUsersCount;
use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyUserCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 30; $i++) {
            $creation_date = Carbon::now()->subDays($i);

            $salesPartnerConsumerCountBySalesPartner = SalesPartner::select('id', 'consultant_id')
                ->whereIn('status', [SalesPartner::STATUS_ACTIVE, SalesPartner::STATUS_INACTIVE])
                ->withCount(['mobileAppUsers' => fn ($q) => $q->where('type', MobileAppUser::DISTRIBUTION_CONSUMER)->where('created_at', '<=', $creation_date)])
                ->get()
                ->map(fn ($item) => ['mobile_app_users_count' => $item['mobile_app_users_count'], 'type' => MobileAppUser::DISTRIBUTION_CONSUMER, 'sales_partner_id' => $item['id'], 'consultant_id' => $item['consultant_id'], 'created_at' => $creation_date])
                ->toArray();

            $directConsumerCountBySalesPartner = SalesPartner::select('id', 'consultant_id')
                ->whereIn('status', [SalesPartner::STATUS_ACTIVE, SalesPartner::STATUS_INACTIVE])
                ->withCount(['mobileAppUsers' => fn ($q) => $q->where('type', MobileAppUser::DIRECT_CONSUMER)->where('created_at', '<=', $creation_date)])
                ->get()
                ->map(fn ($item) => ['mobile_app_users_count' => $item['mobile_app_users_count'], 'type' => MobileAppUser::DIRECT_CONSUMER, 'sales_partner_id' => $item['id'], 'consultant_id' => $item['consultant_id'], 'created_at' => $creation_date])
                ->toArray();

            $salesPartnerConsumerCountWithoutSalesPartner = MobileAppUser::where('type', MobileAppUser::DISTRIBUTION_CONSUMER)->whereNull('sales_partner_id')->count();
            $directConsumerCountWithotSalesPartner = MobileAppUser::where('type', MobileAppUser::DIRECT_CONSUMER)->whereNull('sales_partner_id')->count();

            DailyUsersCount::insert($salesPartnerConsumerCountBySalesPartner);
            DailyUsersCount::insert($directConsumerCountBySalesPartner);
            DailyUsersCount::insert(['mobile_app_users_count' => $salesPartnerConsumerCountWithoutSalesPartner, 'type' => MobileAppUser::DISTRIBUTION_CONSUMER, 'created_at' => $creation_date]);
            DailyUsersCount::insert(['mobile_app_users_count' => $directConsumerCountWithotSalesPartner, 'type' => MobileAppUser::DIRECT_CONSUMER, 'created_at' => $creation_date]);
        }
    }
}
