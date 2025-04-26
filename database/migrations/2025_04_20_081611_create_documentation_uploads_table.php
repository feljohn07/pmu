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
        Schema::create('documentation_uploads', function (Blueprint $table) {
            $table->id();
            // Add a foreign key to link to the accomplishment report
            $table->foreignId('accomplishment_report_id')->constrained('accomplishment_reports')->onDelete('cascade');
            $table->string('url'); // To store the path/URL of the uploaded file
            $table->boolean('approved')->default(false); // Approval status
            $table->timestamps(); // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_uploads');
    }
};
