<div>
    <h2>Manage Roles</h2>
    <form wire:submit.prevent="createRole">
        <input type="text" wire:model="name" placeholder="Role Name">
        <button type="submit">Add Role</button>
    </form>

    <ul>
        @foreach($roles as $role)
            <li>{{ $role->name }}
                <button wire:click="deleteRole({{ $role->id }})">Delete</button>
            </li>
        @endforeach
    </ul>
</div>
