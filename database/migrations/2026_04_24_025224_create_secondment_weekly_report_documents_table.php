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
        Schema::create('secondment_weekly_report_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('secondment_weekly_report_id');
            $table->foreign('secondment_weekly_report_id', 'swr_doc_report_fk')
                ->references('id')
                ->on('secondment_weekly_reports')
                ->onDelete('cascade');
            
            // File information
            $table->string('original_name')->comment('Original filename uploaded by user');
            $table->string('file_name')->unique()->comment('Stored filename');
            $table->string('file_path')->comment('Path to stored file');
            $table->string('mime_type')->nullable();
            $table->integer('file_size')->comment('Size in bytes');
            
            // Document type/relationship
            $table->enum('document_type', ['photo', 'document', 'evidence'])->default('photo')->comment('Type of document');
            $table->enum('related_section', ['operations', 'innovations', 'challenges', 'general'])->nullable()->comment('Which section does this relate to?');
            
            // Metadata
            $table->string('uploaded_by_user')->nullable()->comment('User who uploaded the file');
            $table->longText('description')->nullable()->comment('User description of the document');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondment_weekly_report_documents');
    }
};
