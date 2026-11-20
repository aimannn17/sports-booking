<?php

namespace App\Livewire\Forms;

use App\Models\Facility;
use Livewire\Form;

class FacilityForm extends Form
{
    public ?Facility $facility = null;

    public ?int $facility_type_id = null;

    public string $name = '';

    public string $location = '';

    public string $external_fee = '0.00';

    public bool $is_active = true;

    public string $description = '';

    protected function rules(): array
    {
        return [
            'facility_type_id' => ['required', 'integer', 'exists:facility_types,id'],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'external_fee' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function validationAttributes(): array
    {
        return ['facility_type_id' => 'facility type', 'external_fee' => 'external fee'];
    }

    public function setFacility(Facility $facility): void
    {
        $this->facility = $facility;
        $this->facility_type_id = $facility->facility_type_id;
        $this->name = $facility->name;
        $this->location = (string) $facility->location;
        $this->external_fee = (string) $facility->external_fee;
        $this->is_active = $facility->is_active;
        $this->description = (string) $facility->description;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->validate();
        $data['location'] = $data['location'] ?: null;
        $data['description'] = $data['description'] ?: null;

        return $data;
    }
}
