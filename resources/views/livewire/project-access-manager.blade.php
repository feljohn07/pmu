{{-- resources/views/livewire/project-access-manager.blade.php --}}
<x-mary-card class="mt-10"> {{-- Livewire components must have a single root element --}}

    @if ($project) {{-- Check if the project model was loaded successfully --}}
        <div class="mb-6 p-4 border rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-2">
                <h4 class="text-lg font-medium mb-2">Current Users with Access:</h4>
                {{-- Button to open the modal --}}
                <x-mary-button label="Give Access" icon="o-plus" wire:click="openUserModal" class="btn-primary btn-sm" spinner="openUserModal" />
            </div>

            {{-- List of users currently with access --}}
            
            @if ($project->users->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                             {{-- Order users by name for display --}}
                            @foreach ($project->users()->orderBy('name')->get() as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{-- Example Revoke Button --}}
                                        <x-mary-button icon="o-trash" wire:click="revokeAccess({{ $user->id }})" wire:confirm="Are you sure you want to revoke access for {{ $user->name }}?" spinner class="btn-ghost btn-xs text-red-500" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 italic">No users currently have access to this project.</p>
            @endif
        </div>

        {{-- User Selection Modal --}}
        <x-mary-modal wire:model="showUserModal" title="Grant Access to Users" subtitle="Select users to add to '{{ $project->name }}'">

            {{-- Search Input --}}
            <x-mary-input label="Search Users" wire:model.live.debounce.300ms="userSearch" icon="o-magnifying-glass" placeholder="Search by name or email..." class="mb-4" />

            {{-- List of Available Users --}}
            <div class="max-h-96 overflow-y-auto pr-2"> {{-- Scrollable area for user list --}}
                @if(count($this->availableUsers)) {{-- Use the computed property --}}
                    <div class="space-y-2">
                        @foreach ($this->availableUsers as $user)
                            <x-mary-checkbox
                                :label="$user->name . ' (' . $user->email . ')'"
                                id="user-{{ $user->id }}"
                                value="{{ $user->id }}"
                                wire:model.live="selectedUserIds" {{-- Use .live if immediate feedback needed, otherwise omit --}}
                                class="p-2 rounded hover:bg-base-200"
                            />
                        @endforeach
                    </div>
                @elseif(strlen($userSearch) > 0)
                     <p class="text-center text-gray-500 py-4">No users found matching "{{ $userSearch }}".</p>
                @else
                     <p class="text-center text-gray-500 py-4">No users available to add.</p>
                @endif

                {{-- Loading indicator while searching --}}
                <div wire:loading wire:target="userSearch" class="text-center py-4">
                    <x-mary-loading class="text-primary"/>
                </div>
                 {{-- Loading indicator while fetching computed property --}}
                <div wire:loading wire:target="availableUsers" class="text-center py-4">
                    <x-mary-loading class="text-secondary"/>
                </div>
            </div>

            {{-- Modal Actions --}}
            <x-slot:actions>
                {{-- Note: Clicking Cancel automatically closes the modal via wire:model --}}
                <x-mary-button label="Cancel" @click="$wire.showUserModal = false" />
                <x-mary-button label="Grant Access" class="btn-primary" wire:click="grantAccess" spinner="grantAccess" :disabled="empty($selectedUserIds)" />
            </x-slot:actions>
        </x-mary-modal>

    @elseif($projectId)
        {{-- Project Not Found Error --}}
        <x-mary-alert title="Error" description="Project with ID '{{ $projectId }}' not found." icon="o-exclamation-triangle" class="alert-error" />
    @else
         {{-- No Project ID Provided --}}
         <x-mary-alert title="Missing Information" description="Please provide a project ID." icon="o-information-circle" class="alert-warning" />
    @endif

</x-mary-card>
