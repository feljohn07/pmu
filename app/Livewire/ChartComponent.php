<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Faker\Factory as Faker;

class ChartComponent extends Component
{
    public array $chartConfig = [
        'type' => 'pie',
        'data' => [
            'labels' => ['Mary', 'Joe', 'Ana'],
            'datasets' => [
                [
                    'label' => '# of Votes',
                    'data' => [12, 19, 3],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ],
    ];
    public function randomize()
    {
        $faker = Faker::create();
        Arr::set($this->chartConfig, 'data.datasets.0.data', [
            $faker->numberBetween(1, 100),
            $faker->numberBetween(1, 100),
            $faker->numberBetween(1, 100),
        ]);
        $this->emit('refreshChart', $this->chartConfig);
    }

    public function switchType()
    {
        $type = $this->chartConfig['type'] === 'bar' ? 'pie' : 'bar';
        Arr::set($this->chartConfig, 'type', $type);
        $this->emit('refreshChart', $this->chartConfig);
    }

    public function render()
    {
        return view('livewire.chart-component');
    }
}
