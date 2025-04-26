<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowMaterialCost extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pow_material_costs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'individual_program_of_work_id',
        'description',
        'quantity',
        'unit',
        'price',
        'cost', // Often calculated (quantity * price), but fillable if stored
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:4', // Match migration precision
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --------------------------------------------------------------------
    // Relationships
    // --------------------------------------------------------------------

    /**
     * Get the parent Program of Works item that this material cost belongs to.
     */
    public function programOfWork(): BelongsTo
    {
        return $this->belongsTo(IndividualProgramOfWork::class, 'individual_program_of_work_id');
    }
}
