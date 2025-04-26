<?php

namespace App\Livewire;

use App\Models\DocumentationUpload;
use Livewire\Component;
use App\Models\AccomplishmentReport;
use App\Models\Project; // Keep if needed
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mary\Traits\Toast; // <-- Import Mary UI Toast trait
use Livewire\WithFileUploads;

class AccomplishmentReportComponent extends Component
{
    use Toast; // <-- Use the Mary UI Toast trait
    use WithFileUploads;

    // Properties from original component
    public $projectId;
    /** @var Collection<int, AccomplishmentReport> */
    public $accomplishmentReports;

    // Properties for "Create Plan" Modal
    public $showPlanFormModal = false;
    public $reportDate = '';
    public $plannedAccomplishment = '';
    public ?int $editingPlanId = null;

    // Properties for "Submit Actual" Modal
    public $showActualFormModal = false;
    public ?int $currentReportId = null;
    public ?AccomplishmentReport $currentReport = null;
    public $currentActualAccomplishment = '';

    // Properties for Totals (calculated)
    public float $totalPlanned = 0;
    public float $totalActual = 0;
    public float $totalVariance = 0;

    // Properties for Delete Confirmation Modal
    public $showDeleteConfirmationModal = false;
    public ?int $reportToDeleteId = null;

    // Properties for Documentation Upload Modal
    public $showUploadModal = false;
    public $filesToUpload = []; // Property to hold temporary files
    public ?int $reportIdForUpload = null; // To know which report we're uploading for


    /**
     * Validation rules for creating/editing a plan.
     */
    protected function planRules(): array
    {
        return [
            'reportDate' => 'required|string|max:255',
            'plannedAccomplishment' => 'required|numeric|between:0,100',
        ];
    }

    /**
     * Validation rules for submitting actual accomplishment.
     */
    protected function actualRules(): array
    {
        return [
            'currentActualAccomplishment' => 'required|numeric|between:0,100',
        ];
    }

    /**
     * Validation rules for file uploads.
     */
    protected function fileRules(): array
    {
        return [
            // Adjust max size (in KB) and mime types as needed
            'filesToUpload.*' => 'required|image|max:5120', // Example: Max 5MB image files (jpeg, png, bmp, gif, svg, or webp)
        ];
    }

    /**
     * Custom validation messages for file uploads.
     */
    protected function fileMessages(): array
    {
        return [
            'filesToUpload.*.required' => 'Please select at least one file.',
            'filesToUpload.*.image' => 'Only image files are allowed.',
            'filesToUpload.*.max' => 'Each file must not exceed 5MB.',
        ];
    }

    /**
     * Mount function: Load initial data.
     */
    public function mount(int $projectId): void
    {
        $this->projectId = $projectId;
        $this->loadAccomplishmentReports();
    }

    /**
     * Load accomplishment reports and calculate totals.
     */
    public function loadAccomplishmentReports(): void
    {
        $this->accomplishmentReports = AccomplishmentReport::where('project_id', $this->projectId)
            ->orderBy('created_at') // Consider ordering by report_date if more logical
            ->get();
        $this->calculateTotals();
    }

    /**
     * Calculate total planned, actual, and variance.
     */
    public function calculateTotals(): void
    {
        $this->totalPlanned = $this->accomplishmentReports->sum('planned_accomplishment');
        $this->totalActual = $this->accomplishmentReports->whereNotNull('actual_accomplishment')->sum('actual_accomplishment');
        $this->totalVariance = $this->accomplishmentReports->whereNotNull('variance')->sum('variance'); // Calculate total variance
    }

    //--------------------------------------------------------------------------
    // Plan Modal Methods
    //--------------------------------------------------------------------------

    public function createPlanReport(): void
    {
        $this->resetValidation();
        $this->clearPlanForm();
        $this->editingPlanId = null; // Ensure we are in create mode
        $this->showPlanFormModal = true;
    }

    public function editPlanReport(int $planId): void
    {
        $this->resetValidation();
        try {
            $plan = AccomplishmentReport::where('project_id', $this->projectId)->findOrFail($planId);

            if ($plan->actual_accomplishment !== null) {
                // Use Mary UI error toast
                $this->error('Cannot edit plan', 'Actual accomplishment already submitted.');
                return;
            }

            $this->editingPlanId = $plan->id;
            $this->reportDate = $plan->report_date;
            $this->plannedAccomplishment = $plan->planned_accomplishment;
            $this->showPlanFormModal = true;

        } catch (ModelNotFoundException $e) {
            // Use Mary UI error toast
            $this->error('Not Found', 'Plan entry not found or access denied.');
            $this->clearPlanForm();
        } catch (\Exception $e) {
            // Log::error('Error loading plan for edit: '.$e->getMessage()); // Optional: Log the error
            // Use Mary UI error toast
            $this->error('Error', 'An error occurred while loading the plan details.');
            $this->clearPlanForm();
        }
    }

    public function savePlanReport(): void
    {
        $validatedData = $this->validate($this->planRules());

        try {
            if ($this->editingPlanId) {
                // Update existing plan
                $plan = AccomplishmentReport::where('project_id', $this->projectId)->findOrFail($this->editingPlanId);

                if ($plan->actual_accomplishment !== null) {
                    // Use Mary UI error toast
                    $this->error('Update Failed', 'Cannot update plan after actual accomplishment has been submitted.');
                    $this->showPlanFormModal = false;
                    return;
                }

                $plan->update([
                    'report_date' => $validatedData['reportDate'],
                    'planned_accomplishment' => $validatedData['plannedAccomplishment'],
                    // Variance will be recalculated when actual is submitted or here if needed
                ]);
                // Use Mary UI success toast
                $this->success('Plan Updated', 'Plan entry updated successfully!');
            } else {
                // Create new plan
                AccomplishmentReport::create([
                    'project_id' => $this->projectId,
                    'report_date' => $validatedData['reportDate'],
                    'planned_accomplishment' => $validatedData['plannedAccomplishment'],
                    'actual_accomplishment' => null,
                    'variance' => null,
                ]);
                // Use Mary UI success toast
                $this->success('Plan Created', 'Plan entry created successfully!');
            }

            $this->loadAccomplishmentReports();
            $this->showPlanFormModal = false;
            $this->clearPlanForm();

        } catch (ModelNotFoundException $e) {
            // Use Mary UI error toast
            $this->error('Not Found', 'Plan entry not found for update.');
            $this->showPlanFormModal = false;
        } catch (\Exception $e) {
            // Log::error('Error saving plan report: '.$e->getMessage()); // Optional: Log the error
            $action = $this->editingPlanId ? 'update' : 'create';
            // Use Mary UI error toast
            $this->error('Save Failed', "Failed to {$action} plan entry. Please try again.");
            // Optionally keep modal open: //$this->showPlanFormModal = true;
        }
    }

    public function clearPlanForm(): void
    {
        $this->reportDate = '';
        $this->plannedAccomplishment = '';
        $this->editingPlanId = null; // Clear editing state when clearing form
        // Do not close modal here
    }

    //--------------------------------------------------------------------------
    // Actual Accomplishment Modal Methods
    //--------------------------------------------------------------------------

    public function openActualFormModal(int $reportId): void
    {
        $this->resetValidation();
        try {
            $report = AccomplishmentReport::where('project_id', $this->projectId)
                ->findOrFail($reportId);

            $this->currentReportId = $report->id;
            $this->currentReport = $report;
            $this->currentActualAccomplishment = $report->actual_accomplishment ?? '';
            $this->showActualFormModal = true;

        } catch (ModelNotFoundException $e) {
            // Use Mary UI error toast
            $this->error('Not Found', 'Report not found or access denied.');
            $this->clearActualForm();
        } catch (\Exception $e) {
            // Log::error('Error opening actual form: '.$e->getMessage()); // Optional: Log the error
            // Use Mary UI error toast
            $this->error('Error', 'An error occurred while loading the report details.');
            $this->clearActualForm();
        }
    }

    public function saveActualAccomplishment(): void
    {
        if (!$this->currentReportId || !$this->currentReport) {
            // Use Mary UI error toast
            $this->error('Error', 'No report selected for update.');
            $this->showActualFormModal = false;
            return;
        }

        $validatedData = $this->validate($this->actualRules());
        $actual = (float) $validatedData['currentActualAccomplishment'];
        $planned = (float) $this->currentReport->planned_accomplishment;

        try {
            $this->currentReport->actual_accomplishment = $actual;
            $this->currentReport->variance = $actual - $planned; // Calculate variance
            $this->currentReport->save();

            $this->loadAccomplishmentReports(); // Refresh list and totals
            $this->showActualFormModal = false;
            $this->clearActualForm();
            // Use Mary UI success toast
            $this->success('Actual Submitted', 'Actual accomplishment submitted successfully!');

        } catch (\Exception $e) {
            // Log::error('Error saving actual accomplishment: '.$e->getMessage()); // Optional: Log the error
            // Use Mary UI error toast
            $this->error('Save Failed', 'Failed to submit actual accomplishment. Please try again.');
            // Optionally keep modal open for correction
        }
    }

    public function clearActualForm(): void
    {
        $this->currentReportId = null;
        $this->currentReport = null;
        $this->currentActualAccomplishment = '';
        // Do not close modal here
    }


    //--------------------------------------------------------------------------
    // Delete Confirmation Modal Methods
    //--------------------------------------------------------------------------

    public function confirmDelete(int $reportId): void
    {
        if ($this->accomplishmentReports->contains('id', $reportId)) {
            $this->reportToDeleteId = $reportId;
            $this->showDeleteConfirmationModal = true;
        } else {
            // Use Mary UI error toast
            $this->error('Not Found', 'Report not found or cannot be deleted.');
            $this->reportToDeleteId = null;
            $this->showDeleteConfirmationModal = false;
        }
    }

    public function cancelDelete(): void
    {
        $this->reportToDeleteId = null;
        $this->showDeleteConfirmationModal = false;
    }

    public function deleteReport(): void
    {
        if ($this->reportToDeleteId === null) {
            // Use Mary UI warning toast
            $this->warning('No Selection', 'No report selected for deletion.');
            $this->showDeleteConfirmationModal = false;
            return;
        }

        try {
            $report = AccomplishmentReport::where('project_id', $this->projectId)
                ->findOrFail($this->reportToDeleteId);

            $report->delete();

            $this->loadAccomplishmentReports(); // Refresh list and totals
            // Use Mary UI success toast
            $this->success('Deleted', 'Report entry deleted successfully!');

        } catch (ModelNotFoundException $e) {
            // Use Mary UI error toast
            $this->error('Not Found', 'Report not found or already deleted.');
        } catch (\Exception $e) {
            // Log::error('Error deleting report: '.$e->getMessage()); // Optional: Log the error
            // Use Mary UI error toast
            $this->error('Delete Failed', 'Failed to delete the report entry. Please try again.');
        } finally {
            // Always close the modal and clear the ID after attempting deletion
            $this->cancelDelete();
        }
    }

    //--------------------------------------------------------------------------
    // Documentation Upload Methods
    //--------------------------------------------------------------------------

    public function openUploadModal(int $reportId): void
    {
        $this->resetValidation(); // Clear previous validation errors
        $this->filesToUpload = []; // Clear any previous file selections
        $this->reportIdForUpload = $reportId; // Store the report ID
        $this->showUploadModal = true;        // Show the modal
    }

    public function saveDocumentation(): void
    {
        if (!$this->reportIdForUpload) {
            $this->error('Error', 'No report selected for upload.');
            return;
        }

        // Validate the uploaded files
        $validatedData = $this->validate($this->fileRules(), $this->fileMessages());

        try {
            foreach ($this->filesToUpload as $file) {
                // Store the file in 'storage/app/public/documentation/{report_id}'
                // The store() method returns the relative path
                $path = $file->store("documentation/{$this->reportIdForUpload}", 'public');

                // Create a record in the database
                DocumentationUpload::create([
                    'accomplishment_report_id' => $this->reportIdForUpload,
                    'url' => $path, // Store the relative path
                    'approved' => false, // Default approval status
                ]);
            }

            $this->success('Upload Successful', count($this->filesToUpload) . ' file(s) uploaded.');
            $this->showUploadModal = false; // Close modal on success
            $this->filesToUpload = [];      // Clear the file input
            $this->reportIdForUpload = null; // Reset report ID

        } catch (\Exception $e) {
            // Log::error('Error uploading documentation: '.$e->getMessage()); // Optional logging
            $this->error('Upload Failed', 'An error occurred during file upload. Please try again.');
            // Keep modal open for user to retry or cancel
        }
    }


    // Modify existing uploadDocumentation to just open the modal
    public function uploadDocumentation(int $reportId): void
    {
        $this->openUploadModal($reportId);
    }

    public function viewDocumentation(int $reportId): void
    {
        // TODO: Implement logic to view documentation (perhaps open another modal listing files from DocumentationUpload table)
        // $this->info('Coming Soon', "View documentation feature for report ID {$reportId} not implemented yet.");
        redirect()->route('accomplishment.documentation.show', ['report' => $reportId]);
    }

    //--------------------------------------------------------------------------
    // Render Method
    //--------------------------------------------------------------------------

    public function render()
    {
        // Recalculate totals before rendering, in case data changed without full reload
        // Although loadAccomplishmentReports already calls calculateTotals,
        // it might be safer depending on component interactions.
        // However, calling it ONLY after modification (save/delete) is usually sufficient.
        // $this->calculateTotals(); // Consider if needed here or rely on calls within actions

        return view('livewire.accomplishment-report-component');
    }
}