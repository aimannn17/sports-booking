<?php

namespace App\Actions\Facilities;

use App\Models\Facility;

/**
 * Creates or updates a facility (FR-A2).
 */
class SaveFacility
{
    /**
     * @param  array{facility_type_id: int, name: string, location?: string|null, external_fee: float|string, is_active: bool, description?: string|null}  $data
     */
    public function handle(array $data, ?Facility $facility = null): Facility
    {
        $facility ??= new Facility;
        $facility->fill($data)->save();

        return $facility;
    }
}
