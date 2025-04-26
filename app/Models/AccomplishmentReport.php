<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccomplishmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'report_date',
        'planned_accomplishment',
        'actual_accomplishment',
        'variance',
        // Add documentation fields here if added to migration
    ];

    // Define the relationship to the Project model
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Optional: Add a method to calculate variance if not storing it directly
    // public function getVarianceAttribute()
    // {
    //     if ($this->actual_accomplishment !== null) {
    //         return $this->actual_accomplishment - $this->planned_accomplishment;
    //     }
    //     return null;
    // }

    public function documentationUploads(): HasMany
    {
        return $this->hasMany(DocumentationUpload::class);
    }
}
