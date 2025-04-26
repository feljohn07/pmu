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
        Schema::create('individual_program_of_works', function (Blueprint $table) {
            $table->id(); // Primary key (auto-incrementing BigInt)

            // Foreign key linking to the projects table
            $table->foreignId('project_id')
                ->constrained('projects') // Assumes 'projects' table name
                ->onUpdate('cascade') // Optional: update if project id changes
                ->onDelete('cascade'); // Delete sub-tasks if the project is deleted

            $table->date('start_date');       // Start date of the sub-task
            $table->integer('duration');      // Duration (e.g., in days) - add comment if needed
            $table->unsignedTinyInteger('progress')->default(0); // Progress percentage (0-100), defaults to 0
            // Use unsignedTinyInteger (0-255 range) for efficiency if progress is always 0-100

            $table->timestamps(); // Adds created_at and updated_at columns

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_program_of_works');
    }
};
