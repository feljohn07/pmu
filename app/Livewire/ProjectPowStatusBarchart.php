<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectPowStatusBarchart extends Component
{

    public $category = '';

    public array $summaryStats = [
        'for-approval' => 0,
        'approved' => 0,
        'no-pow' => 0,
    ];

    public function mount(string $category = '')
    {
        $this->category = $category;

        // $this->summaryStats = [
        //     'for-approval' => Project::where('pow_status', 'for-approval')->count(),
        //     'approved' => Project::where('pow_status', 'approved')->count(),
        //     'no-pow' => Project::where('pow_status', 'no-pow')->count(),

        // ];
        $query = Project::query();

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        $forApprovalQuery = clone $query;
        $approvedQuery = clone $query;
        $noPowQuery = clone $query;

        $this->summaryStats = [
            'for-approval' => Project::getForApprovalPOWCount($this->category),
            'approved' => $approvedQuery->where('pow_status', 'approved')->count(),
            'no-pow' => Project::getProjectsWithoutPOWCount($this->category),
        ];
    }

    public function render()
    {
        return view('livewire.project-pow-status-barchart');
    }
}
