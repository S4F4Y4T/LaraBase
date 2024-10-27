<?php

namespace App\Actions\V1\Authenctications;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Mockery\Exception;

class RequestForgetPassword
{
    /**
     * Create a new class instance.
     */
    public function __invoke($validated)
    {

        $status = Password::sendResetLink($validated, function ($user, $token) {
            // Construct the reset password URL using the v1 route
            $resetUrl = URL::to('/api/v1/auth/reset-password?token=' . $token . '&email=' . urlencode($user->email));
            $user->notify(new \App\Notifications\ResetPassword($resetUrl));
        });


        return $status == Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : throw new Exception(message: 'Password reset link could not be sent.', code: 400);
    }
}
