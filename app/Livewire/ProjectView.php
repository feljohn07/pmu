<?php

namespace App\Livewire;

use App\Models\AccomplishmentReport;
use App\Models\FinancialReport;
use App\Models\Project;
use DateTime;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectView extends Component
{

    // #[Url]
    // #[Url(keep: true)]
    #[Url(keep: true, as: 'id')]
    public $projectId;
    public $project;

    public $passedDays;
    public $remainingDays;

    public $financialAllocation;
    public $totalAllocationUsed;

    public $accomplishmentReport;

    public $weeks = [
        // [
        //     'week' => '1st Week October',
        //     'planned' => 4.11,
        //     'actual' => 2.98,
        //     'variance' => 2.98 - 4.11
        // ],
        // [
        //     'week' => '4th Week October',
        //     'planned' => 3.65,
        //     'actual' => 2.75,
        //     'variance' => 2.75 - 3.65
        // ],
        // [
        //     'week' => '3rd Week October',
        //     'planned' => 1.67,
        //     'actual' => 1.76,
        //     'variance' => 1.76 - 1.67
        // ],
        // [
        //     'week' => '2nd Week October',
        //     'planned' => -0.21,
        //     'actual' => 0.82,
        //     'variance' => 0.82 - (-0.21)
        // ]
        [
            'week' => 'October',
            'cumulative' => 2.98,
            'variance' => -1.13
        ],
        [
            'week' => 'November',
            'cumulative' => 2.75,
            'variance' => -0.90
        ],
        [
            'week' => 'December',
            'cumulative' => 1.76,
            'variance' => 0.09
        ],
        [
            'week' => 'January',
            'cumulative' => 0.82,
            'variance' => 1.03
        ]
    ];

    public function mount()
    {
        $this->project = Project::find($this->projectId);
        $this->financialAllocation =  $this->project->total_contract_amount;
        $this->totalAllocationUsed = FinancialReport::where('project_id', $this->projectId)->sum('amount');

        $this->accomplishmentReport = AccomplishmentReport::where('project_id', $this->projectId)->whereNot('actual_accomplishment', null)->get();
        // dd($this->totalAllocationUsed);
    }

    public function render()
    {
        $progress = $this->calculateProjectProgress($this->project->start_date, $this->project->end_date);
        return view('livewire.project-view', [
            'progress' => $progress,
        ]);
    }

    // Project Progress and Deadline

    public function calculateProjectProgress($startDate, $endDate)
    {
        // Convert the date strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $current = new DateTime(); // Current date and time

        // Calculate the total duration of the project
        $totalInterval = $start->diff($end);
        $totalDays = $totalInterval->days;

        // Calculate the duration passed since the start date
        $passedInterval = $start->diff($current);
        $this->passedDays = $passedInterval->days;

        // Calculate the remaining days
        $this->remainingDays = $totalDays - $this->passedDays;

        // Ensure remaining days is not negative (in case current date is after end date)
        $this->remainingDays = max(0, $this->remainingDays);

        return "(" . $this->passedDays . " out of " . ($this->passedDays + $this->remainingDays) . ") " . $this->remainingDays;
    }

    // Upload Scanned POW

    // View Scanned POW

    // Add Planned Accomplishment

    // Add Financial Report

    // Update Financial Report
}
