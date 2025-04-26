<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IndividualProgramOfWork extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'individual_program_of_works';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'item_number',
        'work_description',
        'item_description',
        'quantity',
        'quantity_unit',
        'adjusted_unit_cost',
        'total_item_cost', // Often calculated, but fillable if stored directly
        'material_subtotal', // Often calculated, but fillable if stored directly
        'labor_subtotal', // Often calculated, but fillable if stored directly
        'equipment_subtotal', // Often calculated, but fillable if stored directly
        'indirect_subtotal', // Often calculated, but fillable if stored directly
        'grand_total', // Often calculated, but fillable if stored directly
        'start_date',
        'duration',
        'progress',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:4', // Match migration precision
        'adjusted_unit_cost' => 'decimal:2',
        'total_item_cost' => 'decimal:2',
        'material_subtotal' => 'decimal:2',
        'labor_subtotal' => 'decimal:2',
        'equipment_subtotal' => 'decimal:2',
        'indirect_subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'start_date' => 'date',
        'duration' => 'integer',
        'progress' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // --------------------------------------------------------------------
    // Relationships
    // --------------------------------------------------------------------

    /**
     * Get the project that this Program of Works item belongs to.
     */
    public function project(): BelongsTo
    {
        // Assumes a Project model exists in App\Models\Project
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the material costs associated with this Program of Works item.
     */
    public function materialCosts(): HasMany
    {
        return $this->hasMany(PowMaterialCost::class, 'individual_program_of_work_id');
    }

    /**
     * Get the labor costs associated with this Program of Works item.
     */
    public function laborCosts(): HasMany
    {
        return $this->hasMany(PowLaborCost::class, 'individual_program_of_work_id');
    }

    /**
     * Get the equipment costs associated with this Program of Works item.
     */
    public function equipmentCosts(): HasMany
    {
        return $this->hasMany(PowEquipmentCost::class, 'individual_program_of_work_id');
    }

    /**
     * Get the indirect cost details associated with this Program of Works item.
     */
    public function indirectCost(): HasOne
    {
        return $this->hasOne(PowIndirectCost::class, 'individual_program_of_work_id');
    }

    // --------------------------------------------------------------------
    // Accessors & Mutators (Optional - for calculated fields if not stored)
    // --------------------------------------------------------------------

    // Example: If total_item_cost wasn't stored, you could calculate it:
    // public function getTotalItemCostAttribute(): float
    // {
    //     return (float) $this->quantity * (float) $this->adjusted_unit_cost;
    // }

    // Similar accessors could be created for subtotals and grand_total
    // if you choose not to store them directly in the database.
}
