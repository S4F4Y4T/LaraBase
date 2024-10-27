<?php

namespace App\Http\Resources\V1;

use App\Traits\V1\ActivityLogTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    use ActivityLogTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'activity' => $this->getActivity($this->resource)
        ];
    }
}
