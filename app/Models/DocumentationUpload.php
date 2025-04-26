<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentationUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'accomplishment_report_id',
        'url',
        'approved',
    ];

    /**
     * Get the accomplishment report that owns the documentation.
     */
    public function accomplishmentReport(): BelongsTo
    {
        return $this->belongsTo(AccomplishmentReport::class);
    }
}
