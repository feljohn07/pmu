<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * This migration creates the table to store detailed material costs
     * associated with a specific item in the individual_program_of_works table.
     */
    public function up(): void
    {
        Schema::create('pow_material_costs', function (Blueprint $table) {
            // Primary identifier for the material cost entry
            $table->id();

            // Foreign key linking to the parent POW item
            $table->foreignId('individual_program_of_work_id') // Follows convention: table_singular_id
                ->comment('Link to the specific POW item this material cost belongs to')
                ->constrained('individual_program_of_works') // Links to the main POW table
                ->onDelete('cascade'); // Delete this cost entry if the parent POW item is deleted

            // --- Material Details ---
            $table->string('description')->comment('Description of the material');
            $table->decimal('quantity', 15, 4)->default(1)->comment('Quantity of the material needed'); // Increased precision
            $table->string('unit')->nullable()->comment('Unit of measurement for the material (e.g., pcs, kg, m, l.s.)');
            $table->decimal('price', 15, 2)->default(0)->comment('Cost per unit of the material');
            $table->decimal('cost', 15, 2)->default(0)->comment('Calculated cost (quantity * price) - stored for convenience');

            // Standard Laravel timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * This drops the table if the migration needs to be rolled back.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_material_costs');
    }
};
