@extends('wdr.customer.layout.template')
@section('main')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('swr.report') }}">Weekly Reports</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $report->reference_number }}</a></li>
        </ol>
    </nav>
    <div class="mb-9">
        <div class="d-flex flex-wrap gap-3 justify-content-between mb-4">
            <div>
                <a href="{{ route('reports.images.export', $report) }}" class="btn btn-outline-primary">
                    <span class="fa-solid fa-file-export fs-9 me-2 text-white"></span>Export</a>
            </div>
        </div>

        <div class="row g-3" id="image_gallery" data-sl-isotope='{"layoutMode":"packery"}'>
            @foreach ($report->documents as $doc)
                <a class="photography col-sm-6 col-md-4 col-xl-3 isotope-item"
                    href="{{ route('swr.docs.view.ext', ['document' => $doc->id, 'ext' => pathinfo($doc->file_name, PATHINFO_EXTENSION)]) }}"
                    data-gallery="gallery-grid">
                    <div class="hoverbox img-zoom-hover rounded-2">
                        <img class="img-fluid"
                            src="{{ route('swr.docs.view.ext', ['document' => $doc->id, 'ext' => pathinfo($doc->file_name, PATHINFO_EXTENSION)]) }}"
                            alt="{{ $doc->original_name }}" />
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });
    </script>
@endpush
