<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;


class ChartComponentExample extends Component
{

    public $chartId;
    public $chartName;
    public $completed;
    public $ongoing;
    public $pending;

    public array $myChart = [
        'type' => 'pie',
        'data' => [
            'labels' => ['Mary', 'Joe', 'Ana'],
            'datasets' => [
                [
                    'label' => '# of Votes',
                    'data' => [12, 19, 3],
                ]
            ]
        ]
    ];

    public function render()
    {
        return view('livewire.chart-component-example');
    }
}
