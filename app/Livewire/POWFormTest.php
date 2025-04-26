<?php

namespace App\Livewire;

use App\Models\IndividualProgramOfWork;
use App\Models\PowMaterialCost;
use App\Models\PowLaborCost;
use App\Models\PowEquipmentCost;
use App\Models\PowIndirectCost;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Carbon\Carbon;

class POWFormTest extends Component
{

    // --- Received Properties ---
    public $projectId;
    public $powId = null; // ID of the POW item being edited, null for new

    // --- Form Properties ---
    public $item_number = '';
    public $work_description = '';
    public $item_description = '';
    public $quantity = 1;
    public $quantity_unit = '';
    public $adjusted_unit_cost = 0;
    public $total_item_cost = 0;
    public $start_date = '';
    public $duration = 1;
    public $progress = 0;

    // Form Subtotals (Calculated)
    public $materialSubtotal = 0;
    public $laborSubtotal = 0;
    public $equipmentSubtotal = 0;
    public $indirectSubtotal = 0;
    public $grandTotal = 0;

    // Form Arrays for Costs
    public $materialCosts = [];
    public $laborCosts = [];
    public $equipmentCosts = [];

    // Form Array/Object for Indirect Costs
    public $indirectCostData = [];

    // --- Component State ---
    public $isFormVisible = true; // Controls the visibility of the form container itself

    /**
     * Initialize the component, load data if editing, reset if creating.
     */
    public function mount($projectId, $powId = null)
    {
        $this->projectId = $projectId;
        $this->powId = $powId;

        if ($this->powId) {
            $this->loadPowItemData($this->powId);
        } else {
            $this->resetForm();
        }
    }

    /**
     * Reset form properties to default/empty state.
     */
    protected function resetForm()
    {
        $this->powId = null;
        $this->item_number = '';
        $this->work_description = '';
        $this->item_description = '';
        $this->quantity = 1;
        $this->quantity_unit = '';
        $this->start_date = '';
        $this->duration = 1;
        $this->progress = 0;

        $this->materialCosts = [['description' => '', 'quantity' => 1, 'unit' => '', 'price' => 0, 'cost' => 0]];
        $this->laborCosts = [['description' => '', 'number_of_manpower' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];
        $this->equipmentCosts = [['description' => '', 'number_of_units' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];

        // Initialize indirect cost data with defaults (Base costs start at 0 for manual input)
        $this->indirectCostData = [
            'b1_description' => 'Overhead Expenses',
            'b1_base_cost' => 0, // Manual Input
            'b1_markup_percent' => 7.00,
            'b1_markup_value' => 0,
            'b2_description' => 'Contingencies',
            'b2_base_cost' => 0, // Manual Input
            'b2_markup_percent' => 2.00,
            'b2_markup_value' => 0,
            'b3_description' => 'Miscellaneous',
            'b3_base_cost' => 0, // Manual Input
            'b3_markup_percent' => 1.00,
            'b3_markup_value' => 0,
            'b4_description' => "Contractor's Profit",
            'b4_base_cost' => 0, // Manual Input
            'b4_markup_percent' => 8.00,
            'b4_markup_value' => 0,
            'b5_description' => 'VAT Component',
            'b5_base_cost' => 0, // Manual Input (assuming this is also manual now)
            'b5_markup_percent' => 5.00,
            'b5_markup_value' => 0,
        ];

        $this->calculateAllCosts();
        $this->resetErrorBag();
    }

    /**
     * Load data for a specific POW item into the form properties.
     */
    protected function loadPowItemData($id)
    {
        try {
            $pow = IndividualProgramOfWork::with([
                'materialCosts',
                'laborCosts',
                'equipmentCosts',
                'indirectCost'
            ])->findOrFail($id);

            if ($pow->project_id != $this->projectId) {
                throw new Exception("POW item does not belong to this project.");
            }

            $this->powId = $pow->id;
            $this->item_number = $pow->item_number;
            $this->work_description = $pow->work_description;
            $this->item_description = $pow->item_description;
            $this->quantity = $pow->quantity;
            $this->quantity_unit = $pow->quantity_unit;
            $this->start_date = $pow->start_date ? $pow->start_date->format('Y-m-d') : null;
            $this->duration = $pow->duration;
            $this->progress = $pow->progress;

            $this->materialCosts = $pow->materialCosts->toArray();
            $this->laborCosts = $pow->laborCosts->toArray();
            $this->equipmentCosts = $pow->equipmentCosts->toArray();

            if (empty($this->materialCosts))
                $this->materialCosts = [['description' => '', 'quantity' => 1, 'unit' => '', 'price' => 0, 'cost' => 0]];
            if (empty($this->laborCosts))
                $this->laborCosts = [['description' => '', 'number_of_manpower' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];
            if (empty($this->equipmentCosts))
                $this->equipmentCosts = [['description' => '', 'number_of_units' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];


            if ($pow->indirectCost) {
                $this->indirectCostData = $pow->indirectCost->toArray();
                unset($this->indirectCostData['id'], $this->indirectCostData['individual_program_of_work_id'], $this->indirectCostData['created_at'], $this->indirectCostData['updated_at']);
            } else {
                // Re-initialize if no indirect cost record exists, keeping manual base cost structure
                $this->indirectCostData = [
                    'b1_description' => 'Overhead Expenses',
                    'b1_base_cost' => 0,
                    'b1_markup_percent' => 7.00,
                    'b1_markup_value' => 0,
                    'b2_description' => 'Contingencies',
                    'b2_base_cost' => 0,
                    'b2_markup_percent' => 2.00,
                    'b2_markup_value' => 0,
                    'b3_description' => 'Miscellaneous',
                    'b3_base_cost' => 0,
                    'b3_markup_percent' => 1.00,
                    'b3_markup_value' => 0,
                    'b4_description' => "Contractor's Profit",
                    'b4_base_cost' => 0,
                    'b4_markup_percent' => 8.00,
                    'b4_markup_value' => 0,
                    'b5_description' => 'VAT Component',
                    'b5_base_cost' => 0,
                    'b5_markup_percent' => 5.00,
                    'b5_markup_value' => 0,
                ];
            }

            $this->calculateAllCosts();
            $this->resetErrorBag();

        } catch (Exception $e) {
            Log::error("Error loading POW item data for ID {$id} in form component: " . $e->getMessage());
            session()->flash('form_error', 'Could not load item data. ' . $e->getMessage());
            $this->cancelForm();
        }
    }

    // Rules for validation
    protected function rules()
    {
        return [
            'projectId' => 'required|exists:projects,id',
            'item_number' => 'nullable|string|max:50',
            'work_description' => 'nullable|string',
            'item_description' => 'required|string',
            'quantity' => 'required|numeric|min:0|not_in:0',
            'quantity_unit' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'duration' => 'required|integer|min:1',
            'progress' => 'required|integer|min:0|max:100',

            // Material Costs Validation
            'materialCosts' => 'required|array|min:1',
            'materialCosts.*.description' => 'required|string|max:255',
            'materialCosts.*.quantity' => 'required|numeric|min:0',
            'materialCosts.*.unit' => 'nullable|string|max:50',
            'materialCosts.*.price' => 'required|numeric|min:0',

            // Labor Costs Validation
            'laborCosts' => 'required|array|min:1',
            'laborCosts.*.description' => 'required|string|max:255',
            'laborCosts.*.number_of_manpower' => 'required|numeric|min:0',
            'laborCosts.*.number_of_days' => 'required|numeric|min:0',
            'laborCosts.*.rate_per_day' => 'required|numeric|min:0',

            // Equipment Costs Validation
            'equipmentCosts' => 'required|array|min:1',
            'equipmentCosts.*.description' => 'required|string|max:255',
            'equipmentCosts.*.number_of_units' => 'required|numeric|min:0',
            'equipmentCosts.*.number_of_days' => 'required|numeric|min:0',
            'equipmentCosts.*.rate_per_day' => 'required|numeric|min:0',

            // Indirect Costs Validation (Base Costs are now required numeric)
            'indirectCostData.b1_base_cost' => 'required|numeric|min:0', // Changed
            'indirectCostData.b1_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b2_base_cost' => 'required|numeric|min:0', // Changed
            'indirectCostData.b2_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b3_base_cost' => 'required|numeric|min:0', // Changed
            'indirectCostData.b3_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b4_base_cost' => 'required|numeric|min:0', // Changed
            'indirectCostData.b4_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b5_base_cost' => 'required|numeric|min:0', // Changed
            'indirectCostData.b5_markup_percent' => 'required|numeric|min:0',
        ];
    }

    // Custom validation messages
    protected $messages = [
        'materialCosts.required' => 'At least one material cost item is required.',
        'laborCosts.required' => 'At least one labor cost item is required.',
        'equipmentCosts.required' => 'At least one equipment cost item is required.',
        'materialCosts.*.description.required' => 'Material description is required.',
        'materialCosts.*.quantity.required' => 'Material quantity is required.',
        'materialCosts.*.price.required' => 'Material price is required.',
        'quantity.not_in' => 'Quantity cannot be zero.',
        // Add messages for indirect base costs if needed
        'indirectCostData.*.base_cost.required' => 'Indirect base cost is required.',
        'indirectCostData.*.base_cost.numeric' => 'Indirect base cost must be a number.',
        'indirectCostData.*.base_cost.min' => 'Indirect base cost must be at least 0.',
    ];

    /** Add cost row */
    public function addCostRow($type, $index)
    { /* ... (no changes needed) ... */
        $newRow = [];
        switch ($type) {
            case 'materialCosts':
                $newRow = ['description' => '', 'quantity' => 1, 'unit' => '', 'price' => 0, 'cost' => 0];
                break;
            case 'laborCosts':
                $newRow = ['description' => '', 'number_of_manpower' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0];
                break;
            case 'equipmentCosts':
                $newRow = ['description' => '', 'number_of_units' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0];
                break;
        }
        if (!empty($newRow)) {
            array_splice($this->{$type}, $index + 1, 0, [$newRow]);
        }
    }

    /** Remove cost row */
    public function removeCostRow($type, $index)
    { /* ... (no changes needed) ... */
        if (count($this->{$type}) > 1) {
            unset($this->{$type}[$index]);
            $this->{$type} = array_values($this->{$type});
            $this->calculateAllCosts(); // Recalculate after removal
        } else {
            session()->flash('form_error', 'At least one row is required for ' . str_replace('Costs', '', $type) . ' costs.');
        }
    }

    /** Livewire updated hook */
    public function updated($propertyName)
    {
        // This check already includes indirectCostData changes (including base_cost)
        if (
            str_contains($propertyName, 'materialCosts.') ||
            str_contains($propertyName, 'laborCosts.') ||
            str_contains($propertyName, 'equipmentCosts.') ||
            str_contains($propertyName, 'indirectCostData.') || // Catches base_cost and markup_percent changes
            $propertyName === 'quantity'
        ) {
            $this->calculateAllCosts();
        }
    }

    /**
     * Calculate all costs.
     * MODIFIED: Uses manual base costs for indirect calculations.
     */
    public function calculateAllCosts()
    {
        // 1. Calculate Direct Costs (No changes here)
        $this->materialSubtotal = 0;
        foreach ($this->materialCosts as &$cost) {
            $cost['cost'] = ($cost['quantity'] ?? 0) * ($cost['price'] ?? 0);
            $this->materialSubtotal += $cost['cost'];
        }
        unset($cost);

        $this->laborSubtotal = 0;
        foreach ($this->laborCosts as &$cost) {
            $cost['cost'] = ($cost['number_of_manpower'] ?? 0) * ($cost['number_of_days'] ?? 0) * ($cost['rate_per_day'] ?? 0);
            $this->laborSubtotal += $cost['cost'];
        }
        unset($cost);

        $this->equipmentSubtotal = 0;
        foreach ($this->equipmentCosts as &$cost) {
            $cost['cost'] = ($cost['number_of_units'] ?? 0) * ($cost['number_of_days'] ?? 0) * ($cost['rate_per_day'] ?? 0);
            $this->equipmentSubtotal += $cost['cost'];
        }
        unset($cost);

        // 2. Calculate Direct Cost Total
        $directCostTotal = $this->materialSubtotal + $this->laborSubtotal + $this->equipmentSubtotal;

        // 3. Calculate Indirect Costs using MANUAL Base Costs
        $this->indirectCostData['b1_markup_value'] = ($this->indirectCostData['b1_base_cost'] ?? 0) * (($this->indirectCostData['b1_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b2_markup_value'] = ($this->indirectCostData['b2_base_cost'] ?? 0) * (($this->indirectCostData['b2_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b3_markup_value'] = ($this->indirectCostData['b3_base_cost'] ?? 0) * (($this->indirectCostData['b3_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b4_markup_value'] = ($this->indirectCostData['b4_base_cost'] ?? 0) * (($this->indirectCostData['b4_markup_percent'] ?? 0) / 100);
        // Assuming VAT base cost is also manual now
        $this->indirectCostData['b5_markup_value'] = ($this->indirectCostData['b5_base_cost'] ?? 0) * (($this->indirectCostData['b5_markup_percent'] ?? 0) / 100);


        // 4. Calculate Indirect Subtotal (Sum of calculated markup values)
        $this->indirectSubtotal = ($this->indirectCostData['b1_markup_value'] ?? 0)
            + ($this->indirectCostData['b2_markup_value'] ?? 0)
            + ($this->indirectCostData['b3_markup_value'] ?? 0)
            + ($this->indirectCostData['b4_markup_value'] ?? 0)
            + ($this->indirectCostData['b5_markup_value'] ?? 0);

        // 5. Calculate Grand Total (Direct Cost + Indirect Subtotal)
        $this->grandTotal = $directCostTotal + $this->indirectSubtotal;

        // 6. Calculate Adjusted Unit Cost (Direct Cost / Quantity) and Total Item Cost (Grand Total)
        $currentQuantity = $this->quantity ?? 0;
        if (is_numeric($currentQuantity) && $currentQuantity > 0) {
            // Adjusted Unit Cost reflects only the direct cost per unit
            $this->adjusted_unit_cost = $directCostTotal / $currentQuantity;
            // Total Item Cost reflects the final Grand Total for the whole quantity
            $this->total_item_cost = $this->grandTotal;
        } else {
            $this->adjusted_unit_cost = 0;
            $this->total_item_cost = 0;
        }
    }

    /** Save the form */
    public function save()
    {
        $this->validate();
        $this->calculateAllCosts(); // Final calculation

        DB::beginTransaction();
        try {
            // Prepare main POW data (includes calculated totals/subtotals)
            $powData = [
                'project_id' => $this->projectId,
                'item_number' => $this->item_number,
                'work_description' => $this->work_description,
                'item_description' => $this->item_description,
                'quantity' => $this->quantity,
                'quantity_unit' => $this->quantity_unit,
                'adjusted_unit_cost' => $this->adjusted_unit_cost, // Direct cost per unit
                'total_item_cost' => $this->total_item_cost,       // Grand total
                'start_date' => $this->start_date ?: null,
                'duration' => $this->duration,
                'progress' => $this->progress,
                'material_subtotal' => $this->materialSubtotal,
                'labor_subtotal' => $this->laborSubtotal,
                'equipment_subtotal' => $this->equipmentSubtotal,
                'indirect_subtotal' => $this->indirectSubtotal, // Sum of markups
                'grand_total' => $this->grandTotal,
            ];

            // Create or Update main POW item
            if ($this->powId) {
                $pow = IndividualProgramOfWork::findOrFail($this->powId);
                $pow->update($powData);
                // Delete existing costs before re-adding
                $pow->materialCosts()->delete();
                $pow->laborCosts()->delete();
                $pow->equipmentCosts()->delete();
                $pow->indirectCost()->delete();
            } else {
                $pow = IndividualProgramOfWork::create($powData);
            }

            // Save related costs
            $filteredMaterials = $this->filterEmptyCostRows($this->materialCosts, ['description', 'price']);
            if (!empty($filteredMaterials))
                $pow->materialCosts()->createMany($filteredMaterials);

            $filteredLabor = $this->filterEmptyCostRows($this->laborCosts, ['description', 'rate_per_day']);
            if (!empty($filteredLabor))
                $pow->laborCosts()->createMany($filteredLabor);

            $filteredEquipment = $this->filterEmptyCostRows($this->equipmentCosts, ['description', 'rate_per_day']);
            if (!empty($filteredEquipment))
                $pow->equipmentCosts()->createMany($filteredEquipment);

            // Save Indirect Costs (includes the manual base costs now)
            $pow->indirectCost()->updateOrCreate(
                ['individual_program_of_work_id' => $pow->id],
                $this->indirectCostData
            );

            DB::commit();
            $this->dispatch('powItemSaved');
            redirect(route('project-view', ['id'=> $this->projectId]));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saving POW item in form component: ' . $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            session()->flash('form_error', 'An error occurred while saving: ' . $e->getMessage());
        }
    }

    /** Filter empty cost rows */
    private function filterEmptyCostRows(array $costs, array $checkFields): array
    { /* ... (no changes needed) ... */
        return array_filter($costs, function ($cost) use ($checkFields) {
            foreach ($checkFields as $field) {
                if ($field === 'description') {
                    if (trim($cost[$field] ?? '') !== '')
                        return true; // Keep if description has text
                } elseif (!empty($cost[$field]) || (is_numeric($cost[$field]) && $cost[$field] != 0)) {
                    return true; // Keep if other check fields have non-zero value
                }
            }
            return false; // Discard if all check fields are empty/zero (and description is empty)
        });
    }

    /** Cancel the form */
    public function cancelForm()
    {
        // $this->isFormVisible = false;
        // $this->dispatch('formCancelled');
        redirect(route('project-view', ['id'=> $this->projectId]));
    }

    public function render()
    {
        return view('livewire.p-o-w-form-test');
    }
}
