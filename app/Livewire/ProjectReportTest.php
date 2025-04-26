<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectCost; // Make sure ProjectCost model is imported
use Illuminate\Support\Collection; // Import Collection for type hinting
use Exception; // Import Exception for error handling

class ProjectReportTest extends Component
{
    // --- Component Properties ---

    /**
     * The ID of the project to display.
     * Passed into the component.
     * @var int
     */
    public int $projectId;

    /**
     * The Project model instance.
     * Loaded in the mount method.
     * @var Project|null
     */
    public ?Project $project = null;

    /**
     * Collection of direct costs.
     * @var Collection
     */
    public Collection $directCosts;

    /**
     * Collection of indirect costs.
     * @var Collection
     */
    public Collection $indirectCosts;

    /**
     * Collection of government expenditures.
     * @var Collection
     */
    public Collection $governmentExpenditures;

    /**
     * Collection of physical contingencies.
     * @var Collection
     */
    public Collection $physicalContingencies;

    /**
     * Calculated subtotal for direct costs.
     * @var float
     */
    public float $directCostSubtotal = 0;

    /**
     * Calculated subtotal for indirect costs.
     * @var float
     */
    public float $indirectCostSubtotal = 0;

    /**
     * Calculated subtotal for government expenditures.
     * @var float
     */
    public float $governmentExpendituresSubtotal = 0;

    /**
     * Calculated subtotal for physical contingencies.
     * @var float
     */
    public float $physicalContingenciesSubtotal = 0;

    /**
     * Calculated grand total project cost.
     * @var float
     */
    public float $totalProjectCost = 0;

    /**
     * Error message if project loading fails.
     * @var string|null
     */
    public ?string $errorMessage = null;

    // --- Lifecycle Hooks ---

    /**
     * Mounts the component, loads the project data, and prepares costs.
     *
     * @param int $projectId The ID of the project to load.
     * @return void
     */
    public function mount(int $id): void
    {
        $this->projectId = $id;
        $this->loadProjectData();
    }

    // --- Data Loading and Preparation ---

    /**
     * Loads the project with its costs and calculates totals.
     * Handles potential errors during data fetching.
     *
     * @return void
     */
    protected function loadProjectData(): void
    {
        try {
            // Eager load the projectCosts relationship to prevent N+1 issues
            $this->project = Project::with('projectCosts')->findOrFail($this->projectId);

            // Prepare costs and calculate totals
            $this->prepareCosts();

        } catch (Exception $e) {
            // Log the actual error for debugging
            // Log::error("Failed to load project report for ID {$this->projectId}: " . $e->getMessage());

            // Set a user-friendly error message
            $this->project = null; // Ensure project is null if loading failed
            $this->errorMessage = "Could not load project data. Please ensure the project exists and try again.";
            // Reset cost properties
            $this->resetCostProperties();
        }
    }

    /**
     * Groups costs by type and calculates all subtotals and the grand total.
     * Assumes $this->project is loaded.
     *
     * @return void
     */
    protected function prepareCosts(): void
    {
        if (!$this->project) {
            $this->resetCostProperties();
            return;
        }

        // Group costs by 'cost_type' and sort each group by 'index'
        $costsByType = $this->project->projectCosts->sortBy('index')->groupBy('cost_type');

        // Assign costs to properties, defaulting to an empty collection
        // Use constants from ProjectFormTest for consistency
        $this->directCosts = $costsByType->get(ProjectFormTest::COST_TYPE_DIRECT, collect());
        $this->indirectCosts = $costsByType->get(ProjectFormTest::COST_TYPE_INDIRECT, collect());
        $this->governmentExpenditures = $costsByType->get(ProjectFormTest::COST_TYPE_GOVERNMENT, collect());
        $this->physicalContingencies = $costsByType->get(ProjectFormTest::COST_TYPE_PHYSICAL, collect());

        // Calculate Subtotals
        $this->directCostSubtotal = $this->directCosts->sum('amount');
        $this->indirectCostSubtotal = $this->indirectCosts->sum('amount');
        $this->governmentExpendituresSubtotal = $this->governmentExpenditures->sum('amount');
        $this->physicalContingenciesSubtotal = $this->physicalContingencies->sum('amount');

        // Calculate Grand Total
        $this->totalProjectCost = $this->directCostSubtotal
            + $this->indirectCostSubtotal
            + $this->governmentExpendituresSubtotal
            + $this->physicalContingenciesSubtotal;
    }

    /**
     * Resets all cost-related properties to their default empty/zero state.
     * Used when project loading fails.
     *
     * @return void
     */
    protected function resetCostProperties(): void
    {
        $this->directCosts = collect();
        $this->indirectCosts = collect();
        $this->governmentExpenditures = collect();
        $this->physicalContingencies = collect();
        $this->directCostSubtotal = 0;
        $this->indirectCostSubtotal = 0;
        $this->governmentExpendituresSubtotal = 0;
        $this->physicalContingenciesSubtotal = 0;
        $this->totalProjectCost = 0;
    }


    // --- Helper Methods ---

    /**
     * Helper function to calculate percentage safely, avoiding division by zero.
     * Can be called directly from the Blade view using $this->calculatePercentage(...)
     *
     * @param float $amount The amount of the item.
     * @param float $total The total amount to calculate the percentage against.
     * @return float The calculated percentage.
     */
    public function calculatePercentage(float $amount, float $total): float
    {
        return ($total > 0) ? ($amount / $total) * 100 : 0;
    }

    // --- Rendering ---

    /**
     * Renders the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    // public function render()
    // {
    //     // The view will automatically have access to the public properties
    //     // (project, directCosts, totalProjectCost, errorMessage, etc.)
    //     return view('livewire.project-report');
    // }
    public function render()
    {
        return view('livewire.project-report-test');
    }
}
