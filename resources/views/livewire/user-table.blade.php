<div class="mx-5">

    <x-mary-card class="mt-10">

        <div class="overflow-x-auto">
            <x-mary-button label="New User" @click="$wire.showModal(true, null)" />

            <table class="table w-full">
                <thead>
                    <tr>
                        <th></th>
                        @foreach ($headers as $header)
                            <th>{{ $header['label'] }}</th>
                        @endforeach
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="avatar">
                                <div class="w-10 rounded">
                                    <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
                                </div>
                            </td>
                            @foreach ($headers as $header)
                                <td>{{ $user->{$header['key']} ?? 'User Position' }}</td>
                            @endforeach
                            <td>
                                <button wire:click="showModal(true, {{ $user->id }})"
                                    class="btn btn-warning btn-sm">Edit</button>
                                <button wire:click="delete({{ $user->id }})" class="btn btn-error btn-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </x-mary-card>

    <x-mary-modal wire:model="isShow" class="backdrop-blur" box-class="bg-red-50 p-10 w-full h-full" persistent>
        <x-user-form submit-method="{{ $editing ? 'edit' : 'add' }}" name="{{ $userForm['name'] ?? '' }}"
            email="{{ $userForm['email'] ?? '' }}" password="" />
    </x-mary-modal>

</div>