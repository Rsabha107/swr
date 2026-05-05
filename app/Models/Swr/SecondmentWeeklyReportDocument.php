<?php

namespace App\Models\Swr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecondmentWeeklyReportDocument extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'secondment_weekly_report_id',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'document_type',
        'related_section',
        'uploaded_by_user',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    
    /**
     * The report this document belongs to
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(SecondmentWeeklyReport::class, 'secondment_weekly_report_id');
    }

    // Helper methods
    
    public function getFileSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isDocument()
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
