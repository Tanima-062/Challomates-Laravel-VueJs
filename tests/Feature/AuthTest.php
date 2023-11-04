<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_login()
    {
        $user = User::factory()->create([
            'status'    =>  'active',
            'type'  =>  'system_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

    }

    public function test_challaomates_admin_can_login()
    {
        $user = User::factory()->create([
            'status'    =>  'active',
            'type'  =>  'challo_mates_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_only_active_user_can_login()
    {
        $user = User::factory()->create([
            'status'    =>  'inactive',
            'type'  =>  'challo_mates_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;
        $response->assertRedirect('/');
        $this->assertGuest();

        $user = User::factory()->create([
            'status'    =>  'new',
            'type'  =>  'challo_mates_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;
        $response->assertRedirect('/');
        $this->assertGuest();


        $user = User::factory()->create([
            'status'    =>  'pending',
            'type'  =>  'challo_mates_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;
        $response->assertRedirect('/');
        $this->assertGuest();
    }


    public function test_user_can_logged_out()
    {
        $user = User::factory()->create([
            'status'    =>  'active',
            'type'  =>  'challo_mates_admin'
        ]);

        $response = $this->post(route('login'), [
            'username'     =>  $user->email,
            'password'     =>  'password',
        ])
        ;

        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $response = $this->get(route('dashboard'))
            ->assertSee('Dashboard');

       $this->post(route('logout'));

       $response = $this->get(route('dashboard'))
            ->assertStatus(302)
            ->assertDontSee('Dashboard')
            ->assertRedirect(route('login'));
    }
}
