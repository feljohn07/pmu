<div>
    <h2>Manage Permissions</h2>
    <form wire:submit.prevent="createPermission">
        <input type="text" wire:model="name" placeholder="Permission Name">
        <button type="submit">Add Permission</button>
    </form>

    <ul>
        @foreach($permissions as $permission)
            <li>{{ $permission->name }}
                <button wire:click="deletePermission({{ $permission->id }})">Delete</button>
            </li>
        @endforeach
    </ul>
</div>