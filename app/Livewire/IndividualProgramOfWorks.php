<?php

namespace App\Livewire;

use App\Models\IndividualProgramOfWork;
use App\Models\PowMaterialCost;
use App\Models\PowLaborCost;
use App\Models\PowEquipmentCost;
use App\Models\PowIndirectCost;
use App\Models\Project; // Assuming Project model exists
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Exception;
use Carbon\Carbon;

class IndividualProgramOfWorks extends Component
{
    // Properties for the main view (List of POW items)
    public $projectId;
    public $project; // Type hint for clarity
    public $programOfWorks = []; // Collection of POW items for the project

    // Properties to control the modal form
    public $showFullScreenForm = true;
    public $powId = null; // ID of the POW item being edited, null for new

    // --- Form Properties (Merged from IndividualPowForm) ---
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
    public $indirectCostData = []; // Initialized in resetForm/loadPowItemData

    // -------------------------------------------------------------
    // Lifecycle Hooks & Main View Logic
    // -------------------------------------------------------------

    public function mount($id)
    {
        $this->projectId = $id;
        $this->loadProjectData(); // Load project details and POW list
        $this->resetForm();       // Initialize form properties
    }

    public function loadProjectData()
    {
        try {
            $this->project = Project::findOrFail($this->projectId);
            // Load the list of POW items for display
            $this->programOfWorks = IndividualProgramOfWork::where('project_id', $this->projectId)
                ->orderBy('item_number') // Or desired order
                ->get();

            // dd($this->programOfWorks);
        } catch (Exception $e) {
            Log::error("Error loading project data for ID {$this->projectId}: " . $e->getMessage());
            session()->flash('error', 'Could not load project data.');
            // Potentially redirect if project not found
            // return redirect()->route('some.error.route');
            $this->programOfWorks = []; // Ensure it's an empty array on error
        }
    }

    public function render()
    {
        // The view will contain both the list and the modal form structure
        return view('livewire.individual-program-of-works');
    }

    // -------------------------------------------------------------
    // Modal Form Control Logic
    // -------------------------------------------------------------

    public function createPowItem()
    {
        // $this->resetForm(); // Clear form for new entry
        // $this->showFullScreenForm = true;
        redirect(route('create-pow', [$this->projectId]));
    }

    public function editPowItem($powIdToEdit)
    {
        // $this->loadPowItemData($powIdToEdit); // Load data into form properties
        // $this->showFullScreenForm = true;

        redirect(route('edit-pow', [$this->projectId, $powIdToEdit]));
    }

    public function closeFormModal()
    {
        $this->showFullScreenForm = false;
        $this->resetForm(); // Clear form data when closing
    }

    // -------------------------------------------------------------
    // Form Data Handling & Validation (Merged from IndividualPowForm)
    // -------------------------------------------------------------

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

        // Initialize cost arrays with one empty row
        $this->materialCosts = [['description' => '', 'quantity' => 1, 'unit' => '', 'price' => 0, 'cost' => 0]];
        $this->laborCosts = [['description' => '', 'number_of_manpower' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];
        $this->equipmentCosts = [['description' => '', 'number_of_units' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];

        // Initialize indirect cost data with defaults
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

        $this->calculateAllCosts(); // Calculate initial zero values
        $this->resetErrorBag(); // Clear validation errors
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

            // Security check: Ensure item belongs to the current project
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

            // Ensure at least one row exists for editing UI
            if (empty($this->materialCosts))
                $this->materialCosts = [['description' => '', 'quantity' => 1, 'unit' => '', 'price' => 0, 'cost' => 0]];
            if (empty($this->laborCosts))
                $this->laborCosts = [['description' => '', 'number_of_manpower' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];
            if (empty($this->equipmentCosts))
                $this->equipmentCosts = [['description' => '', 'number_of_units' => 1, 'number_of_days' => 1, 'rate_per_day' => 0, 'cost' => 0]];


            if ($pow->indirectCost) {
                $this->indirectCostData = $pow->indirectCost->toArray();
                // Remove keys not needed in the form
                unset($this->indirectCostData['id'], $this->indirectCostData['individual_program_of_work_id'], $this->indirectCostData['created_at'], $this->indirectCostData['updated_at']);
            } else {
                // Re-initialize if no indirect cost record exists (shouldn't happen if saved correctly)
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

            $this->calculateAllCosts(); // Calculate costs based on loaded data
            $this->resetErrorBag();

        } catch (Exception $e) {
            Log::error("Error loading POW item data for ID {$id}: " . $e->getMessage());
            session()->flash('error', 'Could not load item data. ' . $e->getMessage());
            $this->closeFormModal(); // Close modal on error
        }
    }


    // Rules for validation
    protected function rules()
    {
        // Rules remain the same as in IndividualPowForm
        return [
            'projectId' => 'required|exists:projects,id', // Keep this check
            'item_number' => 'nullable|string|max:50',
            'work_description' => 'nullable|string',
            'item_description' => 'required|string',
            'quantity' => 'required|numeric|min:0|not_in:0', // Cannot be zero for unit cost calculation
            'quantity_unit' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'duration' => 'required|integer|min:1',
            'progress' => 'required|integer|min:0|max:100',

            // Material Costs Validation
            'materialCosts' => 'required|array|min:1', // Ensure at least one material row
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

            // Indirect Costs Validation
            'indirectCostData.b1_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b2_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b3_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b4_markup_percent' => 'required|numeric|min:0',
            'indirectCostData.b5_markup_percent' => 'required|numeric|min:0',
        ];
    }

    // Custom validation messages
    protected $messages = [
        // Messages remain the same
        'materialCosts.required' => 'At least one material cost item is required.',
        'laborCosts.required' => 'At least one labor cost item is required.',
        'equipmentCosts.required' => 'At least one equipment cost item is required.',
        'materialCosts.*.description.required' => 'Material description is required.',
        'materialCosts.*.quantity.required' => 'Material quantity is required.',
        'materialCosts.*.price.required' => 'Material price is required.',
        'quantity.not_in' => 'Quantity cannot be zero.',
        // Add more specific messages as needed
    ];


    /**
     * Add a new row to a specific cost type array.
     */
    public function addCostRow($type, $index)
    {
        // Method remains the same
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

    /**
     * Remove a row from a specific cost type array.
     */
    public function removeCostRow($type, $index)
    {
        // Method remains the same, maybe add a check for minimum 1 row
        if (count($this->{$type}) > 1) {
            unset($this->{$type}[$index]);
            $this->{$type} = array_values($this->{$type});
            $this->calculateAllCosts(); // Recalculate after removal
        } else {
            // Optionally prevent removing the last row
            session()->flash('form_error', 'At least one row is required for ' . str_replace('Costs', '', $type) . ' costs.');
        }
    }

    /**
     * Livewire lifecycle hook for updates.
     */
    public function updated($propertyName)
    {
        // Logic remains the same
        if (
            str_contains($propertyName, 'materialCosts.') ||
            str_contains($propertyName, 'laborCosts.') ||
            str_contains($propertyName, 'equipmentCosts.') ||
            str_contains($propertyName, 'indirectCostData.') ||
            $propertyName === 'quantity'
        ) {
            // Add debounce here if calculations become slow on rapid input
            // $this->debounce('calculateAllCosts', 300);
            $this->calculateAllCosts();
        }
    }

    /**
     * Calculate all costs.
     */
    public function calculateAllCosts()
    {
        // Calculation logic remains exactly the same as in IndividualPowForm
        // 1. Calculate individual direct cost lines and subtotals
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

        // 3. Calculate Indirect Costs
        $this->indirectCostData['b1_base_cost'] = $directCostTotal;
        $this->indirectCostData['b1_markup_value'] = $directCostTotal * (($this->indirectCostData['b1_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b2_base_cost'] = $directCostTotal;
        $this->indirectCostData['b2_markup_value'] = $directCostTotal * (($this->indirectCostData['b2_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b3_base_cost'] = $directCostTotal;
        $this->indirectCostData['b3_markup_value'] = $directCostTotal * (($this->indirectCostData['b3_markup_percent'] ?? 0) / 100);
        $this->indirectCostData['b4_base_cost'] = $directCostTotal;
        $this->indirectCostData['b4_markup_value'] = $directCostTotal * (($this->indirectCostData['b4_markup_percent'] ?? 0) / 100);
        $baseForVat = $directCostTotal + $this->indirectCostData['b1_markup_value'] + $this->indirectCostData['b2_markup_value'] + $this->indirectCostData['b3_markup_value'] + $this->indirectCostData['b4_markup_value'];
        $this->indirectCostData['b5_base_cost'] = $baseForVat;
        $this->indirectCostData['b5_markup_value'] = $baseForVat * (($this->indirectCostData['b5_markup_percent'] ?? 0) / 100);

        // 4. Calculate Indirect Subtotal
        $this->indirectSubtotal = $this->indirectCostData['b1_markup_value'] + $this->indirectCostData['b2_markup_value'] + $this->indirectCostData['b3_markup_value'] + $this->indirectCostData['b4_markup_value'] + $this->indirectCostData['b5_markup_value'];

        // 5. Calculate Grand Total
        $this->grandTotal = $directCostTotal + $this->indirectSubtotal;

        // 6. Calculate Adjusted Unit Cost and Total Item Cost
        $currentQuantity = $this->quantity ?? 0; // Ensure quantity is treated as numeric
        if (is_numeric($currentQuantity) && $currentQuantity > 0) {
            $this->adjusted_unit_cost = $directCostTotal / $currentQuantity;
            $this->total_item_cost = $this->grandTotal; // Total Item Cost = Grand Total
        } else {
            $this->adjusted_unit_cost = 0;
            $this->total_item_cost = 0;
        }
    }

    /**
     * Save the Individual Program of Work item and its associated costs.
     */
    public function save()
    {
        $this->validate();
        $this->calculateAllCosts(); // Final calculation

        DB::beginTransaction();
        try {
            // Prepare main POW data
            $powData = [
                'project_id' => $this->projectId,
                'item_number' => $this->item_number,
                'work_description' => $this->work_description,
                'item_description' => $this->item_description,
                'quantity' => $this->quantity,
                'quantity_unit' => $this->quantity_unit,
                'adjusted_unit_cost' => $this->adjusted_unit_cost,
                'total_item_cost' => $this->total_item_cost,
                'start_date' => $this->start_date ?: null, // Handle empty date
                'duration' => $this->duration,
                'progress' => $this->progress,
                'material_subtotal' => $this->materialSubtotal,
                'labor_subtotal' => $this->laborSubtotal,
                'equipment_subtotal' => $this->equipmentSubtotal,
                'indirect_subtotal' => $this->indirectSubtotal,
                'grand_total' => $this->grandTotal,
            ];

            // Create or Update main POW item
            if ($this->powId) {
                $pow = IndividualProgramOfWork::findOrFail($this->powId);
                $pow->update($powData);
                // Delete existing costs
                $pow->materialCosts()->delete();
                $pow->laborCosts()->delete();
                $pow->equipmentCosts()->delete();
                $pow->indirectCost()->delete(); // Use relation for hasOne
            } else {
                $pow = IndividualProgramOfWork::create($powData);
                $this->powId = $pow->id; // Update powId for potential immediate edit
            }

            // Save related costs using relationships for cleaner code
            $pow->materialCosts()->createMany($this->filterEmptyCostRows($this->materialCosts, ['description', 'price']));
            $pow->laborCosts()->createMany($this->filterEmptyCostRows($this->laborCosts, ['description', 'rate_per_day']));
            $pow->equipmentCosts()->createMany($this->filterEmptyCostRows($this->equipmentCosts, ['description', 'rate_per_day']));

            // Save Indirect Costs (using updateOrCreate for simplicity)
            $pow->indirectCost()->updateOrCreate(
                ['individual_program_of_work_id' => $pow->id], // Condition to find existing
                $this->indirectCostData // Data to update or create
            );


            DB::commit();
            session()->flash('success', 'Program of Work item saved successfully!');
            $this->loadProjectData(); // Refresh the list of POW items
            $this->closeFormModal(); // Close the modal

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error saving POW item: ' . $e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            // Provide more specific feedback if possible (e.g., validation exception)
            session()->flash('error', 'An error occurred while saving: ' . $e->getMessage());
            // Keep the modal open on error for correction
        }
    }

    /**
     * Helper function to filter out cost rows that are essentially empty
     * before attempting to save them to the database.
     *
     * @param array $costs Array of cost data rows.
     * @param array $checkFields Fields to check for non-empty/non-zero values.
     * @return array Filtered array of cost data rows.
     */
    private function filterEmptyCostRows(array $costs, array $checkFields): array
    {
        return array_filter($costs, function ($cost) use ($checkFields) {
            foreach ($checkFields as $field) {
                if (!empty($cost[$field])) { // Check if description is non-empty OR a numeric value is > 0
                    return true; // Keep this row if any check field has a value
                }
            }
            return false; // Discard this row if all check fields are empty/zero
        });
    }

    /**
     * Delete a specific Program of Work item.
     */
    public function deletePowItem($idToDelete)
    {
        // Optional: Add confirmation logic here using JS or a confirmation modal

        DB::beginTransaction();
        try {
            $pow = IndividualProgramOfWork::where('id', $idToDelete)
                ->where('project_id', $this->projectId) // Ensure it belongs to this project
                ->firstOrFail();

            // Deleting the main record will cascade delete related costs due to DB constraints
            $pow->delete();

            DB::commit();
            session()->flash('success', 'Program of Work item deleted successfully!');
            $this->loadProjectData(); // Refresh the list

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error deleting POW item ID {$idToDelete}: " . $e->getMessage());
            session()->flash('error', 'Could not delete the item.');
        }
    }

    public function viewForm($powId) {
        redirect(route('pow-form', [$powId]));
    }

}
