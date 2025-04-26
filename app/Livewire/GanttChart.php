<?php

namespace App\Livewire;

use Livewire\Component;

class GanttChart extends Component
{
    public $tasks = [];

    public function mount()
    {
        // Example Gantt chart data
        $this->tasks = [
            [
                'id' => 'Task 1',
                'name' => 'Project Initiation',
                'start' => '2025-04-15',
                'end' => '2025-04-18',
                'progress' => 100,
                'dependencies' => '',
            ],
            [
                'id' => 'Task 2',
                'name' => 'Requirements Gathering',
                'start' => '2025-04-18',
                'end' => '2025-04-22',
                'progress' => 50,
                'dependencies' => 'Task 1',
            ],
            [
                'id' => 'Task 3',
                'name' => 'Design Phase',
                'start' => '2025-04-22',
                'end' => '2025-04-26',
                'progress' => 0,
                'dependencies' => 'Task 2',
            ],
            [
                'id' => 'Task 4',
                'name' => 'Development',
                'start' => '2025-04-26',
                'end' => '2025-05-02',
                'progress' => 20,
                'dependencies' => 'Task 3',
            ],
            [
                'id' => 'Task 5',
                'name' => 'Testing',
                'start' => '2025-05-02',
                'end' => '2025-05-05',
                'progress' => 0,
                'dependencies' => 'Task 4',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.gantt-chart');
    }
}
