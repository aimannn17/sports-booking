<?php

namespace App\Http\Requests\Admin;

use App\Models\FacilityType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * HTTP layer for creating a facility type (Topic 8.1):
 * authorize() asks the Policy, rules() checks the data.
 * The controller only runs if both pass.
 */
class StoreFacilityTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FacilityType::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100', 'unique:facility_types,name'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'There is already a facility type with this name.',
        ];
    }
}
