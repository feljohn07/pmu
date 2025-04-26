<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

use Mary\Traits\Toast;
use Spatie\Permission\Models\Role;

class UserTable extends Component
{
    public bool $isShow = false;
    public $editing = false;
    public $userForm = [
        'id' => null,
        'name' => '',
        'email' => '',
        'staff_position' => '',
        'password' => '',
        'role_name' => '',
    ];

    use Toast;

    public $users;
    public $roles;

    public bool $staffOnly = false;

    
    public function mount(bool $staffOnly = false)
    {
        $this->staffOnly = $staffOnly;
        $this->getAllUsers();
        $this->getAllRoles();
    }

    
    private function getAllRoles()
    {
        $this->roles = Role::pluck('name', 'name')->toArray(); // Fetch roles as name => name array for select
    }

    public function showModal($state, $userId = null)
    {
        // dd($state);
        $this->isShow = $state;

        if ($userId != null) {
            $user = User::findOrFail($userId);
            $this->userForm = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'staff_position' => $user->staff_position,
                'role_name' => $user->getRoleNames()->first() ?? '',
            ];

            // dd($this->userForm);

            $this->editing = true;
        } else {
            $this->userForm = [
                'id' => null,
                'name' => '',
                'email' => '',
                'staff_position' => '',
                'password' => '',
                'role_name' => '',
            ];
            $this->editing = false;
        }
    }

    private function getAllUsers()
    {
        // $this->users = User::select()->get();
        // Start query builder
        $query = User::query()->with('roles'); // Eager load roles

        // Conditionally apply the filter
        $query->when($this->staffOnly, function ($q) {
            // If staffOnly is true, only get users who have at least one role
            return $q->whereHas('roles');
        });

        // Select specific columns if needed (optional) and get results
        // If you select specific columns, ensure you include 'id' and any columns used by relationships/display
        $this->users = $query->select('users.*')->get(); // Get all columns from users table
    }

    public function add()
    {
        try {
            $user = User::create([
                "name" => $this->userForm['name'],
                "email" => $this->userForm['email'],
                "staff_position" => $this->userForm['staff_position'],
                "password" => bcrypt($this->userForm['password']),
            ]);

            $user->assignRole($this->userForm['role_name']);

            $this->getAllUsers();
            $this->success("Added Successfully");
            $this->showModal(false);

        } catch (\Throwable $th) {
            $this->error("An Error Occurred");
        }
    }

    public function edit()
    {
        try {

            if ($this->userForm['password'] ?? true) {
                $user = User::findOrFail($this->userForm['id']);
                $user->update([
                    "name" => $this->userForm['name'],
                    "email" => $this->userForm['email'],
                    "staff_position" => $this->userForm['staff_position'],
                ]);

            } else {
                $user = User::findOrFail($this->userForm['id']);
                $user->update([
                    "name" => $this->userForm['name'],
                    "email" => $this->userForm['email'],
                    "password" => bcrypt($this->userForm['password']),
                ]);

            }

            // --- Sync roles: Pass role name if selected, or empty array if "None" ---
            if (!empty($this->userForm['role_name'])) {
                $user->syncRoles([$this->userForm['role_name']]); // Assign the selected role
            } else {
                $user->syncRoles([]); // Remove all roles if "None" is selected
            }


            $this->getAllUsers();
            $this->success("Updated Successfully");
            $this->showModal(false);

        } catch (\Throwable $th) {

            dd($th);
            $this->error("An Error Occurred");
        }
    }

    public function delete($id)
    {
        User::find($id)->delete();
        $this->getAllUsers();
        $this->success("Deleted Succesfully");
    }



    public function render()
    {
        return view('livewire.user-table');
    }
}
