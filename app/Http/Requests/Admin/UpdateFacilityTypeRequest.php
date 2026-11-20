<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * HTTP layer for renaming a facility type. Same rules as creating,
 * except the unique check ignores the type being edited.
 */
class UpdateFacilityTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('facility_type'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:3', 'max:100',
                Rule::unique('facility_types', 'name')->ignore($this->route('facility_type')),
            ],
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
