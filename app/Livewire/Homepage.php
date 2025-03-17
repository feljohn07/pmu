<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class Homepage extends Component
{
    public $constructionsChartData;
    public $repairsChartData;
    public $fabricationsChartData;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {

        // TODO: Get projects status

        $this->constructionsChartData = collect(
            [
                "pending" => 2,
                "ongoing" => "0",
                "completed" => "2",
            ]
        );

        $this->constructionsChartData = collect(
            [
                "pending" => 2,
                "ongoing" => "0",
                "completed" => "2",
            ]
        );

        $this->constructionsChartData = collect(
            [
                "pending" => 2,
                "ongoing" => "0",
                "completed" => "2",
            ]
        );

    }

    public function render()
    {
        return view('livewire.homepage');
    }
}
