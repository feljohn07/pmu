<div>
    <x-mary-card class="mt-10">
        <div class="flex items-center justify-between w-full mb-4">
            <p class="text-lg font-semibold">Project List</p>
            <div>
                @hasanyrole(['staff'])
                <x-mary-button label="Add New Project" wire:click="createProject" class="btn-primary" />
                @endhasallroles
            </div>
        </div>

        {{-- Session Message --}}
        @if (session()->has('message'))
            <div x-data="{ showBanner: true }" x-show="showBanner" x-init="setTimeout(() => showBanner = false, 3000)"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
                <button @click="showBanner = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="overflow-x-auto mt-5">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Code No.</th>
                        <th>Construction Project</th>
                        {{-- <th>Material Cost</th>
                        <th>Labor Cost</th>
                        <th>Total Contract Amount</th> --}}
                        <th>POW Status</th>
                        <th>Progress</th>
                        <th>Implementation Status</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                    <tr>
                        {{-- <tr class="hover:bg-gray-100"> --}}
                            <td>{{ $project['id'] }}</td>
                            <td>{{ $project['project_name'] }}</td>
                            {{-- <td>{{ number_format($project['material_cost'], 2) }}</td>
                            <td>{{ number_format($project['labor_cost'], 2) }}</td>
                            <td>{{ number_format($project['total_contract_amount'], 2) }}</td> --}}
                            <td>{{ $project['pow_status'] }}</td>
                            <td>{{ $project['physical_accomplishment'] }} %</td>
                            <td>{{ $project['implementation_status'] }}</td>
                            <td>{{ $project['duration'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($project['start_date'])->format('F j, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($project['end_date'])->format('F j, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    {{-- Add View button if needed, link to appropriate route --}}
                                    {{-- <x-mary-button label="View"
                                        link="{{ route('view-project', ['id' => $project->id]) }}"
                                        class="btn-info btn-sm" /> --}}
                                    {{-- Ensure action names match your Livewire component --}}
                                    @hasanyrole(['staff'])
                                    <x-mary-button label="Edit" wire:click="editProjectRedirect({{ $project->id }})"
                                        class="btn-warning btn-sm" />
                                    <x-mary-button label="Delete" wire:click="deleteProject({{ $project->id }})"
                                        class="btn-error btn-sm" />
                                    @endhasallroles
                                    <x-mary-button label="View" wire:click="viewProject({{ $project->id }})"
                                        class="btn-success btn-sm" />
                                </div>
                            </td>
                            {{-- <td>
                                <x-mary-dropdown icon='o-ellipsis-vertical' top=true >
                                    @hasanyrole(['staff'])
                                        <x-mary-menu-item wire:click="editProject({{ $project->id }})">
                                            Edit
                                        </x-mary-menu-item>
                                        <x-mary-menu-item wire:click="deleteProject({{ $project->id }})">
                                            Delete
                                        </x-mary-menu-item>
                                    @endhasanyrole
                                    <x-mary-menu-item wire:click="viewProject({{ $project->id }})">
                                        View
                                    </x-mary-menu-item>
                                </x-mary-dropdown>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-mary-card>

    {{-- Modal for Add/Edit Project (Existing Code) --}}
    
</div>