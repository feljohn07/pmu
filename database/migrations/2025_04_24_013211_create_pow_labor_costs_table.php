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
        Schema::create('pow_labor_costs', function (Blueprint $table) {
            // Primary identifier for the labor cost entry
            $table->id();

            // Foreign key linking to the parent POW item
            $table->foreignId('individual_program_of_work_id')
                ->comment('Link to the specific POW item this labor cost belongs to')
                ->constrained('individual_program_of_works')
                ->onDelete('cascade'); // Delete this cost entry if the parent POW item is deleted

            // --- Labor Details ---
            $table->string('description')->comment('Description of the labor role (e.g., Project Manager, Skilled Labor, Carpenter)');
            $table->decimal('number_of_manpower', 8, 2)->default(1)->comment('Number of personnel in this role (allows for fractions)');
            $table->decimal('number_of_days', 8, 2)->default(1)->comment('Number of days required for this labor (allows for fractions)');
            $table->decimal('rate_per_day', 15, 2)->default(0)->comment('Cost per day for one unit of this labor role');
            $table->decimal('cost', 15, 2)->default(0)->comment('Calculated cost (manpower * days * rate) - stored for convenience');

            // Standard Laravel timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_labor_costs');
    }
};
