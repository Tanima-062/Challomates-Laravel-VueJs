<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function login(array $data = [])
    {
        $user = User::factory()->create([
            'status'    =>  'active'
        ] + $data);

        Auth::Login($user);
    }
}
