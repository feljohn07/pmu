<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class LineChartCard extends Component
{

    public $chartId;
    public $chartName;
    public $approved;
    public $forApproval;
    public $noPow;

    public function __construct($approved = 0, $forApproval = 0, $noPow, $chartName)
    {
        $this->approved = $approved;
        $this->forApproval = $forApproval;
        $this->noPow = $noPow;
        $this->chartName = $chartName;
        $this->chartId = Str::random(5);
    }

    public function render(): View|Closure|string
    {
        return view('components.line-chart-card');
    }
}
