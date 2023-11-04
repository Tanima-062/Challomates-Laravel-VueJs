<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

class MatchSamePassword implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $credential = [];

        filter_var(request()->username, FILTER_VALIDATE_EMAIL) ? $credential['email']  = request()->username : $credential['phone_number']  = request()->username;

        $user = User::where($credential)->first();
        if($user){
            return !Hash::check($value, $user->password);
        }

        return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your new password must be different from your current password.';
    }
}
