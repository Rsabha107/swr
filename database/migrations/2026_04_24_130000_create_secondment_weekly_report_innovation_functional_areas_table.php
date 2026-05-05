<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('secondment_weekly_report_innovation_functional_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secondment_weekly_report_id')->constrained('secondment_weekly_reports', 'id')->onDelete('cascade');
            $table->foreignId('functional_area_id')->constrained('functional_areas', 'id')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['secondment_weekly_report_id', 'functional_area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondment_weekly_report_innovation_functional_areas');
    }
};
