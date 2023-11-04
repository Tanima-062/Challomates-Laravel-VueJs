<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChallomatesAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_show_challaomates_admin()
    {
        $this->login();


        $challaomates_admin  = User::factory()->create([
            'type'  =>  'challo_mates_admin'
        ]);

        $this->get(route('challo-mates-admins.index'))
            ->assertSee($challaomates_admin->name)
            ->assertSee($challaomates_admin->email)
            ->assertSee($challaomates_admin->prefix_id)
            ;
    }


    public function test_system_admin_can_crete_challaomates_admin()
    {
        $this->login([
            'type'  =>  'system_admin'
        ]);

        $admin = User::factory()->make([
            'type'  =>  'challo_mates_admin',
            'country_iso_code'  =>  'BD',
            'phone_number'  =>  '01828307777',
            'status'    =>  'pending'
        ]);

        $response = $this->post(route('challo-mates-admins.store'), $admin->toArray());

        $challaomates_admin = User::find(2);

        // $this->assertEquals($admin->country_iso_code, $challaomates_admin->country_iso_code);
        // $this->assertEquals($admin->phone_number, $challaomates_admin->phone_number);
        $this->assertEquals($admin->first_name, $challaomates_admin->first_name);
        $this->assertEquals($admin->last_name, $challaomates_admin->last_name);
        $this->assertEquals($admin->language_id, $challaomates_admin->language_id);
        $this->assertEquals($admin->email, $challaomates_admin->email);
        $this->assertEquals($admin->type, 'challo_mates_admin');
        $this->assertEquals($admin->status, 'pending');

        $this->assertDatabaseCount('users', 2);

    }
}
