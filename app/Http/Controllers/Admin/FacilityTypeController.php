<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Facilities\DeleteFacilityType;
use App\Actions\Facilities\SaveFacilityType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacilityTypeRequest;
use App\Http\Requests\Admin\UpdateFacilityTypeRequest;
use App\Models\FacilityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * FR-A1: manage facility types with the 7 RESTful actions (Topic 6).
 *
 * A "skinny controller" (Topic 8.1): the Form Requests validate and authorize,
 * the Action classes do the database work, and this class only orchestrates
 * and returns HTTP responses. The same Actions are used by the seeders and tests.
 */
class FacilityTypeController extends Controller
{
    /** GET /admin/facility-types */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', FacilityType::class);

        $types = FacilityType::query()
            ->withCount('facilities')
            ->when($request->query('search'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.facility-types.index', compact('types'));
    }

    /** GET /admin/facility-types/create */
    public function create(): View
    {
        Gate::authorize('create', FacilityType::class);

        return view('admin.facility-types.create', ['type' => new FacilityType]);
    }

    /** POST /admin/facility-types */
    public function store(StoreFacilityTypeRequest $request, SaveFacilityType $save): RedirectResponse
    {
        $type = $save->handle($request->validated());

        return redirect()
            ->route('admin.facility-types.index')
            ->with('status', "Facility type \"{$type->name}\" added.");
    }

    /** GET /admin/facility-types/{facility_type} */
    public function show(FacilityType $facilityType): View
    {
        Gate::authorize('viewAny', FacilityType::class);

        $facilityType->load(['facilities' => fn ($q) => $q->orderBy('name')]);

        return view('admin.facility-types.show', ['type' => $facilityType]);
    }

    /** GET /admin/facility-types/{facility_type}/edit */
    public function edit(FacilityType $facilityType): View
    {
        Gate::authorize('update', $facilityType);

        return view('admin.facility-types.edit', ['type' => $facilityType]);
    }

    /** PUT/PATCH /admin/facility-types/{facility_type} */
    public function update(UpdateFacilityTypeRequest $request, FacilityType $facilityType, SaveFacilityType $save): RedirectResponse
    {
        $save->handle($request->validated(), $facilityType);

        return redirect()
            ->route('admin.facility-types.index')
            ->with('status', 'Facility type updated.');
    }

    /** DELETE /admin/facility-types/{facility_type} */
    public function destroy(FacilityType $facilityType, DeleteFacilityType $delete): RedirectResponse
    {
        Gate::authorize('delete', $facilityType);

        // Throws a ValidationException if a facility still uses this type;
        // Laravel then redirects back with the error, like a failed form.
        $delete->handle($facilityType);

        return redirect()
            ->route('admin.facility-types.index')
            ->with('status', 'Facility type deleted.');
    }
}
