<?php

namespace App\Livewire;

use Livewire\Component;

class Constructions extends Component
{

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
        [
            'code_no' => 2,
            'project_name' => 'Construction of Material Recovery Facility',
            'material_cost' => 224213.20,
            'labor_cost' => 105300.00,
            'total_contract' => 1012708.70,
            'pow_status' => 'For Approval',
            'physical_accomplishment' => '100%',
            'duration' => 33,
            'implementation_status' => 'Completed',
            'remarks' => '',
            'url' => 'http://tinyurl.com/5d7chztv',
        ],
        [
            'code_no' => 3,
            'project_name' => 'Construction of Records Building',
            'material_cost' => 69601.00,
            'labor_cost' => 26750.00,
            'total_contract' => 96351.00,
            'pow_status' => 'Approved',
            'physical_accomplishment' => '100%',
            'duration' => 28,
            'implementation_status' => 'Completed',
            'remarks' => '',
            'url' => '',
        ],
        [
            'code_no' => 5,
            'project_name' => 'Construction of Organic Agriculture Classroom - CAA',
            'material_cost' => 87528.00,
            'labor_cost' => 18700.00,
            'total_contract' => 106228.00,
            'pow_status' => 'Approved',
            'physical_accomplishment' => '100%',
            'duration' => 23,
            'implementation_status' => 'Completed',
            'remarks' => 'Only 21% of the Labor Cost',
            'url' => '',
        ],
        [
            'code_no' => 9,
            'project_name' => 'Construction of Hostel Dry Wall Partition at NEW CEGS Building',
            'material_cost' => 171131.00,
            'labor_cost' => 430463.00,
            'total_contract' => 601594.00,
            'pow_status' => 'For Approval',
            'physical_accomplishment' => '63%',
            'duration' => 16,
            'implementation_status' => 'On-Going',
            'remarks' => '',
            'url' => '',
        ],
        [
            'code_no' => 16,
            'project_name' => 'Construction of Pantry & Comfort Room Extension at E-Performax Building',
            'material_cost' => 238307.30,
            'labor_cost' => 89430.20,
            'total_contract' => 327737.50,
            'pow_status' => 'Approved',
            'physical_accomplishment' => '95%',
            'duration' => 12,
            'implementation_status' => 'On-Going',
            'remarks' => '',
            'url' => 'https://shorturl.at/vAHN0',
        ],
        [
            'code_no' => 22,
            'project_name' => 'Addition of Room Partition at Guidance Office (Old Administration)',
            'material_cost' => 176493.00,
            'labor_cost' => 57776.40,
            'total_contract' => 234269.40,
            'pow_status' => 'For Approval',
            'physical_accomplishment' => '0.00%',
            'duration' => 0,
            'implementation_status' => 'Pending',
            'remarks' => '',
            'url' => '',
        ],
    ];
    public function render()
    {
        return view('livewire.constructions');
    }
}
