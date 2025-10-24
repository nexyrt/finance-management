<?php

namespace App\Livewire\Users;

use App\Livewire\Traits\Alert;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    use Alert;

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
        return view('livewire.users.create');
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
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name']
        ];
    }

    public function save(): void
    {
        $this->authorize('manage users');
        
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'password' => bcrypt($this->password),
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole($this->role);

        $this->dispatch('created');
        $this->reset();
        $this->status = 'active';
        $this->success('User created successfully');
    }
}