<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class ChartCard extends Component
{
    public $chartId;
    public $chartName;
    public $completed;
    public $ongoing;
    public $pending;


    public function __construct($completed = 0, $ongoing = 0, $pending = 0, $chartName)
    {
        $this->completed = $completed;
        $this->ongoing = $ongoing;
        $this->pending = $pending;
        $this->chartName = $chartName;
        $this->chartId = Str::random(5);
    }

    public function render()
    {
        return view('components.chart-card');
    }
}
