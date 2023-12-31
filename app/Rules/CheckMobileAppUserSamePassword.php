<?php

namespace App\Rules;

use App\Models\MobileAppUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;

class CheckMobileAppUserSamePassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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

        filter_var(request()->email, FILTER_VALIDATE_EMAIL) ? $credential['email']  = request()->email : $credential['phone_number']  = request()->username;

        $user = MobileAppUser::where($credential)->first();

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
