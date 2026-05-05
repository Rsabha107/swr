@extends('wdr.layout.admin_template')
@section('main')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('wdr.admin.report') }}">Daily Reports</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $report->reference_number }}</a></li>
        </ol>
    </nav>
    <div class="mb-9">
        {{-- <h2 class="mb-5">Gallery</h2> --}}
        <div class="d-flex flex-wrap gap-3 justify-content-between mb-4">
            <div>
                <a href="{{ route('reports.images.export', $report) }}" class="btn btn-outline-primary">
                    <span class="fa-solid fa-file-export fs-9 me-2 text-white"></span>Export</a>
            </div>
            {{-- <div class="search-box">
                <form class="position-relative">
                    <input class="form-control search-input search" type="search" placeholder="Search by name"
                        aria-label="Search" />
                    <span class="fas fa-search search-box-icon"></span>

                </form>
            </div> --}}
        </div>

        {{-- <div class="pswp-gallery" id="wdr-gallery">
            @foreach ($report->photos as $doc)
                <a href="{{ route('wdr.docs.view', $doc->id) }}" data-pswp-width="100" data-pswp-height="200">
                    <img src="{{ route('wdr.docs.view', $doc->id) }}" class="img-fluid" alt="" />
                </a>
            @endforeach
        </div> --}}

        <div class="row g-3" id="image_gallery" data-sl-isotope='{"layoutMode":"packery"}'>
            @foreach ($report->photos as $doc)
                <a class="photography col-sm-6 col-md-4 col-xl-3 isotope-item"
                    href="{{ route('wdr.docs.view.ext', ['document' => $doc->id, 'ext' => $doc->extension]) }}"
                    data-gallery="gallery-grid">
                    <div class="hoverbox img-zoom-hover rounded-2"><img class="img-fluid"
                            src="{{ route('wdr.docs.view.ext', ['document' => $doc->id, 'ext' => $doc->extension]) }}"
                            alt="" />
                        {{-- <div class="hoverbox-content flex-center flex-column">
                        <h4 class="text-white">Beach Sunset</h4>
                        <p class="mb-0 text-secondary-lighter text-capitalize">photography</p>
                    </div> --}}
                    </div>
                </a>
            @endforeach
        </div>
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
