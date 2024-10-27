<?php

namespace App\Traits\V1;

use Spatie\Activitylog\Models\Activity;

trait ActivityLogTrait
{
    public function getActivity($resource): array
    {
        $updatedActivity = Activity::forSubject($resource)
            ->latest()
            ->first();

        return [
            'updated_by' => $updatedActivity ? $updatedActivity->causer->name : null,
        ];
    }
}
