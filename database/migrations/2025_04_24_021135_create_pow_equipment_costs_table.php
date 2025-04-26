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
        Schema::create('pow_equipment_costs', function (Blueprint $table) {
            // Primary identifier for the equipment cost entry
            $table->id();

            // Foreign key linking to the parent POW item
            $table->foreignId('individual_program_of_work_id')
                ->comment('Link to the specific POW item this equipment cost belongs to')
                ->constrained('individual_program_of_works')
                ->onDelete('cascade'); // Delete this cost entry if the parent POW item is deleted

            // --- Equipment Details ---
            $table->string('description')->comment('Description of the equipment (e.g., Backhoe, Concrete Mixer, Truck Rental)');
            $table->decimal('number_of_units', 8, 2)->default(1)->comment('Number of units of this equipment');
            $table->decimal('number_of_days', 8, 2)->default(1)->comment('Number of days the equipment is used (allows for fractions)');
            $table->decimal('rate_per_day', 15, 2)->default(0)->comment('Rental or operational cost per unit per day');
            $table->decimal('cost', 15, 2)->default(0)->comment('Calculated cost (units * days * rate) - stored for convenience');

            // Standard Laravel timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_equipment_costs');
    }
};
