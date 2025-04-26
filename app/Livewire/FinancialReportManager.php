<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FinancialReport;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\View\View;

class FinancialReportManager extends Component
{
    public $projectId;

    // Form state properties
    public $report_date; // Will hold the string value
    public $amount;

    // State control
    public $selectedReportId = null;
    public $isModalOpen = false;
    public $confirmingDeletion = false;
    public $reportToDelete = null;

    // Public properties for view data
    public $reports; // Will hold Collection
    public $totalAmount;

    protected $listeners = ['reportAdded' => '$refresh'];

    // Validation Rules
    protected function rules()
    {
        // Validate report_date only as required (it's just text now)
        // You could add 'string', 'max:255' if using VARCHAR in DB
        return [
            'report_date' => 'required|string|max:255', // Treat as a required string
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function mount($projectId)
    {
        $this->projectId = $projectId;

        // dd($this->projectId);
        $this->resetForm();
    }

    private function resetForm()
    {
        // Set default to empty string or a default text format if desired
        // $this->report_date = now()->format('Y-m-d'); // Still okay if using <input type="date">
        $this->report_date = ''; // Or set to empty string if using <input type="text">
        $this->amount = '';
        $this->selectedReportId = null;
        $this->isModalOpen = false;
        $this->confirmingDeletion = false;
        $this->reportToDelete = null;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($reportId)
    {
        if (!$this->projectId)
            return;
        try {
            $report = FinancialReport::where('project_id', $this->projectId)->findOrFail($reportId);

            // Security check
            if ($report->project_id != $this->projectId) {
                session()->flash('error', 'Unauthorized action.');
                return;
            }

            $this->selectedReportId = $report->id;
            // --- Assign the raw string value from the database ---
            $this->report_date = $report->report_date; // No ->format() needed as it's already a string
            // --- End Change ---
            $this->amount = $report->amount;
            $this->isModalOpen = true;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Report not found.');
        }
    }

    public function store()
    {

        if (!$this->projectId) {
            session()->flash('error', 'Project context is missing.');
            return;
        }
        // Validation now expects 'report_date' to be a string
        $validatedData = $this->validate();


        try {

            FinancialReport::updateOrCreate(
                ['id' => $this->selectedReportId],
                [
                    'project_id' => $this->projectId,
                    // Save the validated string directly
                    'report_date' => $validatedData['report_date'],
                    'amount' => $validatedData['amount'],
                ]
            );
            redirect(request()->header('Referer'));
            session()->flash('message', $this->selectedReportId ? 'Report updated successfully.' : 'Report added successfully.');


        } catch (\Exception $e) {
            // Log::error("Error saving financial report: " . $e->getMessage());
            // dd($e);
            session()->flash('error', 'An error occurred while saving the report.');

        }
    }

    // confirmDelete and delete methods remain the same...
    public function confirmDelete($reportId)
    {
        if (!$this->projectId)
            return;
        $report = FinancialReport::where('id', $reportId)->where('project_id', $this->projectId)->first();
        if (!$report) {
            session()->flash('error', 'Report not found or unauthorized action.');
            return;
        }
        $this->confirmingDeletion = true;
        $this->reportToDelete = $reportId;
    }

    public function delete()
    {
        if (!$this->reportToDelete || !$this->projectId)
            return;
        try {
            $report = FinancialReport::where('id', $this->reportToDelete)
                ->where('project_id', $this->projectId)
                ->firstOrFail();
            $report->delete();
            session()->flash('message', 'Report deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Report not found or unauthorized.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while deleting the report.');
        } finally {
            $this->resetForm();
            redirect(request()->header('Referer'));
        }
    }


    public function closeModalFinancial()
    {
        $this->resetForm();
    }

    public function render(): View
    {
        $this->reports = collect();
        $this->totalAmount = 0;
        $projectName = 'N/A';

        if ($this->projectId) {
            // Fetch reports - orderBy report_date might behave differently on TEXT vs DATE column
            // It will sort alphabetically, not chronologically, unless strings are zero-padded (e.g., 'YYYY-MM-DD')
            $this->reports = FinancialReport::where('project_id', $this->projectId)
                ->orderBy('report_date', 'asc') // Alphabetic sort now
                ->get();

            $this->totalAmount = $this->reports->sum('amount');

            $projectData = Project::select('name')->find($this->projectId);
            // $projectName = $projectData ? $projectData->name : 'Project Not Found';
        }

        return view('livewire.financial-report-manager');
    }
}