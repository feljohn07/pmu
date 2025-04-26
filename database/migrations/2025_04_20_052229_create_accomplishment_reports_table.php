<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accomplishment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete(); // Foreign key referencing the projects table
            $table->string('report_date'); // e.g., 'January', 'February' or Week 1, Week 2
            $table->decimal('planned_accomplishment', 5, 2)->default(0); // Storing as decimal (e.g., 10.00 for 10%)
            $table->decimal('actual_accomplishment', 5, 2)->nullable(); // Actual might not be entered yet
            $table->decimal('variance', 5, 2)->nullable(); // Variance can be calculated
            // You might add columns here later for documentation file paths if needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplishment_reports');
    }
};
