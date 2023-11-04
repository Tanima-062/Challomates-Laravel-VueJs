<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create([
            'type'  =>  'system_admin',
            'email'  =>  'admin@mail.com',
            "language_id"   =>  2,
            'status'    =>  User::STATUS_ACTIVE
        ]);

        // $user->assignRole('system_admin');

        \App\Models\User::factory()->create([
            'type'  =>  User::CHALLO_MATES_ADMIN,
            'email'  =>  'challomatesadmin@mail.com',
            "language_id"   =>  2,
            'status'    =>  User::STATUS_ACTIVE
        ]);

        \App\Models\User::factory()->create([
            'type'  =>  User::CHALLO_MATES_ADMIN,
            'email'  =>  'faton.nuhiji@challomates.ch',
            "language_id"   =>  2,
            'status'    =>  User::STATUS_ACTIVE
        ]);


        // \App\Models\User::factory(50)->create();
    }
}
