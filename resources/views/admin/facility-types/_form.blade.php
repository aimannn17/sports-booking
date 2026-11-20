{{--
    Shared fields for create and edit.
    old('name', $type->name): after a failed submit show what the user typed,
    otherwise the saved value (empty for a new type).
--}}
<div>
    <flux:label for="name">Name</flux:label>
    <flux:input id="name" name="name" :value="old('name', $type->name)" placeholder="Badminton" required autofocus class="mt-2" />
    <x-forms.error name="name" />
</div>
