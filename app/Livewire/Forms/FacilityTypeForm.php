<?php

namespace App\Livewire\Forms;

use App\Models\FacilityType;
use Illuminate\Validation\Rule;
use Livewire\Form;

class FacilityTypeForm extends Form
{
    public ?FacilityType $type = null;

    public string $name = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('facility_types', 'name')->ignore($this->type?->id)],
        ];
    }

    public function setType(FacilityType $type): void
    {
        $this->type = $type;
        $this->name = $type->name;
    }
}
