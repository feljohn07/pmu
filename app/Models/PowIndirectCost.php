<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowIndirectCost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pow_indirect_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'individual_program_of_work_id',
        'b1_description',
        'b1_base_cost',
        'b1_markup_percent',
        'b1_markup_value', // Often calculated, but fillable if stored
        'b2_description',
        'b2_base_cost',
        'b2_markup_percent',
        'b2_markup_value', // Often calculated, but fillable if stored
        'b3_description',
        'b3_base_cost',
        'b3_markup_percent',
        'b3_markup_value', // Often calculated, but fillable if stored
        'b4_description',
        'b4_base_cost',
        'b4_markup_percent',
        'b4_markup_value', // Often calculated, but fillable if stored
        'b5_description',
        'b5_base_cost',
        'b5_markup_percent',
        'b5_markup_value', // Often calculated, but fillable if stored
        // 'total_indirect_cost' // If you add this column
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'b1_base_cost' => 'decimal:2',
        'b1_markup_percent' => 'decimal:4', // Match migration precision
        'b1_markup_value' => 'decimal:2',
        'b2_base_cost' => 'decimal:2',
        'b2_markup_percent' => 'decimal:4',
        'b2_markup_value' => 'decimal:2',
        'b3_base_cost' => 'decimal:2',
        'b3_markup_percent' => 'decimal:4',
        'b3_markup_value' => 'decimal:2',
        'b4_base_cost' => 'decimal:2',
        'b4_markup_percent' => 'decimal:4',
        'b4_markup_value' => 'decimal:2',
        'b5_base_cost' => 'decimal:2',
        'b5_markup_percent' => 'decimal:4',
        'b5_markup_value' => 'decimal:2',
        // 'total_indirect_cost' => 'decimal:2', // If you add this column
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --------------------------------------------------------------------
    // Relationships
    // --------------------------------------------------------------------

    /**
     * Get the parent Program of Works item that these indirect costs belong to.
     */
    public function programOfWork(): BelongsTo
    {
        return $this->belongsTo(IndividualProgramOfWork::class, 'individual_program_of_work_id');
    }

     // --------------------------------------------------------------------
    // Accessors & Mutators (Optional)
    // --------------------------------------------------------------------

    // Example: Calculate total indirect cost if not stored
    // public function getTotalIndirectCostAttribute(): float
    // {
    //     return (float) $this->b1_markup_value +
    //            (float) $this->b2_markup_value +
    //            (float) $this->b3_markup_value +
    //            (float) $this->b4_markup_value +
    //            (float) $this->b5_markup_value;
    // }
}
