<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\MarketingFee;
use App\Models\MobileAppUser;
use App\Models\SalesPartner;
use App\Models\StoreVisits;
use Illuminate\Database\Eloquent\Factories\Factory;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreVisits>
 */
class StoreVisitsFactory extends Factory
{
    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (StoreVisits $storeVisits) {
            // $followers = $storeVisits->mobileAppUser
            //     ->AcceptedFollowers()
            //     ->UserFollowers()
            //     ->inRandomOrder()
            //     ->limit(mt_rand(1,2))
            //     ->get()
            //     ;


            // foreach($followers as $follower){
            //     StoreVisits::create([
            //         'sales_partner_id'          =>  SalesPartner::inRandomOrder()->first()->id,
            //         'mobile_app_user_id'        =>  $follower->id,
            //         'check_in_time'             =>  now()->subDays(mt_rand(1, 3)),
            //     ]);
            // }
            if (is_null($storeVisits->check_out_time)) {
                $lat = $storeVisits->salesPartner->latitude;
                $lon = $storeVisits->salesPartner->longitude;
                $storeVisits->mobileAppUser->update([
                    'location'  =>  new Point($lat, $lon),
                    'coin'     =>  $storeVisits->mobileAppUser->coin + $storeVisits->coin
                ]);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $mobile_app_user =  MobileAppUser::inRandomOrder()->first();
        $sales_partner = SalesPartner::inRandomOrder()->first()->id;
        $contract_id = Contract::whereHas('salesPartner', fn ($q) => $q->where('sales_partner_id', $sales_partner))->inRandomOrder()->first();
        $turnover = $this->faker->randomFloat(2, 50, 500);

        return [
            'sales_partner_id'          =>  $sales_partner,
            'mobile_app_user_id'        =>  $mobile_app_user->id,
            'check_in_time'             =>  now()->subDays(mt_rand(1, 3)),
            'check_out_time'            =>  null,
            'contract_id'               =>  $contract_id,
            'turnover'                  =>  $turnover,
            'receipt'                   =>  $this->faker->imageUrl(200, 500),
            'coin'                      =>  mt_rand(0, 10),
            'posting_coins'             =>  mt_rand(0, 10),
            'checkout_type'             =>  $this->faker->randomElement(['automatic', 'manual']),
            'sent_time'                 =>  now()
        ];
    }

    public function addFees()
    {
        return $this->state(function ($attributes) {

            $turnover = $attributes['turnover'];
            $mobile_app_user =  MobileAppUser::find($attributes['mobile_app_user_id']);
            $marketing_fee = MarketingFee::find($attributes['contract_id']->marketing_fee_id);

            if ($mobile_app_user->type == 'direct_consumer') {
                $shares = [
                    "senior_partner_share" => $turnover * ($marketing_fee->direct_consumers_senior_partner_share / 100),
                    "challomate_share" => $turnover * ($marketing_fee->direct_consumers_share_fee_challomates / 100),
                    "challomate_marketing_ag_share" => $turnover * ($marketing_fee->direct_consumers_share_challomates_marketing_ag / 100),
                    "jackpot_share" => $turnover * ($marketing_fee->direct_consumers_share_jackpot / 100),
                ];
            } else {
                $shares = [
                    "consultant_share" => $turnover * ($marketing_fee->distribution_consumers_share_of_consultants / 100),
                    "sales_partner_share" => $turnover * ($marketing_fee->distribution_consumers_proportion_of_sales_partners / 100),
                    "challomate_marketing_ag_share" => $turnover * ($marketing_fee->distribution_consumers_share_challomates_marketing_ag / 100),
                    "jackpot_share" => $turnover * ($marketing_fee->distribution_consumers_share_jackpot / 100),
                ];
            }
            return $shares;
        });
    }
}
