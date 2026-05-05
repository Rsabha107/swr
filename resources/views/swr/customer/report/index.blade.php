@extends('layouts.app')

@section('title', 'My Secondment Weekly Reports')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">My Secondment Weekly Reports</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('swr.report.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
                    Create New Report
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <table id="swrReportsTable" class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th data-field="reporting_week">Reporting Week</th>
                                    <th data-field="venue">Venue</th>
                                    <th data-field="event">Event</th>
                                    <th data-field="wellbeing">Wellbeing</th>
                                    <th data-field="status">Status</th>
                                    <th data-field="photos">Photos</th>
                                    <th data-field="created_at">Submitted</th>
                                    <th data-field="actions" data-sortable="false">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = new DataTable('#swrReportsTable', {
        ajax: '{{ route('swr.report.list') }}',
        columns: [
            { data: 'reporting_week' },
            { data: 'venue' },
            { data: 'event' },
            { data: 'wellbeing', orderable: false },
            { data: 'status' },
            { data: 'photos' },
            { data: 'created_at' },
            { data: 'actions', orderable: false },
        ],
        pageLength: 10,
        order: [[6, 'desc']],
        language: {
            emptyTable: 'No reports found'
        }
    });

    // Delete handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-report')) {
            const id = e.target.dataset.id;
            if (confirm('Are you sure you want to delete this report?')) {
                fetch('{{ route('swr.report.destroy', '') }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        table.ajax.reload();
                        showNotification('Report deleted successfully!', 'success');
                    }
                })
                .catch(e => console.error('Error:', e));
            }
        }
    });
});
</script>
@endsection
