@extends('layouts.app')

@section('title', 'Edit Secondment Weekly Report')

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Edit Report - {{ $report->user?->name }}</h2>
            </div>
            <div class="col-auto">
                <a href="{{ route('swr.report') }}" class="btn btn-ghost-primary">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('swr.report.update', $report->id) }}" method="POST" enctype="multipart/form-data" id="swrForm">
                    @csrf
                    @method('PUT')

                    <!-- Section 1: Basic Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">📋 Basic Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Reporting Week</label>
                                        <input type="date" name="reporting_week" class="form-control @error('reporting_week') is-invalid @enderror" value="{{ $report->reporting_week }}" required>
                                        @error('reporting_week') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Event</label>
                                        <select name="event_id" class="form-control @error('event_id') is-invalid @enderror" id="eventSelect" required>
                                            <option value="">Select Event</option>
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" {{ $report->event_id == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('event_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Venue</label>
                                        <select name="venue_id" class="form-control @error('venue_id') is-invalid @enderror" id="venueSelect" required>
                                            <option value="">Select Venue</option>
                                            @foreach($venues as $venue)
                                                <option value="{{ $venue->id }}" {{ $report->venue_id == $venue->id ? 'selected' : '' }}>{{ $venue->title }}</option>
                                            @endforeach
                                        </select>
                                        @error('venue_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Name</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $report->name }}" required>
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Role</label>
                                        <input type="text" name="role" class="form-control @error('role') is-invalid @enderror" value="{{ $report->role }}" required>
                                        @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">City</label>
                                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ $report->city }}" required>
                                        @error('city') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Weekly Activities -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">🎯 Weekly Activities</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Main Activities</label>
                                <textarea name="main_activities" class="form-control @error('main_activities') is-invalid @enderror" rows="4" required>{{ $report->main_activities }}</textarea>
                                @error('main_activities') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Gained Experience -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">📚 Gained Experience</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Experience Gained</label>
                                <textarea name="experience_gained" class="form-control @error('experience_gained') is-invalid @enderror" rows="4" required>{{ $report->experience_gained }}</textarea>
                                @error('experience_gained') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Innovation -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">💡 Innovation</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Innovation Description</label>
                                <textarea name="innovation_description" class="form-control @error('innovation_description') is-invalid @enderror" rows="4" required>{{ $report->innovation_description }}</textarea>
                                @error('innovation_description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Functional Areas Impacted</label>
                                <div class="row">
                                    @foreach($functionalAreas as $area)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="innovation_functional_areas[]" value="{{ $area->id }}" class="form-check-input" id="innov_{{ $area->id }}" {{ $report->innovationFunctionalAreas->contains('functional_area_id', $area->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="innov_{{ $area->id }}">{{ $area->fa_code ?? $area->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Challenges -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">⚠️ Challenges</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Challenge Description</label>
                                <textarea name="challenges_description" class="form-control @error('challenges_description') is-invalid @enderror" rows="4" required>{{ $report->challenges_description }}</textarea>
                                @error('challenges_description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Was this challenge resolved?</label>
                                <div class="form-check">
                                    <input type="radio" name="challenges_resolved" value="1" class="form-check-input" id="resolved_yes" {{ $report->challenges_resolved ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="resolved_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="challenges_resolved" value="0" class="form-check-input" id="resolved_no" {{ !$report->challenges_resolved ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="resolved_no">No</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Functional Areas Impacted</label>
                                <div class="row">
                                    @foreach($functionalAreas as $area)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="challenges_functional_areas[]" value="{{ $area->id }}" class="form-check-input" id="chal_{{ $area->id }}" {{ $report->challengeFunctionalAreas->contains('functional_area_id', $area->id) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="chal_{{ $area->id }}">{{ $area->fa_code ?? $area->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Value for Qatar -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">🇶🇦 Value for Qatar</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Does this report add value for Qatar?</label>
                                <div class="form-check">
                                    <input type="radio" name="value_for_qatar" value="1" class="form-check-input" id="qatar_yes" {{ $report->value_for_qatar ? 'checked' : '' }} onchange="toggleQatarFields()">
                                    <label class="form-check-label" for="qatar_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="value_for_qatar" value="0" class="form-check-input" id="qatar_no" {{ !$report->value_for_qatar ? 'checked' : '' }} onchange="toggleQatarFields()">
                                    <label class="form-check-label" for="qatar_no">No</label>
                                </div>
                            </div>
                            <div id="qatarFields" style="display: {{ $report->value_for_qatar ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label">Value Type</label>
                                    <input type="text" name="value_for_qatar_type" class="form-control @error('value_for_qatar_type') is-invalid @enderror" value="{{ $report->value_for_qatar_type }}">
                                    @error('value_for_qatar_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="value_for_qatar_description" class="form-control @error('value_for_qatar_description') is-invalid @enderror" rows="3">{{ $report->value_for_qatar_description }}</textarea>
                                    @error('value_for_qatar_description') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 7: HR/Wellbeing -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">💪 HR / Wellbeing</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">How are you feeling?</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" name="wellbeing_status" value="Good" id="wellbeing_good" class="btn-check" {{ $report->wellbeing_status === 'Good' ? 'checked' : '' }} required>
                                    <label for="wellbeing_good" class="btn btn-outline-success">😊 Good</label>
                                    
                                    <input type="radio" name="wellbeing_status" value="Moderate" id="wellbeing_moderate" class="btn-check" {{ $report->wellbeing_status === 'Moderate' ? 'checked' : '' }}>
                                    <label for="wellbeing_moderate" class="btn btn-outline-warning">😐 Moderate</label>
                                    
                                    <input type="radio" name="wellbeing_status" value="Challenging" id="wellbeing_challenging" class="btn-check" {{ $report->wellbeing_status === 'Challenging' ? 'checked' : '' }}>
                                    <label for="wellbeing_challenging" class="btn btn-outline-danger">😟 Challenging</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Do you need support?</label>
                                <div class="form-check">
                                    <input type="radio" name="needs_support" value="1" class="form-check-input" id="support_yes" {{ $report->needs_support ? 'checked' : '' }} onchange="toggleSupportTypes()">
                                    <label class="form-check-label" for="support_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="needs_support" value="0" class="form-check-input" id="support_no" {{ !$report->needs_support ? 'checked' : '' }} onchange="toggleSupportTypes()">
                                    <label class="form-check-label" for="support_no">No</label>
                                </div>
                            </div>
                            <div id="supportTypes" style="display: {{ $report->needs_support ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label">Support Types Needed</label>
                                    <div class="row">
                                        @php
                                            $supportTypes = ['Mental Health', 'Financial Advice', 'Relocation Support', 'Legal Support', 'Visa Support', 'Health Insurance', 'Family Support', 'Work-Life Balance', 'Career Development', 'Language Training', 'Cultural Adaptation', 'Housing', 'Transportation', 'Medical', 'Other'];
                                        @endphp
                                        @foreach($supportTypes as $type)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input type="checkbox" name="support_types[]" value="{{ $type }}" class="form-check-input" id="sup_{{ $loop->index }}" {{ in_array($type, $report->support_types ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="sup_{{ $loop->index }}">{{ $type }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 8: Additional Comments -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h3 class="card-title mb-0">📝 Additional Comments</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Any additional comments?</label>
                                <textarea name="additional_comment" class="form-control @error('additional_comment') is-invalid @enderror" rows="3">{{ $report->additional_comment }}</textarea>
                                @error('additional_comment') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto">
                                    <button type="submit" name="status" value="draft" class="btn btn-secondary">Save as Draft</button>
                                    <button type="submit" name="status" value="submitted" class="btn btn-primary">Submit Report</button>
                                    <a href="{{ route('swr.report') }}" class="btn btn-ghost-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleQatarFields() {
    const qatarFields = document.getElementById('qatarFields');
    const qatarYes = document.getElementById('qatar_yes');
    qatarFields.style.display = qatarYes.checked ? 'block' : 'none';
}

function toggleSupportTypes() {
    const supportTypes = document.getElementById('supportTypes');
    const supportYes = document.getElementById('support_yes');
    supportTypes.style.display = supportYes.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('eventSelect');
    const venueSelect = document.getElementById('venueSelect');

    eventSelect.addEventListener('change', function() {
        const eventId = this.value;
        if (eventId) {
            fetch('{{ route('swr.report.byEvent', '') }}/' + eventId)
                .then(r => r.json())
                .then(data => {
                    venueSelect.innerHTML = '<option value="">Select Venue</option>';
                    data.venues.forEach(venue => {
                        const option = document.createElement('option');
                        option.value = venue.id;
                        option.text = venue.title;
                        venueSelect.appendChild(option);
                    });
                });
        }
    });
});
</script>
@endsection
