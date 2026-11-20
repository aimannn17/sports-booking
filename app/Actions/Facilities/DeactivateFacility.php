<?php

namespace App\Actions\Facilities;

use App\Models\Facility;

/**
 * "Deletes" a facility by deactivating it, so its booking history is kept (FR-A2).
 * Existing bookings stay; no new bookings can be made.
 */
class DeactivateFacility
{
    public function handle(Facility $facility): Facility
    {
        $facility->update(['is_active' => false]);

        return $facility;
    }
}
