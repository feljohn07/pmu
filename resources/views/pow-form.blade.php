{{-- resources/views/projects/pow/create.blade.php --}}

{{-- Assuming you have a main layout file, extend it --}}
{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Program of Work Item for Project: {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Add any specific custom styles if needed */
        label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }
        input[type=text], input[type=number], input[type=date], select, textarea {
            @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm;
        }
        .form-section {
            @apply mt-6 p-4 border border-gray-200 rounded-md shadow-sm bg-white;
        }
        .section-title {
            @apply text-lg font-semibold mb-4 border-b pb-2 text-gray-800;
        }
        .error-message {
            @apply text-red-500 text-xs mt-1;
        }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">

<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-gray-900">Create New Program of Work Item</h1>
    <p class="mb-4 text-gray-600">Project: <span class="font-semibold">{{ $project->name }}</span></p>

    {{-- Form starts here --}}
    {{-- Replace '#' with your actual store route, e.g., route('projects.pow.store', $project) --}}
    <form method="POST" action="{{ route('projects.pow.store', $project->id) }}" class="space-y-6">
        @csrf {{-- CSRF Protection --}}

        {{-- Hidden field for project_id --}}
        <input type="hidden" name="project_id" value="{{ $project->id }}">

        {{-- Section 1: POW Item Details --}}
        <div class="form-section">
            <h2 class="section-title">Item Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="item_no">Item No.</label>
                    <input type="text" name="item_no" id="item_no" value="{{ old('item_no') }}" required class="@error('item_no') border-red-500 @enderror">
                    @error('item_no')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="work_description">Work Description</label>
                    <input type="text" name="work_description" id="work_description" value="{{ old('work_description') }}" required class="@error('work_description') border-red-500 @enderror">
                    @error('work_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="item_description">Item Description</label>
                    <textarea name="item_description" id="item_description" rows="3" class="@error('item_description') border-red-500 @enderror">{{ old('item_description') }}</textarea>
                    @error('item_description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1.00) }}" required step="0.01" min="0" class="@error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="quantity_unit">Quantity Unit</label>
                    <input type="text" name="quantity_unit" id="quantity_unit" value="{{ old('quantity_unit', 'l.s.') }}" required class="@error('quantity_unit') border-red-500 @enderror">
                     @error('quantity_unit')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="duration_days">Duration (Calendar Days)</label>
                    <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days') }}" step="0.01" min="0" class="@error('duration_days') border-red-500 @enderror">
                    @error('duration_days')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desirable_start_date">Desirable Start Date</label>
                    <input type="date" name="desirable_start_date" id="desirable_start_date" value="{{ old('desirable_start_date') }}" class="@error('desirable_start_date') border-red-500 @enderror">
                     @error('desirable_start_date')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    {{-- Or handle 'Upon Approval' status differently if needed --}}
                </div>
            </div>
        </div>

        {{-- Section 2: Direct Costs (Subtotals) --}}
        {{-- Note: For detailed entry (materials, labor, equip), you'd need dynamic JS or separate forms --}}
        <div class="form-section">
             <h2 class="section-title">Direct Costs (Subtotals)</h2>
             <p class="text-sm text-gray-600 mb-4">Enter the subtotal for each direct cost category. Detailed breakdowns can be added later or managed separately.</p>
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <div>
                    <label for="direct_cost_materials_subtotal">Materials Subtotal (Php)</label>
                    <input type="number" name="direct_cost_materials_subtotal" id="direct_cost_materials_subtotal" value="{{ old('direct_cost_materials_subtotal') }}" step="0.01" min="0" class="@error('direct_cost_materials_subtotal') border-red-500 @enderror">
                    @error('direct_cost_materials_subtotal')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="direct_cost_labor_subtotal">Labor Subtotal (Php)</label>
                    <input type="number" name="direct_cost_labor_subtotal" id="direct_cost_labor_subtotal" value="{{ old('direct_cost_labor_subtotal') }}" step="0.01" min="0" class="@error('direct_cost_labor_subtotal') border-red-500 @enderror">
                    @error('direct_cost_labor_subtotal')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="direct_cost_equipment_subtotal">Equipment Subtotal (Php)</label>
                    <input type="number" name="direct_cost_equipment_subtotal" id="direct_cost_equipment_subtotal" value="{{ old('direct_cost_equipment_subtotal') }}" step="0.01" min="0" class="@error('direct_cost_equipment_subtotal') border-red-500 @enderror">
                     @error('direct_cost_equipment_subtotal')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Unit Cost might be derived or entered --}}
                 <div class="md:col-span-3">
                    <label for="unit_cost">Unit Cost (Php)</label>
                    <input type="number" name="unit_cost" id="unit_cost" value="{{ old('unit_cost') }}" step="0.01" min="0" class="@error('unit_cost') border-red-500 @enderror" placeholder="Usually Total Direct Cost if quantity is 1">
                     @error('unit_cost')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                     <p class="text-xs text-gray-500 mt-1">This is often the same as the Total Direct Cost, especially if the quantity is 1 lot/unit.</p>
                </div>
             </div>
        </div>

        {{-- Section 3: Indirect Costs (Percentages) --}}
        {{-- The values will be calculated server-side based on these percentages --}}
        <div class="form-section">
            <h2 class="section-title">Indirect Costs (Mark-up Percentages)</h2>
            <p class="text-sm text-gray-600 mb-4">Enter the mark-up percentages. The actual cost values will be calculated upon saving.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                 <div>
                    <label for="overhead_percentage">Overhead (%)</label>
                    <input type="number" name="overhead_percentage" id="overhead_percentage" value="{{ old('overhead_percentage') }}" step="0.01" min="0" max="100" placeholder="e.g., 7" class="@error('overhead_percentage') border-red-500 @enderror">
                    @error('overhead_percentage')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contingencies_percentage">Contingencies (%)</label>
                    <input type="number" name="contingencies_percentage" id="contingencies_percentage" value="{{ old('contingencies_percentage') }}" step="0.01" min="0" max="100" placeholder="e.g., 2" class="@error('contingencies_percentage') border-red-500 @enderror">
                     @error('contingencies_percentage')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="miscellaneous_percentage">Miscellaneous (%)</label>
                    <input type="number" name="miscellaneous_percentage" id="miscellaneous_percentage" value="{{ old('miscellaneous_percentage') }}" step="0.01" min="0" max="100" placeholder="e.g., 1" class="@error('miscellaneous_percentage') border-red-500 @enderror">
                     @error('miscellaneous_percentage')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contractor_profit_percentage">Contractor's Profit (%)</label>
                    <input type="number" name="contractor_profit_percentage" id="contractor_profit_percentage" value="{{ old('contractor_profit_percentage') }}" step="0.01" min="0" max="100" placeholder="e.g., 8 or 10" class="@error('contractor_profit_percentage') border-red-500 @enderror">
                    @error('contractor_profit_percentage')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                 <div>
                    <label for="vat_percentage">VAT (%)</label>
                    <input type="number" name="vat_percentage" id="vat_percentage" value="{{ old('vat_percentage') }}" step="0.01" min="0" max="100" placeholder="e.g., 5" class="@error('vat_percentage') border-red-500 @enderror">
                     @error('vat_percentage')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

         {{-- Section 4: Final Costs (Optional Input) --}}
         <div class="form-section">
            <h2 class="section-title">Final Costs (Optional Override)</h2>
            <p class="text-sm text-gray-600 mb-4">These costs are typically calculated automatically. Only enter values here if you need to manually override the calculated totals.</p>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                    <label for="adjusted_unit_cost">Adjusted Unit Cost (Php)</label>
                    <input type="number" name="adjusted_unit_cost" id="adjusted_unit_cost" value="{{ old('adjusted_unit_cost') }}" step="0.01" min="0" placeholder="Leave blank for auto-calculation" class="@error('adjusted_unit_cost') border-red-500 @enderror">
                     @error('adjusted_unit_cost')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                     <p class="text-xs text-gray-500 mt-1">Overrides the final calculated unit cost if provided.</p>
                </div>
                {{-- Total Item Cost is calculated, no input needed usually --}}
             </div>
         </div>


        {{-- Submit Button --}}
        <div class="flex justify-end mt-8">
            <a href="{{ route('projects.show', $project->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Save Program of Work Item
            </button>
        </div>
    </form>
</div>

</body>
</html>
{{-- @endsection --}}
