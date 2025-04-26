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
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Foreign key to the projects table
            $table->string('cost_type'); // e.g., 'directCost', 'indirectCost', 'governmentExpedentures', 'physicalContengencies'
            $table->integer('index'); // To maintain order
            $table->string('cost_description');
            $table->decimal('percentage', 5, 2)->nullable(); // 100.00
            $table->decimal('amount', 15, 2)->nullable(); // Adjust precision as needed
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade'); // Important: Delete costs when project is deleted

            $table->unique(['project_id', 'cost_type', 'index']); //prevent duplicate entries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_costs');
    }
};
