<?php
namespace App\Http\Controllers\Api\V5;

use App\Filters\V5\UtilityFilter;
use App\Http\Requests\V5\Utility\StoreUtilityRequest;
use App\Http\Requests\V5\Utility\UpdateUtilityRequest;
use App\Http\Resources\V5\UtilityResource;
use App\Models\Utility;

use App\Traits\V1\ApiResponse;
use App\Http\Controllers\Api\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class UtilityController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(UtilityFilter $filters): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->isAuthorized('all', Utility::class);

        return UtilityResource::collection(
            Utility::query()->filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreUtilityRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->isAuthorized('create', Utility::class);

        $utility = Utility::query()->create($request->validated());
        return self::success(message: "Utility created successfully", data: UtilityResource::make($utility));
    }

    /**
     * Display the specified resource.
     */
    public function show(Utility $utility): UtilityResource
    {
        $this->isAuthorized('show', $utility);

        return new UtilityResource($utility);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUtilityRequest $request, Utility $utility): \Illuminate\Http\JsonResponse
    {
        $utility = tap($utility)->update($request->validated());

        return self::success(message: "Utility updated successfully", data: UtilityResource::make($utility));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utility $utility): \Illuminate\Http\JsonResponse
    {
        $this->isAuthorized('delete', $utility);

        $utility->delete();

        return self::success(message: "Utility deleted successfully");
    }
}
