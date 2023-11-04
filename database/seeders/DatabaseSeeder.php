<?php

namespace Database\Seeders;

use App\Models\BoosterType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(LanguageSeeder::class);
       $this->call(UserSeeder::class);
       $this->call(BranchSeeder::class);
       $this->call(BranchCategorySeeder::class);
       $this->call(CompanyConsultantSeeder::class);
       $this->call(PaymentMethodSeeder::class);
       $this->call(PackageSeeder::class);
       $this->call(SweepStakesSeeder::class);
       $this->call(MarketingFeeSeeder::class);
       $this->call(BranchCategorySeeder::class);
       $this->call(SalesPartnerSeeder::class);
       $this->call(ContractSeeder::class);
       $this->call(MobileAppUserSeeder::class);
       $this->call(StoreVisitsSeeder::class);
       $this->call(StorySeeder::class);
       $this->call(BoosterSeeder::class);
       $this->call(BoosterBoosterTypeSeeder::class);
       $this->call(FollowerSeeder::class);
       $this->call(StoreVisitsSeeder::class);
       $this->call(LikeSeeder::class);
       $this->call(CommentSeeder::class);
       $this->call(ParticipationSeeder::class);
       $this->call(RaffleSeeder::class);
       $this->call(RaffleWinnerSeeder::class);
       $this->call(DailyUserCountSeeder::class);
    }
}
