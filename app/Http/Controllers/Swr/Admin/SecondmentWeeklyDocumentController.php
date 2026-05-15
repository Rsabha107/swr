<?php

namespace App\Http\Controllers\Swr\Admin;

use App\Http\Controllers\Controller;

use App\Models\Swr\SecondmentWeeklyReport;
use App\Models\Swr\SecondmentWeeklyReportDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SecondmentWeeklyDocumentController extends Controller
{
    public function exportImages(SecondmentWeeklyReport $report)
    {
        $zip = new ZipArchive();

        $zipFileName = "report-{$report->id}-images.zip";
        $zipPath = storage_path("app/tmp/{$zipFileName}");

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Cannot create zip file');
        }

        foreach ($report->documents as $doc) {
            if ($doc->mime_type && str_starts_with($doc->mime_type, 'image/')) {
                $content = Storage::disk($doc->disk)->get($doc->file_path);
                
                // Get the file extension
                $extension = pathinfo($doc->file_name ?? $doc->original_name ?? $doc->file_path, PATHINFO_EXTENSION);
                
                // Get the base name (prioritize description, then original_name, then file_name)
                $baseName = $doc->description ?? $doc->original_name ?? pathinfo($doc->file_name ?? basename($doc->file_path), PATHINFO_FILENAME);
                
                // Ensure the filename has an extension
                $fileName = $baseName;
                if ($extension && !str_ends_with(strtolower($fileName), '.' . strtolower($extension))) {
                    $fileName .= '.' . $extension;
                }
                
                $zip->addFromString($fileName, $content);
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    public function download(SecondmentWeeklyReportDocument $document)
    {
        Log::info('Request to download SWR document: ' . $document->id);
        
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        Log::info('Downloading SWR document: ' . $document->id);

        abort_unless(Storage::disk($document->disk)->exists($document->file_path), 404);

        // inline preview for images/pdf
        return Storage::disk($document->disk)->response($document->file_path, $document->original_name ?? basename($document->file_path), [
            'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? basename($document->file_path)) . '"',
        ]);

        // download for other file types
        return Storage::disk($document->disk)->download(
            $document->file_path,
            $document->original_name ?? basename($document->file_path)
        );
    }

    public function view(SecondmentWeeklyReportDocument $document)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        
        $disk = Storage::disk($document->disk);
        abort_unless($disk->exists($document->file_path), 404);

        return response($disk->get($document->file_path), 200, [
            'Content-Type' => $document->mime_type ?? 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? basename($document->file_path)) . '"',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function destroy(SecondmentWeeklyReportDocument $document)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        
        Storage::disk($document->disk)->delete($document->file_path);
        $document->delete();

        Log::info('Deleted SWR document: ' . $document->id);

        return response()->json(['error' => false, 'message' => 'Document deleted']);
    }
}
