<?php

namespace App\Models;

use Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Log;

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
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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

    /**
     * Get the count of projects considered 'on-going' based on calculated end date.
     *
     * An project is on-going if today's date is between the start_date (inclusive)
     * and the calculated end date (start_date + duration days, inclusive).
     * Assumes 'duration' is in days and the database is SQLite.
     *
     * @param string|null $category Optional category to filter by.
     * @return int
     */
    public static function getOngoingCount(?string $category = null): int
    {
        // Use today() for date-only comparison. Use now() if time is relevant.
        // Current time based on context: 2025-04-26
        $today = Carbon::today();

        // Base query
        $query = static::query(); // Use static::query() within a static method

        // Condition: start_date <= today AND today <= calculated_end_date
        // Use whereDate to safely compare only the date part of start_date
        $query->whereDate('start_date', '<=', $today);

        // Use whereRaw for the calculated end date comparison using SQLite syntax
        // DATE(start_date, '+' || duration || ' days') >= today
        $query->whereRaw("DATE(start_date, '+' || duration || ' days') >= ?", [$today]);

        // Add category filter if provided
        if ($category !== null) {
            $query->where('category', $category);
        }

        // Return the count
        return $query->count();
    }

    /**
     * Get the count of projects considered 'completed' based on calculated end date.
     * Completed means the calculated end date is before today.
     * Assumes 'duration' is in days and the database is SQLite.
     *
     * @param string|null $category Optional category to filter by.
     * @return int
     */
    public static function getCompletedCount(?string $category = null): int
    {
        $today = Carbon::today();
        $query = static::query();

        // Completed: calculated_end_date < today
        $query->whereRaw("DATE(start_date, '+' || duration || ' days') < ?", [$today]);

        if ($category !== null) {
            $query->where('category', $category);
        }
        return $query->count();
    }

    /**
     * Get the count of projects considered 'pending' (not yet started).
     * Pending means the start date is after today.
     *
     * @param string|null $category Optional category to filter by.
     * @return int
     */
    public static function getPendingCount(?string $category = null): int
    {
        $today = Carbon::today();
        $query = static::query();

        // Pending: start_date > today
        $query->whereDate('start_date', '>', $today);

        if ($category !== null) {
            $query->where('category', $category);
        }
        return $query->count();
    }


    /**
     * Get the calculated end date based on start_date and duration.
     * Returns a Carbon object or null if calculation is not possible.
     *
     * Access via: $project->calculated_end_date
     */
    public function calculatedEndDate()
    {

        // Current time based on context: 2025-04-26
        $today = Carbon::today(); // Use today() for date-only comparison

        // Cast start_date just in case it wasn't automatically cast (though $casts should handle it)
        $startDate = $this->start_date;

        // Calculate the end date using Carbon
        // copy() prevents modifying the original start_date object
        $calculatedEndDate = $startDate->copy()->addDays($this->duration);

        return $calculatedEndDate;
    }


    /**
     * Check the current status of the project based on dates.
     * Assumes 'duration' is in days.
     *
     * @return string ('Pending', 'Ongoing', 'Completed', 'Unknown')
     */
    public function checkProjectStatus(): string
    {
        // return !$this->start_date instanceof Carbon || !is_numeric($this->duration) || $this->duration < 0;
        // Ensure start_date and duration are available and valid
        if (!$this->start_date instanceof Carbon || !is_numeric($this->duration) || $this->duration < 0) {
            // You might want to log this situation or handle it differently
            return 'Unknown'; // Or perhaps default to 'Pending' or based on 'implementation_status'
        }

        // Current time based on context: 2025-04-26
        $today = Carbon::today(); // Use today() for date-only comparison

        // Cast start_date just in case it wasn't automatically cast (though $casts should handle it)
        $startDate = $this->start_date;

        // Calculate the end date using Carbon
        // copy() prevents modifying the original start_date object
        $calculatedEndDate = $startDate->copy()->addDays($this->duration);


        // Check the conditions
        if ($startDate->isAfter($today)) {
            return 'Pending';
        } elseif ($today->betweenIncluded($startDate, $calculatedEndDate)) {
            // betweenIncluded checks if $today >= $startDate AND $today <= $calculatedEndDate
            return 'Ongoing';
        } elseif ($calculatedEndDate->isBefore($today)) {
            return 'Completed';
        } else {
            // This case might occur if start_date is today and duration is 0,
            // or potentially other edge cases depending on exact time vs. date comparison.
            // Decide how to handle this - often considered 'Ongoing' if start_date is today.
            // Let's refine the 'Ongoing' check slightly for clarity if start == today
            if ($startDate->isSameDay($today)) {
                return 'Ongoing'; // Started today
            }
            // If it's not pending, not completed, and not started today, it implies ongoing.
            // The betweenIncluded should cover most cases, but this is a fallback.
            // If calculatedEndDate is also today, it's effectively completed today, but often still shown as 'Ongoing' until tomorrow.
            // Let's stick to the logic: if today is <= calculatedEndDate, it's ongoing (or pending if start is future)
            if ($today->lte($calculatedEndDate)) {
                return 'Ongoing';
            }

            return 'Unknown'; // Fallback if logic doesn't catch it
        }
    }

    public function calculateTotalAccomplishment(): float
    {
        // Same logic as the accessor's fallback
        if (method_exists($this, 'accomplishmentReports')) {
            return $this->accomplishmentReports()->sum('actual_accomplishment') ?? 0.0;
        }
        // Log::warning("Attempted to calculateTotalAccomplishment, but the 'accomplishmentReports' relationship method is missing or incorrect in Project ID {$this->id}.");
        return 0.0;
    }

    /**
     * NEW METHOD: Check if the project has any associated Program of Works records.
     *
     * @return bool True if at least one POW record exists, false otherwise.
     */
    public function hasPOW(): bool
    {
        // Check if the relationship method exists first for robustness
        if (!method_exists($this, 'individualProgramOfWorks')) {
            // Log::warning("Attempted to call hasPOW, but the 'individualProgramOfWorks' relationship method is missing in the Project model.");
            return false; // Or throw an exception
        }

        // Use the exists() method on the relationship for an efficient database check.
        // This avoids loading the related models into memory.
        return $this->individualProgramOfWorks()->exists();
    }

    public function approve(): bool
    {
        try {

            $this->approval_status = 'approved';

            return $this->save(); // Save changes to the database
        } catch (\Exception $e) {
            Log::error("Failed to approve Project ID {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * NEW STATIC METHOD: Get the count of projects where the POW status
     * is 'for-approval'.
     *
     * @param string|null $category Optional category to filter by.
     * @return int
     */
    public static function getForApprovalPOWCount(?string $category = null): int
    {
        // Start the query
        $query = static::query();

        // Filter by the specific pow_status value
        $query->where('pow_status', 'for-approval');

        // Add condition: Must have at least one related POW record
        $query->whereHas('individualProgramOfWorks');

        // Add category filter if provided
        if ($category !== null) {
            $query->where('category', $category);
        }

        // Return the count
        return $query->count();
    }

    /**
     * NEW STATIC METHOD: Get the count of projects that do not have any related
     * Individual Program of Works records.
     *
     * @param string|null $category Optional category to filter by.
     * @return int
     */
    public static function getProjectsWithoutPOWCount(?string $category = null): int
    {
        // Start the query
        $query = static::query();

        // Use doesntHave() to filter projects where the relationship has no records
        // Parameter is the name of the relationship method
        $query->doesntHave('individualProgramOfWorks');

        // Add category filter if provided
        if ($category !== null) {
            $query->where('category', $category);
        }

        // Return the count
        return $query->count();
    }
    public function getTotalProgress(string $column = 'actual_accomplishment'): float|int
    {
        // Access the related accomplishments via the defined relationship ('accomplishments()')
        // Use the sum() aggregate function provided by Laravel's query builder/Eloquent
        // Pass the column name you want to sum.
        // Use '?? 0' to return 0 if there are no accomplishments or the sum is null.
        return $this->accomplishments()->sum($column) ?? 0;
    }

    // TODO add totalPOWCost

    public function calculateTotalPOWCost(): float
    {
        // Check if the relationship method exists first for robustness
        if (!method_exists($this, 'individualProgramOfWorks')) {
            // Log::warning("Attempted to call calculateTotalPOWCost, but the 'individualProgramOfWorks' relationship method is missing in the Project model.");
            return 0.0; // Or throw an exception
        }

        // Use the sum() method on the relationship to efficiently calculate the total cost
        // This avoids loading all related models into memory
        return $this->individualProgramOfWorks()->sum('grand_total');
    }

}

