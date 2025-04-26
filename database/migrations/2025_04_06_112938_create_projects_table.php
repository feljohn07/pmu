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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->decimal('material_cost', 15, 2);
            $table->decimal('labor_cost', 15, 2);
            $table->decimal('total_contract_amount', 15, 2);
            $table->string('pow_status');
            $table->string('physical_accomplishment');
            $table->integer('duration');
            $table->string('implementation_status');
            $table->text('remarks');
            $table->string('url');
            $table->string('category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
