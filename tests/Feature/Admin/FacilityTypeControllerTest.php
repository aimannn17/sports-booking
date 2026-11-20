<?php

use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\User;

/*
 * Feature tests for the classic Resource Controller (Topics 6 and 8.3):
 * each test sends a real HTTP request through route -> Form Request -> controller -> Action.
 */

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('index lists the types with how many facilities use each', function () {
    $type = FacilityType::factory()->create(['name' => 'Badminton']);
    Facility::factory()->count(2)->for($type)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.facility-types.index'))
        ->assertOk()
        ->assertViewIs('admin.facility-types.index')
        ->assertSee('Badminton');
});

test('index can be searched', function () {
    FacilityType::factory()->create(['name' => 'Squash']);
    FacilityType::factory()->create(['name' => 'Pickleball']);

    $this->actingAs($this->admin)
        ->get(route('admin.facility-types.index', ['search' => 'Pick']))
        ->assertSee('Pickleball')
        ->assertDontSee('Squash');
});

test('store saves a new type and redirects with a flash message', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.facility-types.store'), ['name' => 'Squash'])
        ->assertRedirect(route('admin.facility-types.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('facility_types', ['name' => 'Squash']);
});

test('store validates the name', function (array $data, string $error) {
    FacilityType::factory()->create(['name' => 'Futsal']);

    $this->actingAs($this->admin)
        ->from(route('admin.facility-types.create'))
        ->post(route('admin.facility-types.store'), $data)
        ->assertRedirect(route('admin.facility-types.create'))
        ->assertSessionHasErrors(['name' => $error]);
})->with([
    'missing' => [[], 'The name field is required.'],
    'too short' => [['name' => 'Ab'], 'The name field must be at least 3 characters.'],
    'duplicate' => [['name' => 'Futsal'], 'There is already a facility type with this name.'],
]);

test('update renames a type, and may keep its own name', function () {
    $type = FacilityType::factory()->create(['name' => 'Tennis']);

    $this->actingAs($this->admin)
        ->put(route('admin.facility-types.update', $type), ['name' => 'Tennis'])
        ->assertSessionHasNoErrors();

    $this->actingAs($this->admin)
        ->put(route('admin.facility-types.update', $type), ['name' => 'Lawn Tennis'])
        ->assertRedirect(route('admin.facility-types.index'));

    expect($type->fresh()->name)->toBe('Lawn Tennis');
});

test('destroy deletes an unused type', function () {
    $type = FacilityType::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.facility-types.destroy', $type))
        ->assertRedirect(route('admin.facility-types.index'));

    $this->assertModelMissing($type);
});

test('destroy refuses a type that a facility still uses', function () {
    $facility = Facility::factory()->create();

    $this->actingAs($this->admin)
        ->from(route('admin.facility-types.index'))
        ->delete(route('admin.facility-types.destroy', $facility->facilityType))
        ->assertRedirect(route('admin.facility-types.index'))
        ->assertSessionHasErrors('type');

    $this->assertModelExists($facility->facilityType);
});

test('a missing type gives a 404 through route model binding', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.facility-types.edit', 9999))
        ->assertNotFound();
});

test('non-admins cannot create, update or delete types', function () {
    $type = FacilityType::factory()->create(['name' => 'Netball']);
    $student = User::factory()->student()->create();

    $this->actingAs($student)->post(route('admin.facility-types.store'), ['name' => 'Hacked'])->assertNotFound();
    $this->actingAs($student)->put(route('admin.facility-types.update', $type), ['name' => 'Hacked'])->assertNotFound();
    $this->actingAs($student)->delete(route('admin.facility-types.destroy', $type))->assertNotFound();

    expect($type->fresh()->name)->toBe('Netball');
    $this->assertDatabaseMissing('facility_types', ['name' => 'Hacked']);
});

