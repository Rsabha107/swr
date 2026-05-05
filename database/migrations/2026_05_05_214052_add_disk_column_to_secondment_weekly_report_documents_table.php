<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('secondment_weekly_report_documents', function (Blueprint $table) {
            $table->string('disk')->default('local')->after('file_path');
        });
        
        // Update existing records to use 'public' disk since they were stored there
        DB::table('secondment_weekly_report_documents')->update(['disk' => 'public']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secondment_weekly_report_documents', function (Blueprint $table) {
            $table->dropColumn('disk');
        });
    }
};
