@extends('layouts.app')

@section('title', 'All Secondment Weekly Reports')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Secondment Weekly Reports</h2>
            </div>
            <div class="col-auto">
                <form class="d-inline-flex gap-2 me-2" style="display: none;">
                    <select class="form-control form-control-sm" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select class="form-control form-control-sm" id="eventFilter">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="#" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                    Export
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
                                    <th data-field="name">Reporter</th>
                                    <th data-field="venue">Venue</th>
                                    <th data-field="event">Event</th>
                                    <th data-field="wellbeing">Wellbeing</th>
                                    <th data-field="status">Status</th>
                                    <th data-field="photos">Photos</th>
                                    <th data-field="submitted_at">Submitted</th>
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
        ajax: {
            url: '{{ route('swr.admin.report.list') }}',
            data: function(d) {
                d.status = document.getElementById('statusFilter').value;
                d.event_id = document.getElementById('eventFilter').value;
                return d;
            }
        },
        columns: [
            { data: 'reporting_week' },
            { data: 'name' },
            { data: 'venue' },
            { data: 'event' },
            { data: 'wellbeing', orderable: false },
            { data: 'status' },
            { data: 'photos' },
            { data: 'submitted_at' },
            { data: 'actions', orderable: false },
        ],
        pageLength: 10,
        order: [[7, 'desc']],
        language: {
            emptyTable: 'No reports found'
        }
    });

    // Filter handlers
    document.getElementById('statusFilter').addEventListener('change', function() {
        table.ajax.reload();
    });
    
    document.getElementById('eventFilter').addEventListener('change', function() {
        table.ajax.reload();
    });

    // Approve handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('approve-report')) {
            const id = e.target.dataset.id;
            if (confirm('Approve this report?')) {
                fetch('{{ route('swr.admin.report.approve', '') }}/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        table.ajax.reload();
                        showNotification('Report approved!', 'success');
                    }
                })
                .catch(e => console.error('Error:', e));
            }
        }
    });

    // Reject handler
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('reject-report')) {
            const id = e.target.dataset.id;
            if (confirm('Reject this report?')) {
                fetch('{{ route('swr.admin.report.reject', '') }}/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        table.ajax.reload();
                        showNotification('Report rejected!', 'success');
                    }
                })
                .catch(e => console.error('Error:', e));
            }
        }
    });
});
</script>
@endsection
