@extends('layouts.app')

@section('title', 'Report Photos - Admin View')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Report Photos</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('swr.admin.report.detail', $report->id) }}" class="btn btn-ghost-primary">Back to Report</a>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                @if($report->documents->count() > 0)
                    <div class="row g-3">
                        @foreach($report->documents as $doc)
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <div class="card-body p-0">
                                        <a href="{{ route('swr.docs.view.ext', ['document' => $doc->id, 'ext' => pathinfo($doc->file_name, PATHINFO_EXTENSION)]) }}" data-gallery="gallery" class="glightbox">
                                            <img src="{{ route('swr.docs.view.ext', ['document' => $doc->id, 'ext' => pathinfo($doc->file_name, PATHINFO_EXTENSION)]) }}" class="img-fluid rounded-top" alt="{{ $doc->original_name }}" style="height: 200px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted">{{ $doc->original_name }}</small>
                                        <br>
                                        <small class="text-muted">{{ $doc->getFileSize() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12" y2="16" /><line x1="8" y1="12" x2="16" y2="12" /></svg>
                        No photos attached to this report.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = GLightbox({
            selector: '.glightbox'
        });
    });
</script>
@endsection
    </div>
@endsection

@push('script')
    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

        });

        document.addEventListener('DOMContentLoaded', () => {
            baguetteBox.run('.wdr-gallery', {
                animation: 'fadeIn',
                captions: true
            });
        });
    </script>
@endpush
