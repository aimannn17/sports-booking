<?php

namespace App\Actions\Facilities;

use App\Models\FacilityType;

/**
 * Creates or renames a facility type (FR-A1).
 */
class SaveFacilityType
{
    /**
     * @param  array{name: string}  $data
     */
    public function handle(array $data, ?FacilityType $type = null): FacilityType
    {
        $type ??= new FacilityType;
        $type->fill(['name' => trim($data['name'])])->save();

        return $type;
    }
}
