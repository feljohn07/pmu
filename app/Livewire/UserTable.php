<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

use Mary\Traits\Toast;

class UserTable extends Component
{
    public bool $isShow = false;
    public $editing = false;
    public $userForm = [];

    use Toast;

    public $headers = [
        // ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'E-mail'],
        ['key' => 'position', 'label' => 'Position'],
    ];

    public $users;

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
            ];

            // dd($this->userForm);

            $this->editing = true;
        } else {
            $this->userForm = [];
            $this->editing = false;
        }
    }

    private function getAllUsers()
    {
        $this->users = User::select()->get();
    }

    public function add()
    {
        try {
            User::create([
                "name" => $this->userForm['name'],
                "email" => $this->userForm['email'],
                "password" => bcrypt($this->userForm['password']),
            ]);

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
            $user = User::findOrFail($this->userForm['id']);
            $user->update([
                "name" => $this->userForm['name'],
                "email" => $this->userForm['email'],
                "password" => $this->userForm['password'] ? bcrypt($this->userForm['password']) : $user->password,
            ]);

            $this->getAllUsers();
            $this->success("Updated Successfully");
            $this->showModal(false);

        } catch (\Throwable $th) {
            $this->error("An Error Occurred");
        }
    }

    public function delete($id)
    {
        User::find($id)->delete();
        $this->getAllUsers();
        $this->success("Deleted Succesfully");
    }


    public function mount()
    {
        $this->getAllUsers();
    }


    public function render()
    {
        return view('livewire.user-table');
    }
}
