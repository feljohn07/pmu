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
        Schema::create('pow_indirect_costs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('individual_program_of_work_id')
                ->unique()
                ->constrained('individual_program_of_works')
                ->onDelete('cascade');

            $table->string('b1_description')->nullable()->default('Overhead Expenses');
            $table->decimal('b1_base_cost', 15, 2)->nullable()->default(0);
            $table->decimal('b1_markup_percent', 8, 4)->nullable()->default(0);
            $table->decimal('b1_markup_value', 15, 2)->nullable()->default(0);

            $table->string('b2_description')->nullable()->default('Contingencies');
            $table->decimal('b2_base_cost', 15, 2)->nullable()->default(0);
            $table->decimal('b2_markup_percent', 8, 4)->nullable()->default(0);
            $table->decimal('b2_markup_value', 15, 2)->nullable()->default(0);

            $table->string('b3_description')->nullable()->default('Miscellaneous');
            $table->decimal('b3_base_cost', 15, 2)->nullable()->default(0);
            $table->decimal('b3_markup_percent', 8, 4)->nullable()->default(0);
            $table->decimal('b3_markup_value', 15, 2)->nullable()->default(0);

            // CORRECTED LINE: Escape the single quote (apostrophe) with a backslash
            $table->string('b4_description')->nullable()->default('Contracto`r Profit');
            $table->decimal('b4_base_cost', 15, 2)->nullable()->default(0);
            $table->decimal('b4_markup_percent', 8, 4)->nullable()->default(0);
            $table->decimal('b4_markup_value', 15, 2)->nullable()->default(0);

            $table->string('b5_description')->nullable()->default('VAT Component');
            $table->decimal('b5_base_cost', 15, 2)->nullable()->default(0);
            $table->decimal('b5_markup_percent', 8, 4)->nullable()->default(0);
            $table->decimal('b5_markup_value', 15, 2)->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_indirect_costs');
    }
};
