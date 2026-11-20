<?php

use App\Actions\Users\DeleteUser;
use App\Actions\Users\UpdateUser;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/*
 * FR-A8: view, search, edit and delete user accounts.
 */
new #[Title('Users')] class extends Component {
    use WithPagination;

    public UserForm $form;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $role = '';

    #[Locked]
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'role'], true)) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->withCount('bookings')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('student_id', 'like', "%{$this->search}%")))
            ->when($this->role !== '', fn ($q) => $q->where('role', $this->role))
            ->orderBy('name')
            ->paginate(15);
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $this->resetValidation();
        $this->form->setUser($user);
        Flux::modal('user-form')->show();
    }

    public function save(UpdateUser $update): void
    {
        abort_if($this->form->user === null, 404);
        $this->authorize('update', $this->form->user);

        $data = $this->form->validate();

        try {
            $update->handle(auth()->user(), $this->form->user, $data);
        } catch (ValidationException $e) {
            $this->addError('form.role', $e->validator->errors()->first());

            return;
        }

        Flux::modal('user-form')->close();
        Flux::toast(variant: 'success', text: __('User updated.'));
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        Flux::modal('delete-user')->show();
    }

    public function delete(DeleteUser $delete): void
    {
        $user = User::findOrFail($this->deletingId);
        $this->authorize('delete', $user);

        try {
            $delete->handle(auth()->user(), $user);
            Flux::toast(variant: 'success', text: __('User deleted.'));
        } catch (ValidationException $e) {
            Flux::toast(variant: 'danger', text: $e->validator->errors()->first());
        }

        $this->deletingId = null;
        Flux::modal('delete-user')->close();
    }
}; ?>

<div class="flex flex-col gap-6">
    <div>
        <flux:heading size="xl" level="1">{{ __('Users') }}</flux:heading>
        <flux:subheading>{{ __('Students, external users and admins.') }}</flux:subheading>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Name, email or student ID')" class="sm:max-w-xs" />
        <flux:select wire:model.live="role" class="sm:max-w-44">
            <flux:select.option value="">{{ __('All roles') }}</flux:select.option>
            @foreach (\App\Enums\Role::cases() as $r)
                <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$this->users">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Role') }}</flux:table.column>
            <flux:table.column>{{ __('Student ID') }}</flux:table.column>
            <flux:table.column>{{ __('Bookings') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong"><flux:link :href="route('admin.users.show', $user)" wire:navigate>{{ $user->name }}</flux:link></flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell><flux:badge size="sm" :color="$user->role->color()">{{ $user->role->label() }}</flux:badge></flux:table.cell>
                    <flux:table.cell>{{ $user->student_id ?? '—' }}</flux:table.cell>
                    <flux:table.cell>{{ $user->bookings_count }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $user->id }})">{{ __('Edit') }}</flux:button>
                        @can('delete', $user)
                            <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $user->id }})"
                                :disabled="$user->bookings_count > 0" :title="$user->bookings_count > 0 ? __('Has bookings') : null">{{ __('Delete') }}</flux:button>
                        @endcan
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="6">{{ __('No users found.') }}</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="user-form" class="md:w-[28rem]">
        <form wire:submit="save" class="flex flex-col gap-5">
            <flux:heading size="lg">{{ __('Edit user') }}</flux:heading>
            <flux:input wire:model="form.name" :label="__('Name')" required />
            <flux:input wire:model="form.email" type="email" :label="__('Email')" required />
            <flux:input wire:model="form.phone" :label="__('Phone')" />
            <flux:select wire:model="form.role" :label="__('Role')">
                @foreach (\App\Enums\Role::cases() as $r)
                    <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
                @endforeach
            </flux:select>
            {{-- Alpine: the student ID field only matters for students --}}
            <div x-show="$wire.form.role === 'student'" x-transition>
                <flux:input wire:model="form.student_id" :label="__('Student ID')" />
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-user" class="md:w-96">
        <div class="flex flex-col gap-5">
            <div>
                <flux:heading size="lg">{{ __('Delete this user?') }}</flux:heading>
                <flux:text class="mt-1">{{ __('The account is removed permanently. Users with bookings cannot be deleted.') }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close><flux:button variant="ghost">{{ __('Cancel') }}</flux:button></flux:modal.close>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
