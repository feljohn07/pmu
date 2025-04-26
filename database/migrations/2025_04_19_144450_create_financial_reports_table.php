<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id') // Foreign key linking to the projects table
                ->constrained()        // Assumes a 'projects' table with an 'id' column
                ->onDelete('cascade');  // If a project is deleted, delete its reports too
            $table->text('report_date');  // To store the specific date or start of the month/period
            $table->decimal('amount', 15, 2); // Suitable for monetary values (15 total digits, 2 decimal places)
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
