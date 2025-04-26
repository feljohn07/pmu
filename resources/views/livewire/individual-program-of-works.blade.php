{{-- resources/views/livewire/individual-program-of-works.blade.php --}}
<div class="mt-5">
    {{-- Main Card for Listing POW Items --}}
    <x-mary-card title="Individual Program Of Works (POW)"
        subtitle="Details for Project: {{ $project->project_name ?? 'N/A' }}" shadow>
        <x-slot:menu>
            {{-- Add New POW Item Button --}}
            <x-mary-button label="Add POW Item" icon="o-plus" class="btn-primary" wire:click="createPowItem" spinner />
        </x-slot:menu>

        {{-- Session Messages for List View (Optional) --}}
        @if (session()->has('success') && !$showFullScreenForm) {{-- Only show list success if form is closed --}}
            <x-mary-alert title="Success!" :description="session('success')" icon="o-check-circle"
                class="alert-success mb-4" />
        @endif
        @if (session()->has('error') && !$showFullScreenForm) {{-- Only show list error if form is closed --}}
            <x-mary-alert title="Error!" :description="session('error')" icon="o-exclamation-triangle"
                class="alert-error mb-4" />
        @endif

        {{-- Table Section --}}
        <div class="overflow-x-auto mt-4">
            <table class="table table-zebra w-full">
                {{-- Table Head --}}
                <thead>
                    <tr>
                        <th>Item No.</th>
                        <th>Work Description</th>
                        <th class="text-right">Quantity</th>
                        <th>Unit</th>
                        <th class="text-right">Adj. Unit Cost</th>
                        <th class="text-right">Total Cost</th>
                        <th class="text-center">Duration (Days)</th>
                        <th class="text-center">Progress (%)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                {{-- Table Body --}}
                <tbody>
                    @forelse ($programOfWorks as $item)
                        <tr wire:key="pow-item-{{ $item->id }}" class="hover:bg-base-200">
                            <td>{{ $item->item_number }}</td>
                            <td>
                                <div class="font-medium">{{ $item->work_description }}</div>
                                @if($item->item_description)
                                    <div class="text-xs text-gray-500">{{ $item->item_description }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                {{ number_format($item->quantity, $item->quantity_unit == 'l.s.' ? 0 : 2) }}
                            </td>
                            <td>{{ $item->quantity_unit }}</td>
                            <td class="text-right">{{ number_format($item->adjusted_unit_cost, 2) }}</td>
                            <td class="text-right font-semibold">{{ number_format($item->total_item_cost, 2) }}</td>
                            <td class="text-center">{{ $item->duration }}</td>
                            <td class="text-center">{{ $item->progress }}%</td>
                            <td>
                                <div class="flex space-x-1 justify-end">
                                    {{-- Edit Button --}}
                                    <x-mary-button icon="o-pencil-square" wire:click="editPowItem({{ $item->id }})"
                                        class="btn-sm btn-ghost text-blue-600" spinner tooltip="Edit Item" />
                                    {{-- Delete Button --}}
                                    <x-mary-button icon="o-trash" wire:click="deletePowItem({{ $item->id }})"
                                        wire:confirm="Are you sure you want to delete this item?"
                                        class="btn-sm btn-ghost text-red-600" spinner tooltip="Delete Item" />

                                    <x-mary-button icon="o-eye" wire:click="viewForm({{ $item->id }})"
                                        class="btn-sm btn-ghost text-red-600" spinner tooltip="View Form" />


                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No Program of Work items found for this project yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-mary-card>


</div>