<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use Alert;

    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone_number = null;
    public string $status = 'active';
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public ?string $role = null;
    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.users.edit');
    }

    #[On('load::user')]
    public function load(User $user): void
    {
        $this->authorize('manage users');

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->status = $user->status;
        $this->role = $user->roles->first()?->name;
        $this->modal = true;
    }

    #[Computed]
    public function roles(): array
    {
        return Role::orderBy('name')->get()
            ->map(fn($role) => ['label' => ucfirst($role->name), 'value' => $role->name])
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name']
        ];
    }

    public function save(): void
    {
        $this->authorize('manage users');

        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
        ]);

        if ($this->password) {
            $this->user->update(['password' => bcrypt($this->password)]);
        }

        $this->user->syncRoles([$this->role]);

        $this->dispatch('updated');
        $this->reset(['password', 'password_confirmation']);
        $this->success('User updated successfully');
    }
}