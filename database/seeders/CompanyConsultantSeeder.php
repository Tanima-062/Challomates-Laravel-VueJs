<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CompanyConsultantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->state(
            new Sequence(
                ['status'  => User::STATUS_ACTIVE],
                ['status'  => User::STATUS_INACTIVE]
            )
        )->create(
            [
                'type' => 'company_consultant',
            ]
        );
    }
}
