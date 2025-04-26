<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use App\Models\User;

class Homepage extends Component
{
    public $constructionsChartData;
    public $repairsChartData;
    public $fabricationsChartData;

    // TODO: latest project
    public $latestProjects;

    public function mount()
    {
        $this->loadChartData();
        $this->loadLatestProjects();
    }

    public function chartData(string $category = '')
    {
        $query = Project::query()->where('category', $category);

        // Clone the base query for each status count
        $completedQuery = clone $query;
        $ongoingQuery = clone $query;
        $pendingQuery = clone $query;


        switch ($category) {
            case 'constructions':
                // Assign the counts to your property
                $this->constructionsChartData = collect([
                    'completed' => $completedQuery->where('implementation_status', 'completed')->count(),
                    'ongoing' => $ongoingQuery->where('implementation_status', 'on-going')->count(),
                    'pending' => $pendingQuery->where('implementation_status', 'pending')->count(),
                ]);
                break;
            case 'repairs':
                // Assign the counts to your property
                $this->repairsChartData = collect([
                    'completed' => $completedQuery->where('implementation_status', 'completed')->count(),
                    'ongoing' => $ongoingQuery->where('implementation_status', 'on-going')->count(),
                    'pending' => $pendingQuery->where('implementation_status', 'pending')->count(),
                ]);
                break;
            case 'fabrications':
                // Assign the counts to your property
                $this->fabricationsChartData = collect([
                    'completed' => $completedQuery->where('implementation_status', 'completed')->count(),
                    'ongoing' => $ongoingQuery->where('implementation_status', 'on-going')->count(),
                    'pending' => $pendingQuery->where('implementation_status', 'pending')->count(),
                ]);
                break;
            default:

                break;
        }

    }


    public function loadChartData()
    {

        $this->chartData('constructions');
        $this->chartData('repairs');
        $this->chartData('fabrications');

    }

    public function loadLatestProjects()
    {
        $this->latestProjects = Project::select()->orderBy('created_at', 'desc')->limit(10)->get();
        // dd($this->latestProjects);
    }

    public function render()
    {
        return view('livewire.homepage');
    }
}
