<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'report_date',
        'amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'report_date' => 'text', // Automatically cast the report_date to a Carbon date object
        'amount' => 'decimal:2', // Cast amount to a decimal with 2 places
    ];

    /**
     * Get the project that owns the financial report.
     */
    public function project(): BelongsTo
    {
        // Assumes you have a Project model (App\Models\Project)
        return $this->belongsTo(Project::class);
    }
}
