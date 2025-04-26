<div>

    {{-- Header --}}
    <div class="flex justify-between items-center p-4 border-b bg-gray-50 rounded-t-lg">
        <h2 id="pow-item-form-title" class="text-xl font-bold text-gray-700">
            {{ $powId ? 'Edit' : 'Create New' }} Program of Work
        </h2>
        {{-- Use the cancelForm method for the close button --}}
        <x-mary-button icon="o-x-mark" class="btn-sm btn-ghost" wire:click="cancelForm" aria-label="Close form" />
    </div>

    {{-- Body (Scrollable Form) --}}
    <div class="p-6 flex-grow overflow-y-auto">
        {{-- Session Messages Inside Form --}}
        @if (session()->has('form_error'))
            <x-mary-alert title="Hold on!" :description="session('form_error')" icon="o-exclamation-triangle"
                class="alert-warning mb-4" />
        @endif
        {{-- Maybe a success message if you decide to keep the form open after save for some reason --}}
        @if (session()->has('form_success'))
            <x-mary-alert title="Success!" :description="session('form_success')" icon="o-check-circle"
                class="alert-success mb-4" />
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong> Please check the fields below.
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- The Form Itself --}}
        <form id="pow_item_form" wire:submit.prevent="save" class="space-y-8">

            {{-- Section 1: Main POW Item Details --}}
            <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2 text-gray-700">Work Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- Item Number --}}
                    <div>
                        <x-mary-input id="form_item_number" label="Item Number" wire:model.defer="item_number" />
                    </div>
                    {{-- Work Description (Category) --}}
                    <div class="lg:col-span-2">
                        <x-mary-input id="form_work_description" label="Work Description"
                            wire:model.defer="work_description" />
                    </div>
                    {{-- Item Description --}}
                    <div class="md:col-span-2 lg:col-span-3">
                        <x-mary-textarea id="form_item_description" label="Item Description *"
                            wire:model.defer="item_description" rows="2" />
                    </div>
                    {{-- Quantity --}}
                    <div>
                        <x-mary-input id="form_quantity" label="Quantity *" type="number" step="0.0001"
                            wire:model.live="quantity" />
                    </div>
                    {{-- Quantity Unit --}}
                    <div>
                        <x-mary-input id="form_quantity_unit" label="Unit *" wire:model.defer="quantity_unit"
                            placeholder="e.g., sqm, cu.m, pcs, l.s." />
                    </div>
                    {{-- Adjusted Unit Cost (Display Only) --}}
                    <div>
                        <x-mary-input id="form_adj_unit_cost" label="Adjusted Unit Cost"
                            value="{{ number_format($adjusted_unit_cost, 2) }}" readonly class="bg-gray-100" />
                    </div>
                    {{-- Total Item Cost (Display Only) --}}
                    <div>
                        <x-mary-input id="form_total_item_cost" label="Grand Total"
                            value="{{ number_format($total_item_cost, 2) }}" readonly class="bg-gray-100" />
                    </div>
                    {{-- Start Date --}}
                    <div>
                        <x-mary-input id="form_start_date" label="Start Date" type="date"
                            wire:model.defer="start_date" />
                    </div>
                    {{-- Duration --}}
                    <div>
                        <x-mary-input id="form_duration" label="Duration (Days) *" type="number"
                            wire:model.defer="duration" min="1" />
                    </div>
                    {{-- Progress --}}
                    <div>
                        <x-mary-input id="form_progress" label="Progress (%) *" type="number"
                            wire:model.defer="progress" min="0" max="100" />
                    </div>
                </div>
            </div>

            {{-- Section 2: Direct Costs (Material, Labor, Equipment) --}}
            <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2 text-gray-700">Direct Costs</h3>

                {{-- Material Costs --}}
                <div class="mb-6 border-l-4 border-blue-500 pl-4 py-2">
                    <h4 class="text-md font-medium mb-3 text-gray-800">A. Material Costs</h4>
                    <div class="space-y-3">
                        @foreach ($materialCosts as $index => $cost)
                            <div wire:key="mat-{{ $index }}"
                                class="grid grid-cols-12 gap-2 items-end pb-2 border-b last:border-b-0">
                                {{-- Use unique IDs if needed, though wire:key often suffices --}}
                                <div class="col-span-12 md:col-span-4"><x-mary-input dense placeholder="Description *"
                                        wire:model.live="materialCosts.{{ $index }}.description" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="Quantity *"
                                        type="number" step="0.0001" wire:model.live="materialCosts.{{ $index }}.quantity" />
                                </div>
                                <div class="col-span-6 md:col-span-1"><x-mary-input dense placeholder="Unit"
                                        wire:model.live="materialCosts.{{ $index }}.unit" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="Unit Price *"
                                        type="number" step="0.01" wire:model.live="materialCosts.{{ $index }}.price" />
                                </div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="Cost"
                                        value="{{ number_format($cost['cost'] ?? 0, 2) }}" readonly class="bg-gray-100" />
                                </div>
                                <div class="col-span-12 md:col-span-1 flex items-center justify-end space-x-1">
                                    <x-mary-button icon="o-plus-circle"
                                        wire:click="addCostRow('materialCosts', {{ $index }})"
                                        class="btn-xs btn-ghost text-green-600" spinner tooltip="Add Row Below" />
                                    @if (count($materialCosts) > 1)
                                        <x-mary-button icon="o-minus-circle"
                                            wire:click="removeCostRow('materialCosts', {{ $index }})"
                                            class="btn-xs btn-ghost text-red-600" spinner tooltip="Remove Row" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-right font-semibold pr-10 mt-2">
                        Material Subtotal: {{ number_format($materialSubtotal, 2) }}
                    </div>
                </div>

                {{-- Labor Costs --}}
                <div class="mb-6 border-l-4 border-green-500 pl-4 py-2">
                    <h4 class="text-md font-medium mb-3 text-gray-800">B. Labor Costs</h4>
                    <div class="space-y-3">
                        @foreach ($laborCosts as $index => $cost)
                            <div wire:key="lab-{{ $index }}"
                                class="grid grid-cols-12 gap-2 items-end pb-2 border-b last:border-b-0">
                                <div class="col-span-12 md:col-span-4"><x-mary-input dense placeholder="Description *"
                                        wire:model.live="laborCosts.{{ $index }}.description" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="No. Manpower *"
                                        type="number" step="0.01"
                                        wire:model.live="laborCosts.{{ $index }}.number_of_manpower" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="No. Days *"
                                        type="number" step="0.01"
                                        wire:model.live="laborCosts.{{ $index }}.number_of_days" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="Rate/Day *"
                                        type="number" step="0.01" wire:model.live="laborCosts.{{ $index }}.rate_per_day" />
                                </div>
                                <div class="col-span-6 md:col-span-1"><x-mary-input dense placeholder="Cost"
                                        value="{{ number_format($cost['cost'] ?? 0, 2) }}" readonly class="bg-gray-100" />
                                </div>
                                <div class="col-span-12 md:col-span-1 flex items-center justify-end space-x-1">
                                    <x-mary-button icon="o-plus-circle" wire:click="addCostRow('laborCosts', {{ $index }})"
                                        class="btn-xs btn-ghost text-green-600" spinner tooltip="Add Row Below" />
                                    @if (count($laborCosts) > 1)
                                        <x-mary-button icon="o-minus-circle"
                                            wire:click="removeCostRow('laborCosts', {{ $index }})"
                                            class="btn-xs btn-ghost text-red-600" spinner tooltip="Remove Row" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-right font-semibold pr-10 mt-2">
                        Labor Subtotal: {{ number_format($laborSubtotal, 2) }}
                    </div>
                </div>

                {{-- Equipment Costs --}}
                <div class="mb-6 border-l-4 border-yellow-500 pl-4 py-2">
                    <h4 class="text-md font-medium mb-3 text-gray-800">C. Equipment Costs</h4>
                    <div class="space-y-3">
                        @foreach ($equipmentCosts as $index => $cost)
                            <div wire:key="eqp-{{ $index }}"
                                class="grid grid-cols-12 gap-2 items-end pb-2 border-b last:border-b-0">
                                <div class="col-span-12 md:col-span-4"><x-mary-input dense placeholder="Description *"
                                        wire:model.live="equipmentCosts.{{ $index }}.description" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="No. Units *"
                                        type="number" step="0.01"
                                        wire:model.live="equipmentCosts.{{ $index }}.number_of_units" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="No. Days *"
                                        type="number" step="0.01"
                                        wire:model.live="equipmentCosts.{{ $index }}.number_of_days" /></div>
                                <div class="col-span-6 md:col-span-2"><x-mary-input dense placeholder="Rate/Day *"
                                        type="number" step="0.01"
                                        wire:model.live="equipmentCosts.{{ $index }}.rate_per_day" /></div>
                                <div class="col-span-6 md:col-span-1"><x-mary-input dense placeholder="Cost"
                                        value="{{ number_format($cost['cost'] ?? 0, 2) }}" readonly class="bg-gray-100" />
                                </div>
                                <div class="col-span-12 md:col-span-1 flex items-center justify-end space-x-1">
                                    <x-mary-button icon="o-plus-circle"
                                        wire:click="addCostRow('equipmentCosts', {{ $index }})"
                                        class="btn-xs btn-ghost text-green-600" spinner tooltip="Add Row Below" />
                                    @if (count($equipmentCosts) > 1)
                                        <x-mary-button icon="o-minus-circle"
                                            wire:click="removeCostRow('equipmentCosts', {{ $index }})"
                                            class="btn-xs btn-ghost text-red-600" spinner tooltip="Remove Row" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-right font-semibold pr-10 mt-2">
                        Equipment Subtotal: {{ number_format($equipmentSubtotal, 2) }}
                    </div>
                </div>

                <div class="text-right font-bold text-lg pr-10 mt-4 pt-4 border-t">
                    Total Direct Cost (A+B+C):
                    {{ number_format($materialSubtotal + $laborSubtotal + $equipmentSubtotal, 2) }}
                </div>
            </div>

            {{-- Section 3: Indirect Costs -- MODIFIED --}}
            <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2 text-gray-700">Indirect Costs (Manual Base Costs)
                </h3> {{-- Title changed --}}
                <div class="space-y-3">
                    @php
                        $indirectItems = [
                            'b1' => 'Overhead Expenses',
                            'b2' => 'Contingencies',
                            'b3' => 'Miscellaneous',
                            'b4' => "Contractor's Profit",
                            'b5' => 'VAT Component',
                        ];
                     @endphp
                    @foreach ($indirectItems as $key => $label)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center pb-2 border-b last:border-b-0"
                            wire:key="indirect-{{ $key }}">
                            {{-- Description --}}
                            <div class="md:col-span-4">
                                <x-mary-input id="pow_item_form_indirect_{{ $key }}_desc" dense label="{{ $label }}"
                                    wire:model.defer="indirectCostData.{{ $key }}_description" />
                            </div>
                            {{-- Base Cost - NOW MANUAL --}}
                            <div class="md:col-span-3">
                                <x-mary-input id="pow_item_form_indirect_{{ $key }}_base" dense label="Base Cost *" {{--
                                    Added asterisk --}} type="number" {{-- Set type --}} step="0.01" {{-- Set step --}}
                                    wire:model.live="indirectCostData.{{ $key }}_base_cost" {{-- Use live binding --}} {{--
                                    Removed readonly and bg-gray-100 --}} />
                            </div>
                            {{-- Markup Percent --}}
                            <div class="md:col-span-2">
                                <x-mary-input id="pow_item_form_indirect_{{ $key }}_perc" dense label="Markup (%) *"
                                    type="number" step="0.0001"
                                    wire:model.live="indirectCostData.{{ $key }}_markup_percent" />
                            </div>
                            {{-- Markup Value (Calculated) --}}
                            <div class="md:col-span-3">
                                <x-mary-input id="pow_item_form_indirect_{{ $key }}_value" dense label="Markup Value"
                                    value="{{ number_format($indirectCostData[$key . '_markup_value'] ?? 0, 2) }}" readonly
                                    class="bg-gray-100" />
                            </div>
                        </div>
                    @endforeach
                    {{-- Total Indirect Cost Display --}}
                    <div class="text-right font-semibold mt-2 pt-2 border-t">
                        Total Indirect Cost: {{ number_format($indirectSubtotal, 2) }}
                    </div>
                </div>
            </div>

            {{-- Section 4: Grand Total --}}
            <div class="p-4 border rounded-lg shadow-sm bg-gray-100 text-right">
                <span class="text-lg font-bold text-gray-800">
                    Grand Total (Direct + Indirect): {{ number_format($grandTotal, 2) }}
                </span>
            </div>

            {{-- Form Actions in Footer --}}
            <div class="flex justify-end space-x-3 pt-4">
                {{-- Use cancelForm method here --}}
                <x-mary-button label="Cancel" wire:click="cancelForm" class="btn-ghost" />
                <x-mary-button label="{{ $powId ? 'Update Item' : 'Save Item' }}" type="submit" class="btn-primary"
                    spinner="save" />
            </div>
        </form> {{-- End of form --}}

    </div>

</div>