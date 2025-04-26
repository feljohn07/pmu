<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowLaborCost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pow_labor_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'individual_program_of_work_id',
        'description',
        'number_of_manpower',
        'number_of_days',
        'rate_per_day',
        'cost', // Often calculated, but fillable if stored
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'number_of_manpower' => 'decimal:2',
        'number_of_days' => 'decimal:2',
        'rate_per_day' => 'decimal:2',
        'cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --------------------------------------------------------------------
    // Relationships
    // --------------------------------------------------------------------

    /**
     * Get the parent Program of Works item that this labor cost belongs to.
     */
    public function programOfWork(): BelongsTo
    {
        return $this->belongsTo(IndividualProgramOfWork::class, 'individual_program_of_work_id');
    }
}
