<div class="mx-5">

    <x-mary-card class="mt-10">

        <div class="overflow-x-auto">
            {{-- <x-mary-button label="New User" @click="$wire.showModal(true, null)" />
            --}}

            @role('admin')
            <x-mary-button label="New {{ $staffOnly == true ? 'Staff' : 'User' }}" @click="$wire.showModal(true, null)"
                class="mb-4" />
            @endrole

            <table class="table w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Role(s)</th>
                        <th></th>
                        {{-- <th>Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>

                            <td class="avatar">
                                @if ($user['profile'])
                                    <div class="w-10 rounded">
                                        <x-mary-avatar :image="Storage::url($user['profile'])" placeholder="RT" class="!w-10" />
                                        {{-- <img src="{{ Storage::url($user['profile']) }}" alt="{{ $user->name }}"
                                            style="max-width: 50px; max-height: 50px;"> --}}
                                    </div>
                                @else
                                    <x-mary-avatar placeholder="RT" class="!w-10" />
                                @endif
                            </td>

                            <td>{{ $user['id']}}</td>
                            <td>{{ $user['name']}}</td>
                            <td>{{ $user['staff_position']}}</td>
                            <td>
                                {{-- Use Spatie's getRoleNames() which returns a collection --}}
                                {{-- Implode the names with a comma, or show 'None' if no roles --}}
                                {{ $user->getRoleNames()->implode(', ') ?: 'None' }}
                            </td>
                            <td>
                                @role('admin')
                                <button wire:click="showModal(true, {{ $user->id }})"
                                    class="btn btn-warning btn-sm">Edit</button>
                                <button wire:click="delete({{ $user->id }})" class="btn btn-error btn-sm">Delete</button>
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </x-mary-card>
    <x-mary-modal wire:model="isShow" title="{{ $editing ? 'Edit User' : 'Add New User' }}"
        subtitle="{{ $editing ? 'Update the user details below' : 'Enter the details for the new user' }}" separator
        class="backdrop-blur" persistent>

        {{-- Standard form with Daisy UI styled fields --}}
        <form wire:submit.prevent="{{ $editing ? 'edit' : 'add' }}" class="space-y-4">

            {{-- Name Input --}}
            <div class="form-control">
                <label for="name" class="label">
                    <span class="label-text">Name <span class="text-error">*</span></span>
                </label>
                <input type="text" id="name" wire:model="userForm.name"
                    class="input input-bordered w-full @error('userForm.name') input-error @enderror" required />
                @error('userForm.name')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            {{-- Email Input --}}
            <div class="form-control">
                <label for="email" class="label">
                    <span class="label-text">Email <span class="text-error">*</span></span>
                </label>
                <input type="email" id="email" wire:model="userForm.email"
                    class="input input-bordered w-full @error('userForm.email') input-error @enderror" required />
                @error('userForm.email')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            {{-- Password Input with Show/Hide Toggle (Alpine.js) --}}
            <div x-data="{ show: false }" class="form-control">
                <label for="password" class="label">
                    <span class="label-text">Password @if(!$editing) <span class="text-error">*</span> @endif</span>
                    @if($editing)
                        <span class="label-text-alt">Leave blank to keep current</span>
                    @endif
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="password" wire:model="userForm.password"
                        class="input input-bordered w-full pr-16 @error('userForm.password') input-error @enderror" {{--
                        Only strictly required when creating --}} @if(!$editing) required @endif />
                    <button type="button" @click="show = !show"
                        class="btn btn-sm btn-ghost absolute top-0 right-0 h-full px-3"
                        aria-label="Toggle password visibility">
                        {{-- You can use text or icons here --}}
                        <span x-text="show ? 'Hide' : 'Show'"></span>
                        {{-- Example with Heroicons (if available):
                        <x-heroicon-o-eye x-show="!show" class="w-5 h-5" />
                        <x-heroicon-o-eye-slash x-show="show" class="w-5 h-5" />
                        --}}
                    </button>
                </div>
                @error('userForm.password')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            {{-- Staff Position Input --}}
            <div class="form-control">
                <label for="staff_position" class="label">
                    <span class="label-text">Position <span class="text-error">*</span></span>
                </label>
                <input type="text" id="staff_position" wire:model="userForm.staff_position"
                    class="input input-bordered w-full @error('userForm.staff_position') input-error @enderror"
                    required />
                @error('userForm.staff_position')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            {{-- Role Select Input --}}
            <div class="form-control">
                <label for="role" class="label">
                    <span class="label-text">Role</span> {{-- Assuming role might not be strictly required --}}
                </label>
                <select id="role" wire:model="userForm.role_name"
                    class="select select-bordered w-full @error('userForm.role_name') select-error @enderror">
                    <option value="">Select a role (or No Role)</option>
                    {{-- Loop through roles fetched in component --}}
                    @foreach($roles as $roleName => $roleLabel)
                        <option value="{{ $roleName }}">{{ $roleLabel }}</option>
                    @endforeach
                </select>
                @error('userForm.role_name')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                @enderror
            </div>

            {{-- Action Buttons using Daisy UI --}}
            <div class="flex justify-end space-x-3 pt-4">
                {{-- Cancel Button --}}
                <button type="button" class="btn btn-ghost" @click="$wire.isShow = false">
                    Cancel
                </button>

                {{-- Submit Button with Loading State --}}
                <button type="submit" class="btn btn-primary">
                    {{-- Show text or spinner based on loading state --}}
                    <span wire:loading.remove wire:target="{{ $editing ? 'edit' : 'add' }}">
                        {{ $editing ? 'Update User' : 'Create User' }}
                    </span>
                    <span wire:loading wire:target="{{ $editing ? 'edit' : 'add' }}">
                        <span class="loading loading-spinner loading-sm"></span>
                        {{ $editing ? 'Updating...' : 'Creating...' }}
                    </span>
                </button>
            </div>
        </form>

        {{-- This modal does not use the <x-slot:actions> --}}

    </x-mary-modal>
</div>