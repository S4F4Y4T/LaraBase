<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\Authenctications\RequestForgetPassword;
use App\Actions\V1\Authenctications\ResetPassword as ResetPasswordAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\V1\Authentication\ForgetPassword;
use App\Http\Requests\V1\Authentication\LoginRequest;
use App\Http\Requests\V1\Authentication\ResetPassword;
use App\Traits\V1\ApiResponse;

class AuthenticationController extends Controller
{
    use ApiResponse;

   public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
   {
       if (! $token = auth()->attempt($request->only('email', 'password'))) {
           self::error('Unauthorized', 401);
       }

       return $this->respondWithToken($token);
   }

   public function forgetPassword(ForgetPassword $request, RequestForgetPassword $action): \Illuminate\Http\JsonResponse
   {
       $action($request->validated());

       return self::success(message: 'Verification code sent. Please check your email.');
   }

   public function resetPassword(ResetPassword $request, ResetPasswordAction $action): \Illuminate\Http\JsonResponse
   {
       $reset = $action($request->only('email', 'password', 'confirm_password', 'token'));

       return self::success(message: (string)$reset);
   }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): \Illuminate\Http\JsonResponse
    {
        return self::success('Data fetched successfully', data: auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();

        return self::success(message: 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return self::success(message: 'authentication successful',
            data:[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
