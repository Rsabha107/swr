<?php

namespace App\Http\Controllers\Wdr\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wdr\GuardianDocument;
use App\Models\Wdr\ParticipantDocument;
use App\Models\Wdr\WorkforceDailyReport;
use App\Models\Wdr\WorkforceDailyReportDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class WorkforceReportDocumentController extends Controller
{
    public function exportImages(WorkforceDailyReport $report)
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

        foreach ($report->photos as $doc) {
            if ($doc->mime && str_starts_with($doc->mime, 'image/')) {
                $content = Storage::disk($doc->disk)->get($doc->path);
                $zip->addFromString($doc->custom_name ?? $doc->original_name ?? basename($doc->path), $content);
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    public function download(WorkforceDailyReportDocument $document)
    {
        Log::info('Request to download guardian document: ' . $document->id);
        // TODO: add policy check (important!)
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        Log::info('Downloading guardian document: ' . $document->id);

        abort_unless(Storage::disk($document->disk)->exists($document->path), 404);

        // inline preview for images/pdf
        return Storage::disk($document->disk)->response($document->path, $document->original_name ?? basename($document->path), [
            'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? basename($document->path)) . '"',
        ]);

        // download for other file types
        return Storage::disk($document->disk)->download(
            $document->path,
            $document->original_name ?? basename($document->path)
        );
    }

    public function view(WorkforceDailyReportDocument $document)
    {
        // TODO authorize

        // Log::info('doc: ' . json_encode($document));
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        $disk = Storage::disk($document->disk);
        abort_unless($disk->exists($document->path), 404);

        return response($disk->get($document->path), 200, [
            'Content-Type' => $document->mime ?? 'image/jpeg',
            'Content-Disposition' => 'inline; filename="' . ($document->original_name ?? basename($document->path)) . '"',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function destroy(WorkforceDailyReportDocument $document)
    {
        // TODO: add policy check (important!)
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();

        Log::info('Deleted workforce daily report document: ' . $document->id);

        return response()->json(['error' => false, 'message' => 'Document deleted']);
    }
}
