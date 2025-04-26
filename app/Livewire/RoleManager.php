<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{

    public $roles;
    public $name;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function createRole()
    {
        $this->validate(['name' => 'required|unique:roles,name']);
        Role::create(['name' => $this->name]);
        $this->roles = Role::all();
        $this->name = '';
    }

    public function deleteRole($id)
    {
        Role::find($id)->delete();
        $this->roles = Role::all();
    }

    public function render()
    {
        return view('livewire.role-manager');
    }
}
