@extends('wdr.layout.admin_template')

@section('title', 'Report Details - Admin View')

@section('main')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Report Details - {{ $report->user?->name }}</h2>
            </div>
            <div class="col-auto">
                <div class="btn-list gap-2">
                    @if($report->status === 'submitted')
                        <button class="btn btn-success approve-report" data-id="{{ $report->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" /><line x1="12" y1="12" x2="20" y2="7.5" /><line x1="12" y1="12" x2="12" y2="21" /><line x1="12" y1="12" x2="4" y2="7.5" /></svg>
                            Approve
                        </button>
                        <button class="btn btn-danger reject-report" data-id="{{ $report->id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="9" y1="9" x2="15" y2="15" /><line x1="9" y1="15" x2="15" y2="9" /></svg>
                            Reject
                        </button>
                    @endif
                    @if($report->documents->count() > 0)
                        <a href="{{ route('swr.admin.report.gallery', $report->id) }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="3" y="5" width="18" height="14" rx="2" ry="2" /><circle cx="8" cy="9" r="1" /><line x1="21" y1="13" x2="3" y2="13" /></svg>
                            Gallery ({{ $report->documents->count() }})
                        </a>
                    @endif
                    <a href="{{ route('swr.admin.report.pdf', $report->id) }}" class="btn btn-warning" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3v4m0 4v4m0 4v4" /></svg>
                            PDF
                        </a>
                    <a href="{{ route('swr.admin.report') }}" class="btn btn-ghost-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <!-- Basic Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Basic Information</h3>
                        <div class="ms-auto">
                            <span class="badge bg-{{ $report->status === 'draft' ? 'secondary' : ($report->status === 'submitted' ? 'info' : ($report->status === 'approved' ? 'success' : 'danger')) }}">
                                {{ $report->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Reporter</label>
                                    <p><strong>{{ $report->user?->name }}</strong></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Reporting Week</label>
                                    <p>{{ $report->reporting_week ? format_date($report->reporting_week) : 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Event</label>
                                    <p>{{ $report->event->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Venue</label>
                                    <p>{{ $report->venue->title ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <p>{{ $report->city ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <p>{{ $report->role ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Activities -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Weekly Activities</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $report->main_activities }}</p>
                    </div>
                </div>

                <!-- Gained Experience -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Gained Experience</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $report->experience_gained }}</p>
                    </div>
                </div>

                <!-- Innovation -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Innovation</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p>{{ $report->innovation_description }}</p>
                        </div>
                        @if($report->innovationFunctionalAreas->count())
                            <div class="mb-3">
                                <label class="form-label">Functional Areas Impacted</label>
                                <div>
                                    @foreach($report->innovationFunctionalAreas as $pivot)
                                        <span class="badge bg-info me-1 mb-1">{{ $pivot->functionalArea?->fa_code ?? $pivot->functionalArea?->fa_code ?? 'N/A' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Challenges -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Challenges</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p>{{ $report->challenges_description }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resolved</label>
                            <p>
                                <span class="badge bg-{{ $report->challenges_resolved ? 'success' : 'danger' }}">
                                    {{ $report->challenges_resolved ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                        @if($report->challengeFunctionalAreas->count())
                            <div class="mb-3">
                                <label class="form-label">Functional Areas Impacted</label>
                                <div>
                                    @foreach($report->challengeFunctionalAreas as $pivot)
                                        <span class="badge bg-warning me-1 mb-1">{{ $pivot->functionalArea?->fa_code ?? $pivot->functionalArea?->fa_code ?? 'N/A' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Value for Qatar -->
                @if($report->value_for_qatar)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Value for Qatar</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <p><span class="badge bg-primary">{{ $report->value_for_qatar_type }}</span></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <p>{{ $report->value_for_qatar_description }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- HR / Wellbeing -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">HR / Wellbeing</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Wellbeing Status</label>
                            <p>
                                <span class="text-lg">{{ $report->getWellbeingEmoji() }}</span>
                                {{ $report->wellbeing_status }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Needs Support</label>
                            <p><span class="badge bg-{{ $report->needs_support ? 'danger' : 'success' }}">{{ $report->needs_support ? 'Yes' : 'No' }}</span></p>
                        </div>
                        @if($report->support_types)
                            <div class="mb-3">
                                <label class="form-label">Support Types</label>
                                <div>
                                    @foreach($report->support_types as $type)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $type }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($report->additional_comment)
                            <div class="mb-3">
                                <label class="form-label">Additional Comments</label>
                                <p>{{ $report->additional_comment }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Created: {{ format_date($report->created_at, 'Y-m-d H:i') }}</small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Updated: {{ format_date($report->updated_at, 'Y-m-d H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveBtn = document.querySelector('.approve-report');
    const rejectBtn = document.querySelector('.reject-report');

    if (approveBtn) {
        approveBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this report?')) {
                fetch('{{ route('swr.admin.report.approve', $report->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error approving report');
                    }
                })
                .catch(e => alert('Error: ' + e));
            }
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reject this report?')) {
                fetch('{{ route('swr.admin.report.reject', $report->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error rejecting report');
                    }
                })
                .catch(e => alert('Error: ' + e));
            }
        });
    }
});
</script>
@endsection
