<?php

namespace App\Actions\Facilities;

use App\Models\FacilityType;
use Illuminate\Validation\ValidationException;

/**
 * Deletes a facility type that no facility uses (FR-A1). Mirrors the onDelete restrict rule.
 */
class DeleteFacilityType
{
    public function handle(FacilityType $type): void
    {
        if ($type->facilities()->exists()) {
            throw ValidationException::withMessages(['type' => "\"{$type->name}\" is used by a facility, so it cannot be deleted."]);
        }

        $type->delete();
    }
}
