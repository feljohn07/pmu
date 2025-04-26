    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        /**
         * Run the migrations.
         *
         * This migration adds detailed columns for work items, cost breakdowns,
         * and adjusts scheduling columns in the existing 'individual_program_of_works' table.
         */
        public function up(): void
        {
            // Check if the table exists before attempting to modify it
            if (Schema::hasTable('individual_program_of_works')) {
                Schema::table('individual_program_of_works', function (Blueprint $table) {
                    // Ensure columns are added after a specific existing column for organization, if desired.
                    // Example: ->after('project_id')
                    // If order doesn't matter strictly, you can omit ->after().

                    // --- Add Core POW Item Details ---
                    $table->string('item_number')->nullable()->comment('Standard item number (e.g., A.1.a, II.b)')->after('project_id');
                    $table->text('work_description')->nullable()->comment('General description of the work category')->after('item_number');
                    $table->text('item_description')->nullable()->comment('Specific description of this particular work item')->after('work_description');
                    $table->decimal('quantity', 15, 4)->default(0)->comment('Quantity of the work item')->after('item_description'); // Increased precision
                    $table->string('quantity_unit')->nullable()->comment('Unit of measurement (e.g., l.s, unit, pcs, bd ft, sqm, cu.m)')->after('quantity');
                    $table->decimal('adjusted_unit_cost', 15, 2)->default(0)->comment('The final unit cost after adjustments')->after('quantity_unit');
                    $table->decimal('total_item_cost', 15, 2)->default(0)->comment('Calculated total cost for this item (quantity * adjusted_unit_cost)')->after('adjusted_unit_cost');

                    // --- Add Cost Subtotals ---
                    $table->decimal('material_subtotal', 15, 2)->default(0)->comment('Sum of all material costs for this item')->after('total_item_cost');
                    $table->decimal('labor_subtotal', 15, 2)->default(0)->comment('Sum of all labor costs for this item')->after('material_subtotal');
                    $table->decimal('equipment_subtotal', 15, 2)->default(0)->comment('Sum of all equipment costs for this item')->after('labor_subtotal');
                    $table->decimal('indirect_subtotal', 15, 2)->default(0)->comment('Sum of all indirect costs (OCM, Profit, VAT, etc.) for this item')->after('equipment_subtotal');
                    $table->decimal('grand_total', 15, 2)->default(0)->comment('The overall total cost for this POW item (Direct + Indirect)')->after('indirect_subtotal');

                    // --- Modify Existing Scheduling Columns (if needed) ---
                    // Example: Make start_date nullable if it wasn't already
                    // $table->date('start_date')->nullable()->comment('Planned start date for this work item')->change();
                    // Example: Set a default for duration if it didn't have one
                    // $table->integer('duration')->default(1)->comment('Planned duration of the work item (e.g., in days)')->change();
                    // Progress column likely already exists as defined in the original migration
                });
            }
        }

        /**
         * Reverse the migrations.
         *
         * This migration removes the columns added in the 'up' method,
         * reverting the table schema back to its previous state.
         */
        public function down(): void
        {
            // Check if the table exists before attempting to modify it
            if (Schema::hasTable('individual_program_of_works')) {
                Schema::table('individual_program_of_works', function (Blueprint $table) {
                    // Drop columns in reverse order of addition (optional, but good practice)
                    $table->dropColumn([
                        'item_number',
                        'work_description',
                        'item_description',
                        'quantity',
                        'quantity_unit',
                        'adjusted_unit_cost',
                        'total_item_cost',
                        'material_subtotal',
                        'labor_subtotal',
                        'equipment_subtotal',
                        'indirect_subtotal',
                        'grand_total',
                    ]);

                    // Optionally revert changes made to existing columns in the 'up' method
                    // Be careful: Ensure the original state is known or acceptable.
                    // Example: Revert start_date nullability (only if it was NOT nullable before)
                    // $table->date('start_date')->nullable(false)->change();
                    // Example: Revert duration default (only if it had no default before)
                    // $table->integer('duration')->default(null)->change(); // Or remove default entirely if possible/needed
                });
            }
        }
    };
