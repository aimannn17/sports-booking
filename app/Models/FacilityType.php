<?php

namespace App\Models;

use Database\Factories\FacilityTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class FacilityType extends Model
{
    /** @use HasFactory<FacilityTypeFactory> */
    use HasFactory;

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
