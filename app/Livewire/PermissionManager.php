<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionManager extends Component
{

    public $permissions;
    public $name;

    public function mount()
    {
        $this->permissions = Permission::all();
    }

    public function createPermission()
    {
        $this->validate(['name' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->name]);
        $this->permissions = Permission::all();
        $this->name = '';
    }

    public function deletePermission($id)
    {
        Permission::find($id)->delete();
        $this->permissions = Permission::all();
    }
    
    public function render()
    {
        return view('livewire.permission-manager');
    }
}
