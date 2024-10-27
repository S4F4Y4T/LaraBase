<?php

namespace App\Actions\V1\Authenctications;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Mockery\Exception;

class ResetPassword
{
    /**
     * Create a new class instance.
     */
    public function __invoke($data)
    {

        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );


        return $status == Password::PASSWORD_RESET
            ? __($status)
            : throw new Exception(message: __($status), code: 400);
    }
}
