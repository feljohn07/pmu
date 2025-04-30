<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use DateTime;
use Carbon\Carbon;

class ProjectManager extends Component
{
    public $projects;
    public $editingProject = null;
    public $showModal = false;

    public $projectName = '';
    public $materialCost;
    public $laborCost;
    public $totalContractAmount;
    public $powStatus = '';
    public $physicalAccomplishment = '';
    public $duration = '';
    public $implementationStatus = '';
    public $remarks = '';
    public $category = '';
    public $url = '';
    public $startDate = '';
    public $endDate = '';

    protected $rules = [
        'projectName' => 'required|min:3|max:255',
        'materialCost' => 'required|numeric|min:0',
        'laborCost' => 'required|numeric|min:0',
        'duration' => 'required|numeric|min:0',
        'remarks' => 'nullable|max:500',
        'powStatus' => 'required|in:approved,for-approval,no-pow',
        'implementationStatus' => 'required|in:on-going,completed,pending',
        'startDate' => 'required',
        // 'endDate' => 'required',
    ];

    public $projectCategory = '';

    public function mount(string $projectCategory = '')
    {
        $this->projectCategory = $projectCategory;
        $this->loadProjects();
    }

    public function render()
    {
        return view('livewire.project-manager');
    }

    public function loadProjects()
    {
        if ($this->projectCategory == '') {
            $this->projects = Project::orderBy('created_at', 'desc')->get();

        } else {
            $this->projects = Project::where('category', $this->projectCategory)
                ->orderBy('created_at', 'desc')
                ->get();
        }

    }

    public function createProject()
    {
        // $this->resetInputFields();
        // $this->editingProject = null;
        // $this->showModal = true;

        redirect(route('create-project', ['category' => $this->projectCategory]));
    }

    public function editProjectRedirect(Project $project)
    {
        // $this->resetInputFields();
        // $this->editingProject = null;
        // $this->showModal = true;

        redirect(route('edit-project', [$project->id, $project->category]));
    }

    public function editProject(Project $project)
    {
        $this->editingProject = $project;
        $this->projectName = $project['project_name'];
        $this->materialCost = $project['material_cost'];
        $this->laborCost = $project['labor_cost'];
        $this->totalContractAmount = $project['total_contract_amount'];
        $this->powStatus = $project['pow_status'];
        $this->physicalAccomplishment = $project['physical_accomplishment'];
        $this->duration = $project['duration'];
        $this->implementationStatus = $project['implementation_status'];
        $this->remarks = $project['remarks'];
        $this->category = $project['category'];
        $this->startDate = $project['start_date'];
        $this->endDate = $project['end_date'];
        $this->url = $project['url'];
        $this->showModal = true;
    }

    public function saveProject()
    {
        $this->validate();

        // Calculate the end date based on start date + duration (in days)
        $startDate = Carbon::parse($this->startDate);
        $endDate = $startDate->copy()->addDays((int) $this->duration);

        if ($this->editingProject) {
            $this->editingProject->update([
                'project_name' => $this->projectName,
                'material_cost' => $this->materialCost,
                'labor_cost' => $this->laborCost,
                'total_contract_amount' => $this->materialCost + $this->laborCost,
                'pow_status' => $this->powStatus,
                'physical_accomplishment' => $this->physicalAccomplishment,
                'duration' => $this->duration,
                'implementation_status' => $this->implementationStatus,
                'remarks' => $this->remarks,
                'category' => $this->projectCategory,
                'start_date' => $this->startDate,
                'end_date' => $endDate,
                'url' => $this->url,
            ]);
            session()->flash('message', 'Project updated successfully.');
        } else {
            Project::create([
                'project_name' => $this->projectName,
                'material_cost' => $this->materialCost,
                'labor_cost' => $this->laborCost,
                'total_contract_amount' => $this->materialCost + $this->laborCost,
                'pow_status' => 'for-approval',
                'physical_accomplishment' => $this->physicalAccomplishment,
                'duration' => $this->duration,
                'implementation_status' => 'pending',
                'remarks' => '',
                'category' => $this->projectCategory,
                'start_date' => $this->startDate,
                'end_date' => $endDate, // this will be based on duration -> end_date = start_date + duration
                'url' => $this->url,

            ]);
            session()->flash('message', 'Project created successfully.');
        }

        $this->resetInputFields();
        $this->loadProjects();
        $this->closeModal();
        $this->refreshPage();

    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        $this->loadProjects();
        session()->flash('message', 'Project deleted successfully.');
        $this->refreshPage();
    }

    // public function toggleApproval(Project $task)
    // {
    //     // $task->update(['completed' => !$task->completed]);
    //     // $this->loadProjects();
    // }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetInputFields()
    {

        $this->projectName = '';
        $this->materialCost = '';
        $this->laborCost = '';
        $this->totalContractAmount = '';
        $this->powStatus = '';
        $this->physicalAccomplishment = '';
        $this->duration = '';
        $this->implementationStatus = '';
        $this->category = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->url = '';
        $this->resetValidation();
    }

    public function approveProject(Project $project)
    {
        $project->pow_status = 'approved';

        $project->save();
        $this->loadProjects();
        session()->flash('message', 'Project approved successfully.');
        $this->refreshPage();
    }

    public function refreshPage()
    {
        redirect(request()->header('Referer'));
    }

    public function viewProject($id)
    {

        redirect(route('project-view', ['id' => $id]));
    }

}
