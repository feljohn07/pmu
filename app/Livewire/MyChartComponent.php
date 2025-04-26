<?php

namespace App\Livewire;

use Livewire\Component;

class MyChartComponent extends Component
{
    // Properties to hold data (passed in or calculated)
    public string $chartName = 'Project Status'; // Example Name
    public int $completed = 0;
    public int $ongoing = 0;
    public int $pending = 0;

    // Property to hold the chart configuration for Mary UI
    public array $chartConfig = [];

    // You might pass initial data via mount
    public function mount($chartName = 'Status', $completed = 10, $ongoing = 5, $pending = 3)
    {
        $this->chartName = $chartName;
        $this->completed = $completed;
        $this->ongoing = $ongoing;
        $this->pending = $pending;

        // Initial setup of the chart configuration
        $this->updateChartConfig();
    }

    // Method to build the chart configuration array
    public function updateChartConfig()
    {
        $this->chartConfig = [
            'type' => 'doughnut',
            'data' => [
                // 'labels' => ['Completed', 'On-Going', 'Pending'], // Labels often shown in tooltip/legend
                'datasets' => [
                    [
                        // 'label' => 'Count', // Optional dataset label
                        'data' => [$this->completed, $this->ongoing, $this->pending],
                        'backgroundColor' => ['#FFA500', '#008000', '#FFD700'], // Your colors
                        'hoverOffset' => 4 // Example option
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false, // Important for custom sizing
                'cutout' => '50%', // Your cutout percentage
                'plugins' => [
                    'legend' => [
                        'display' => false // Disable default legend as you have a custom one
                    ],
                    'tooltip' => [
                        'enabled' => true // Keep tooltips if desired
                    ],
                ],
                 // Note: Displaying text in the center typically requires a Chart.js plugin,
                 // which might need extra setup beyond the basic Mary UI component.
            ]
        ];
    }

    // Example method if data needs to be updated reactively
    public function refreshChartData($newData)
    {
         $this->completed = $newData['completed'] ?? 0;
         $this->ongoing = $newData['ongoing'] ?? 0;
         $this->pending = $newData['pending'] ?? 0;
         $this->updateChartConfig(); // Re-build the config array, Livewire/MaryUI handles the update
    }


    public function render()
    {
        // The view that uses the <x-mary-chart> component
        return view('livewire.my-chart-component');
    }
}