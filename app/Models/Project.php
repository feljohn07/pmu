<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'id',
        'project_name',
        'start_date',
        'end_date',
        'material_cost',
        'labor_cost',
        'total_contract_amount',
        'pow_status',
        'physical_accomplishment',
        'duration',
        'implementation_status',
        'remarks',
        'category',
        'url',
        'project_category',
        'appropriation',
        'source_of_funds',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'material_cost' => 'float',
        'labor_cost' => 'float',
        'total_contract_amount' => 'float',
        'physical_accomplishment' => 'float',
        'duration' => 'integer',
    ];

    /**
     * The users that belong to the project.
     */
    public function users(): BelongsToMany
    {
        // Laravel automatically figures out the pivot table and keys
        $relationship = $this->belongsToMany(User::class);

        // If you added timestamps to the pivot table:
        // $relationship->withTimestamps();

        // If you added extra pivot columns (like 'role'):
        // $relationship->withPivot('role');

        return $relationship;
    }

    public function accomplishmentReports()
    {
        return $this->hasMany(AccomplishmentReport::class);
    }

    public function financialReports(): HasMany
    {
        return $this->hasMany(FinancialReport::class);
    }


    public function individualProgramOfWorks(): HasMany
    {
        return $this->hasMany(IndividualProgramOfWork::class);
    }

    public function projectCosts(): HasMany
    {
        return $this->hasMany(ProjectCost::class);
    }
}
