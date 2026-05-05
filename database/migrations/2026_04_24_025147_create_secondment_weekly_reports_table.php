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
        Schema::create('secondment_weekly_reports', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            
            // Basic Information Section
            $table->date('reporting_week')->nullable()->comment('Calendar selection - week ending date');
            $table->string('name')->nullable()->comment('Auto-filled from user');
            $table->string('role')->nullable()->comment('Auto-filled from user');
            $table->string('city')->nullable()->comment('Auto-filled from venue');
            
            // Weekly Activities Section
            $table->longText('main_activities')->nullable()->comment('Main activities and responsibilities');
            
            // Gained Experience Section
            $table->longText('experience_gained')->nullable()->comment('Experience gained this week');
            
            // Innovation Section
            $table->longText('innovation_description')->nullable()->comment('Innovation observed this week');
            
            // Challenges Section
            $table->longText('challenges_description')->nullable()->comment('Challenges faced this week');
            $table->boolean('challenges_resolved')->default(false)->comment('Was the challenge resolved?');
            
            // Value for Qatar Section
            $table->boolean('value_for_qatar')->default(false)->comment('Should this be applied for future events?');
            $table->enum('value_for_qatar_type', ['Must Have', 'Good to Have', 'Requires further assessment'])->nullable()->comment('Conditional if value_for_qatar is true');
            $table->longText('value_for_qatar_description')->nullable()->comment('Conditional description');
            
            // HR / Wellbeing Section
            $table->enum('wellbeing_status', ['Good', 'Moderate', 'Challenging'])->nullable()->comment('How is your wellbeing?');
            $table->boolean('needs_support')->default(false)->comment('Do you need support?');
            $table->json('support_types')->nullable()->comment('Multi-select: Workload, Accommodation, Logistics, Health, Other');
            $table->longText('support_other_description')->nullable()->comment('Conditional - if Other is selected');
            
            // Additional Comment
            $table->longText('additional_comment')->nullable()->comment('Optional additional comment');
            
            // Status and tracking
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondment_weekly_reports');
    }
};
