<?php

namespace App\Livewire;

use Livewire\Component;

class ConstructionView extends Component
{

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

    public $projects = [
        [
            'code_no' => 1,
            'project_name' => 'University Gymnasium and Cultural Center',
            'material_cost' => 718458.70,
            'labor_cost' => 294250.00,
            'total_contract' => 25000000.00,
            'pow_status' => 'Approved',
            'physical_accomplishment' => '8.31%',
            'duration' => 200,
            'implementation_status' => 'On-Going',
            'remarks' => '',
            'url' => 'https://shorturl.at/cefLU',
        ],

    ];

    public function render()
    {
        return view('livewire.construction-view');
    }
}
