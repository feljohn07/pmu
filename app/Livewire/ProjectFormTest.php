<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectCost;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class ProjectFormTest extends Component
{
    public $category = '';


    public $projectId;
    public $projectName = '';
    public $project_category = '';
    public $appropriation = '';
    public $source_of_funds = '';
    public $duration = '';
    public $start_date = '';
    public $directCost = [];
    public $indirectCost = [];
    public $governmentExpenditures = [];
    public $physicalContingencies = [];

    // Define cost type constants
    const COST_TYPE_DIRECT = 'directCost';
    const COST_TYPE_INDIRECT = 'indirectCost';
    const COST_TYPE_GOVERNMENT = 'governmentExpenditures';
    const COST_TYPE_PHYSICAL = 'physicalContingencies';

    public function mount($id = null, $category = '')
    {
        $this->projectId = $id;
        $this->category = $category;

        if ($this->projectId) {
            $project = Project::find($this->projectId);

            if ($project) {
                // Use project_name
                $this->projectName = $project->project_name; //
                $this->project_category = $project->project_category;
                $this->appropriation = $project->appropriation;
                $this->source_of_funds = $project->source_of_funds;
                $this->duration = $project->duration;
                $this->start_date = $project->start_date;

                $this->directCost = $project->projectCosts()->where('cost_type', self::COST_TYPE_DIRECT)->orderBy('index')->get()->toArray();
                $this->indirectCost = $project->projectCosts()->where('cost_type', self::COST_TYPE_INDIRECT)->orderBy('index')->get()->toArray();
                $this->governmentExpenditures = $project->projectCosts()->where('cost_type', self::COST_TYPE_GOVERNMENT)->orderBy('index')->get()->toArray();
                $this->physicalContingencies = $project->projectCosts()->where('cost_type', self::COST_TYPE_PHYSICAL)->orderBy('index')->get()->toArray();

                if (empty($this->directCost)) {
                    $this->directCost = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
                }
                if (empty($this->indirectCost)) {
                    $this->indirectCost = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
                }
                if (empty($this->governmentExpenditures)) {
                    $this->governmentExpenditures = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
                }
                if (empty($this->physicalContingencies)) {
                    $this->physicalContingencies = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
                }
            } else {
                session()->flash('error', 'Project not found.');
                // Consider redirecting to a safe route, e.g., projects index
                redirect()->route('projects');
            }
        } else {
            $this->directCost = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
            $this->indirectCost = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
            $this->governmentExpenditures = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
            $this->physicalContingencies = [['cost_description' => '', 'percentage' => '', 'amount' => '']];
        }
    }

    public function addRow($type)
    {
        $this->{$type}[count($this->{$type})] = ['cost_description' => '', 'percentage' => '', 'amount' => ''];
    }

    public function addRowAt($group, $index, $position = 'below')
    {
        $costRow = ['cost_description' => '', 'percentage' => '', 'amount' => ''];
        if ($position === 'above') {
            array_splice($this->$group, $index, 0, [$costRow]);
        } else {
            array_splice($this->$group, $index + 1, 0, [$costRow]);
        }
    }

    public function removeRow($type, $index)
    {
        unset($this->{$type}[$index]);
        $this->{$type} = array_values($this->{$type});
    }

    public function save()
    {
        // Validate the form data
        $this->validate([
            'projectName' => 'required|string|max:255',
            'project_category' => 'required|string|max:255',
            // 'appropriation' => 'required|string|max:255', // REMOVED VALIDATION
            'source_of_funds' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'start_date' => 'required|date',
            // Basic validation for cost amounts
            'directCost.*.amount' => 'nullable|numeric|min:0',
            'indirectCost.*.amount' => 'nullable|numeric|min:0',
            'governmentExpenditures.*.amount' => 'nullable|numeric|min:0',
            'physicalContingencies.*.amount' => 'nullable|numeric|min:0',
        ]);

        try {

             // --- Start Calculation ---
             // 1. Calculate Total Amount from all cost types
             $totalAmount = 0;
             $costTypes = [
                 $this->directCost,
                 $this->indirectCost,
                 $this->governmentExpenditures,
                 $this->physicalContingencies
             ];

             foreach ($costTypes as $costs) {
                 foreach ($costs as $cost) {
                     // Ensure amount is set, not null, and numeric before adding
                     if (isset($cost['amount']) && is_numeric($cost['amount'])) {
                         $totalAmount += (float) $cost['amount'];
                     }
                 }
             }

            // 2. Calculate and Assign Percentage for each cost item (as before)
            if ($totalAmount > 0) {
                foreach ($this->directCost as &$cost) {
                    $cost['percentage'] = (isset($cost['amount']) && is_numeric($cost['amount'])) ? (((float) $cost['amount'] / $totalAmount) * 100) : 0;
                } unset($cost);
                foreach ($this->indirectCost as &$cost) {
                    $cost['percentage'] = (isset($cost['amount']) && is_numeric($cost['amount'])) ? (((float) $cost['amount'] / $totalAmount) * 100) : 0;
                } unset($cost);
                foreach ($this->governmentExpenditures as &$cost) {
                    $cost['percentage'] = (isset($cost['amount']) && is_numeric($cost['amount'])) ? (((float) $cost['amount'] / $totalAmount) * 100) : 0;
                } unset($cost);
                foreach ($this->physicalContingencies as &$cost) {
                    $cost['percentage'] = (isset($cost['amount']) && is_numeric($cost['amount'])) ? (((float) $cost['amount'] / $totalAmount) * 100) : 0;
                } unset($cost);
            } else {
                 foreach ($this->directCost as &$cost) { $cost['percentage'] = 0; } unset($cost);
                 foreach ($this->indirectCost as &$cost) { $cost['percentage'] = 0; } unset($cost);
                 foreach ($this->governmentExpenditures as &$cost) { $cost['percentage'] = 0; } unset($cost);
                 foreach ($this->physicalContingencies as &$cost) { $cost['percentage'] = 0; } unset($cost);
            }

             // Calculate end_date (as before)
             $startDate = Carbon::parse($this->start_date);
             $endDate = $startDate->copy()->addDays((int) $this->duration - 1)->toDateString();

             // --- End Calculation ---

            DB::beginTransaction();

            // Prepare project data, including the calculated appropriation
            $projectData = [
                'project_name' => $this->projectName,
                'project_category' => $this->project_category,
                'appropriation' => $totalAmount, // <<< SET CALCULATED APPROPRIATION HERE
                'source_of_funds' => $this->source_of_funds,
                'duration' => $this->duration,
                'start_date' => $this->start_date,
                'end_date' => $endDate,
                'material_cost' => 0, // Placeholder, adjust if needed
                'labor_cost' => 0,    // Placeholder, adjust if needed
                'total_contract_amount' => $totalAmount, // Still useful to store explicit total
                'pow_status' => 'for-approval',
                'physical_accomplishment' => '',
                'implementation_status' => 'pending',
                'remarks' => '',
                'url' => '',
                'category' => $this->category,
            ];

            // Update or Create Project (as before)
            if ($this->projectId) {
                $project = Project::findOrFail($this->projectId);
                $project->update($projectData);
                ProjectCost::where('project_id', $this->projectId)->delete();
            } else {
                $project = Project::create($projectData);
                $this->projectId = $project->id;
            }

            // Save costs (as before)
            $this->saveCosts($this->projectId, self::COST_TYPE_DIRECT, $this->directCost);
            $this->saveCosts($this->projectId, self::COST_TYPE_INDIRECT, $this->indirectCost);
            $this->saveCosts($this->projectId, self::COST_TYPE_GOVERNMENT, $this->governmentExpenditures);
            $this->saveCosts($this->projectId, self::COST_TYPE_PHYSICAL, $this->physicalContingencies);

            DB::commit();

            session()->flash('success', $project->wasRecentlyCreated ? 'Project created successfully!' : 'Project updated successfully!');
            return redirect()->route($this->category);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Project save failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to save project. An unexpected error occurred.');
        }
    }

    // saveCosts method remains unchanged
    private function saveCosts($projectId, $type, $costs)
    {
        foreach ($costs as $index => $cost) {
            $hasDescription = isset($cost['cost_description']) && trim($cost['cost_description']) !== '';
            $hasAmount = isset($cost['amount']) && is_numeric($cost['amount']) && (float)$cost['amount'] >= 0;

            if ($hasDescription || ($hasAmount && (float)$cost['amount'] > 0)) {
                ProjectCost::create([
                    'project_id' => $projectId,
                    'cost_type' => $type,
                    'index' => $index,
                    'cost_description' => trim($cost['cost_description'] ?? ''),
                    'percentage' => isset($cost['percentage']) && is_numeric($cost['percentage']) ? (float) $cost['percentage'] : 0,
                    'amount' => $hasAmount ? (float) $cost['amount'] : 0,
                ]);
            }
        }
    }

    public function deleteProject($projectId)
    {
        try {
            DB::beginTransaction();
            ProjectCost::where('project_id', $projectId)->delete(); //
            Project::destroy($projectId); //
            DB::commit();
            session()->flash('success', 'Project deleted successfully.');
            // Redirect to index or dashboard after deletion
            // return redirect()->route('projects');
        } catch (Exception $e) {
            DB::rollBack();
            // Log::error('Project delete failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete project.');
            // return redirect()->back();
        }
    }

    public function render()
    {
        return view('livewire.project-form-test'); //
    }
}