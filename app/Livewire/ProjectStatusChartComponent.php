<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectStatusChartComponent extends Component
{

    public $category = '';

    public array $summaryStats = [
        'completed' => 0,
        'ongoing' => 0,
        'pending' => 0,
    ];

    public function mount(string $category = '')
    {
        $this->category = $category;

        $query = Project::query();

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        $CompletedQuery = clone $query;
        $onGoingQuery = clone $query;
        $pendingQuery = clone $query;

        $this->summaryStats = [
            'completed' => $CompletedQuery->where('implementation_status', 'completed')->count(),
            'ongoing' => $onGoingQuery->where('implementation_status', 'on-going')->count(),
            'pending' => $pendingQuery->where('implementation_status', 'pending')->count(),
        ];

        // $this->summaryStats = [
        //     'completed' => Project::where('implementation_status', 'completed')->count(),
        //     'ongoing' => Project::where('implementation_status', 'on-going')->count(),
        //     'pending' => Project::where('implementation_status', 'pending')->count(),

        // ];
    }

    /**
     * Render the component's view.
     */
    public function render()
    {
        // The $summaryStats public property is automatically passed to the view
        return view('livewire.project-status-chart-component');
    }
}