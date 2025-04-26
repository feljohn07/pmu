<?php

namespace App\Livewire;

use App\Models\IndividualProgramOfWork;
use App\Models\Project;
use Exception;
use Livewire\Component;
use Log;

class POWReportTest extends Component
{
    public $powId;
    public $powItem = null; // The specific POW item with its relations
    public ?Project $project = null; // The related project

    /**
     * Mount the component and load the POW data.
     *
     * @param int $powId The ID of the IndividualProgramOfWork item to display.
     */
    public function mount($powId)
    {
        $this->powId = $powId;
        $this->loadPowData();
    }

    /**
     * Load the Program of Work item data and its related project.
     */
    public function loadPowData()
    {
        try {
            $this->powItem = IndividualProgramOfWork::with([
                'project', // Load the related project
                'materialCosts',
                'laborCosts',
                'equipmentCosts',
                'indirectCost'
            ])->findOrFail($this->powId);

            $this->project = $this->powItem->project; // Assign the loaded project

            // dd($this->project);

        } catch (Exception $e) {
            Log::error("Error loading POW item data for report (ID: {$this->powId}): " . $e->getMessage());
            // Optionally flash a message or handle the error in the view
            session()->flash('report_error', 'Could not load Program of Work item details.');
            $this->powItem = null; // Ensure powItem is null if loading fails
            $this->project = null;
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        // Calculate derived values needed for the view if not directly stored
        // Although most seem to be stored directly on powItem from the previous form component

        $totalDirectCost = ($this->powItem->material_subtotal ?? 0)
            + ($this->powItem->labor_subtotal ?? 0)
            + ($this->powItem->equipment_subtotal ?? 0);

        $finalAdjustedUnitCost = 0;
        if ($this->powItem && ($this->powItem->quantity ?? 0) > 0) {
            $finalAdjustedUnitCost = ($this->powItem->grand_total ?? 0) / $this->powItem->quantity;
        }


        return view('livewire.p-o-w-report-test', [
            'totalDirectCost' => $totalDirectCost,
            'finalAdjustedUnitCost' => $finalAdjustedUnitCost,
        ]);
    }
}
