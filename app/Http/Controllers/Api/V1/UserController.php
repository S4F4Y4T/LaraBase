<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\UserFilter;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserFilter $filters)
    {
        return UserResource::collection(
            User::query()->filter($filters)->paginate(
                $request->get('show', 15)
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new UserResource(User::query()->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Update the user with the validated data
        tap($user)->update($request->validated());


        return self::success(message: "User updated successfully", data: new UserResource($user)

        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
