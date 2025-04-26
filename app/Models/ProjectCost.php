<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCost extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'project_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'cost_type',
        'index',
        'cost_description',
        'percentage',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'project_id' => 'integer',
        'percentage' => 'decimal:2', // Cast to decimal with 2 places
        'amount' => 'decimal:2',    // Cast to decimal with 2 places
    ];

    /**
     * Get the project that owns the cost.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
