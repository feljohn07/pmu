<div class="p-4 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Project Form</h2>

    {{-- Display Success/Error Messages (Example, adapt if not using session flash) --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form wire:submit.prevent="save" class="space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                {{-- Updated Label and wire:model --}}
                <label class="block font-medium text-sm text-gray-700">Project Name</label>
                <input type="text" wire:model.defer="projectName"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('projectName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">Category</label>
                <input type="text" wire:model.defer="project_category"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('project_category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">Appropriation</label>
                <input disabled type="text" wire:model.defer="appropriation"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('appropriation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">Source of Funds</label>
                <input type="text" wire:model.defer="source_of_funds"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('source_of_funds') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">Duration (days)</label>
                <input type="text" wire:model.defer="duration"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('duration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-medium text-sm text-gray-700">Start Date</label>
                <input type="date" wire:model.defer="start_date"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <hr class="my-6">

        {{-- Cost Sections --}}
        <div class="space-y-6">
            @php
                // Define groups directly here or pass from component if needed elsewhere
                $costGroups = [
                    'directCost' => 'Direct Cost',
                    'indirectCost' => 'Indirect Cost',
                    'governmentExpenditures' => 'Government Expenditures',
                    'physicalContingencies' => 'Physical Contingencies',
                ];
            @endphp

            @foreach ($costGroups as $groupKey => $label)
                <div class="mt-6 border p-4 rounded bg-gray-50 shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">{{ $label }}</h3>

                    {{-- Use wire:key for efficient DOM updates in loops --}}
                    @foreach ($this->{$groupKey} as $index => $cost)
                        <div wire:key="{{ $groupKey }}-{{ $index }}"
                            class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-3 items-end mb-3 p-2 border-b border-gray-200 last:border-b-0">
                            {{-- Description --}}
                            <div class="sm:col-span-3 md:col-span-2">
                                <label for="{{ $groupKey }}_{{ $index }}_desc"
                                    class="text-sm font-medium text-gray-600">Description</label>
                                <input type="text" id="{{ $groupKey }}_{{ $index }}_desc"
                                    wire:model.defer="{{ $groupKey }}.{{ $index }}.cost_description"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm p-1.5">
                                @error("{$groupKey}.{$index}.cost_description") <span
                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            {{-- Percentage --}}
                            {{-- <div>
                                <label for="{{ $groupKey }}_{{ $index }}_perc"
                                    class="text-sm font-medium text-gray-600">Percentage (%)</label>
                                <input type="number" step="0.01" id="{{ $groupKey }}_{{ $index }}_perc"
                                    wire:model.defer="{{ $groupKey }}.{{ $index }}.percentage"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm p-1.5">
                                @error("{$groupKey}.{$index}.percentage") <span class="text-red-500 text-xs">{{ $message
                                    }}</span> @enderror
                            </div> --}}
                            {{-- Amount --}}
                            <div>
                                <label for="{{ $groupKey }}_{{ $index }}_amount"
                                    class="text-sm font-medium text-gray-600">Amount</label>
                                <input type="number" step="0.01" id="{{ $groupKey }}_{{ $index }}_amount"
                                    wire:model.defer="{{ $groupKey }}.{{ $index }}.amount"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm p-1.5">
                                @error("{$groupKey}.{$index}.amount") <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- Action Buttons --}}
                            <div class="flex gap-2 items-center pt-4 md:pt-5 col-span-1">
                                {{-- Add Above --}}
                                <button type="button" wire:click="addRowAt('{{ $groupKey }}', {{ $index }}, 'above')"
                                    class="p-1 text-blue-600 hover:text-blue-800" title="Add Row Above">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                </button>
                                {{-- Add Below --}}
                                <button type="button" wire:click="addRowAt('{{ $groupKey }}', {{ $index }}, 'below')"
                                    class="p-1 text-green-600 hover:text-green-800" title="Add Row Below">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </button>
                            </div>
                            {{-- Remove Button --}}
                            <div class="flex items-center pt-4 md:pt-5 col-span-1 justify-end">
                                {{-- Only show remove if more than one row exists --}}
                                @if (count($this->{$groupKey}) > 1)
                                    <button type="button" wire:click="removeRow('{{ $groupKey }}', {{ $index }})"
                                        class="p-1 text-red-600 hover:text-red-800" title="Remove Row">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Button to add a new row at the end of the group --}}
                    <button type="button"
                        wire:click="addRowAt('{{ $groupKey }}', {{ count($this->{$groupKey}) - 1 }}, 'below')"
                        class="mt-4 px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        + Add {{ $label }} Row
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Submit Button --}}
        <div class="pt-6 flex justify-end">
            @if ($projectId === null)
                <a href="{{ url(route('project-view', ['id' => $projectId])) }}" class="btn btn-outline me-2">
                    &larr; Cancel
                </a>
            @else
                <a href="{{ url(route($category,)) }}" class="btn btn-outline me-2">
                    &larr; Cancel
                </a>
            @endif


            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                <span wire:loading.remove wire:target="save">Save Project</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>