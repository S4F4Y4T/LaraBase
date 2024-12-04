<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\V1\Authenctications\RequestForgetPassword;
use App\Actions\V1\Authenctications\ResetPassword as ResetPasswordAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\V1\Authentication\ForgetPassword;
use App\Http\Requests\V1\Authentication\LoginRequest;
use App\Http\Requests\V1\Authentication\ResetPassword;
use App\Http\Resources\V1\UserResource;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthenticationController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        // Attempt to authenticate the user with the provided credentials (email and password)
        if (! $token = auth()->attempt($request->only('email', 'password'))) {
            return self::error('Unauthorized', 401); // Return error if authentication fails
        }

        // Use the helper function to respond with the access token
        return $this->respondWithToken($token)
            ->cookie(
                'refresh_token',         // Cookie name
                auth()->refresh(),                   // Token
                config('jwt.refresh_ttl'), // Expiration time in minutes from config
                '/',                             // Path
                null,                            // Domain (optional)
                true,                            // Secure (HTTPS only)
                true                             // HTTP-only (not accessible via JavaScript)
            );
    }

    public function refresh(Request $request): \Illuminate\Http\JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token'); // Retrieve the refresh token from the cookie

        try {
            return $this->respondWithToken(
                auth()->setToken($refreshToken)->refresh()
            );

        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Refresh token expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid refresh token'], 400);
        }
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
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => UserResource::make(
                    auth()->user()->load('roles.permissions')
                )
            ]);
    }
}

