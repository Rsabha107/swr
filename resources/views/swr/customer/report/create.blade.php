@extends('swr.customer.layout.template')

@section('title', 'Create Secondment Weekly Report')

@section('main')

<link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    /* Custom checkbox styling */
    .checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 8px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
    }

    .checkbox-group input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkbox-group label {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        border: 1px solid #d0d0d0;
        border-radius: 20px;
        background-color: #f8f8f8;
        cursor: pointer;
        font-size: 0.9rem;
        margin: 0;
        transition: all 0.2s ease;
        user-select: none;
        flex: 1;
    }

    .checkbox-group input[type="checkbox"]:hover + label {
        border-color: #0066cc;
        background-color: #f0f7ff;
    }

    .checkbox-group input[type="checkbox"]:checked + label {
        background-color: #0066cc;
        color: white;
        border-color: #0052a3;
    }

    .checkbox-group input[type="checkbox"]:checked + label::before {
        content: '✓';
        display: inline-block;
        margin-right: 6px;
        font-weight: bold;
    }

    /* Radio button styling */
    .radio-group {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .radio-item {
        display: flex;
        align-items: center;
    }

    .radio-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .radio-item label {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border: 1px solid #d0d0d0;
        border-radius: 20px;
        background-color: #f8f8f8;
        cursor: pointer;
        font-size: 0.9rem;
        margin: 0;
        transition: all 0.2s ease;
        user-select: none;
    }

    .radio-item input[type="radio"]:hover + label {
        border-color: #0066cc;
        background-color: #f0f7ff;
    }

    .radio-item input[type="radio"]:checked + label {
        background-color: #0066cc;
        color: white;
        border-color: #0052a3;
    }


</style>

<main id="mainContent" class="main-content" style="display: block">
    <div id="createPage" class="page-content" style="display: block">
        <div class="page-header">
            <h2>Create Secondment Weekly Report</h2>
            <a href="{{ route('swr.report') }}"><button class="btn btn-subtle-primary px-3 px-sm-5 me-2"><span class="fa-solid fa-arrow-left me-sm-2"></span><span class="d-none d-sm-inline">Back</span></button></a>
        </div>

        <form id="swrForm" class="report-form needs-validation" action="{{ route('swr.report.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Basic Information -->
            <div class="form-section">
                <h3>Basic Information</h3>
                <div class="form-row">

                    <x-formy.form_date_input class="col-sm-12 col-md-12  mb-3" inputType="text" floating='0'
                            inputValue="" classLabel='mb-1' name="reporting_week" elementId="reporting_week" label="Reporting Day"
                            required="required" disabled="" inputValue="{{ old('reporting_week') }}"/>

                    <x-formy.floating-select class="col-sm-12 col-md-12  mb-3" id="venue_id" floating='0'
                            name="venue_id" classLabel='mb-1' :options="$venues" label="Venue" required="required" multiple='0'
                            selectedValue="title" itemIdForeach="id" itemTitleForeach="title" inputValue="{{ $defaultVenueId ?? old('venue_id') }}" />
                    {{-- <x-formy.form_input class="col-sm-12 col-md-12 mb-3" inputType="text" floating='0' inputValue="{{ old('city') }}"
                            classLabel='mb-1' name="city" elementId="city" inputAttributes=""
                            label="City" inputPlaceholder="City" required="required" disabled=''/> --}}
                </div>

                <div class="form-row">
                    {{-- <x-formy.floating-select class="col-sm-12 col-md-6 mb-3" floating='1' elementId="venue_id" name="venue_id" 
                    label="Venue" required="required" selectedValue="{{ old('venue_id') }}">
                        @foreach($venues as $venue)
                            <option value="{{ $venue->id }}">{{ $venue->title }} ({{ $venue->city ?? 'N/A' }})</option>
                        @endforeach
                    </x-formy.floating-select> --}}

                    {{-- <x-formy.form_input class="col-sm-12 col-md-6 mb-3" inputType="text" floating='1' elementId="name" 
                    name="name" label="Name (Auto-filled)" inputValue="{{ $user->name }}" disabled="disabled"/> --}}
                
                    <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="text" floating='0' inputValue="{{ $user->name }}"
                            classLabel='mb-1' name="name" elementId="name"
                            label="Name" inputPlaceholder="name"
                            inputAttributes="min=0" required="required" disabled='' />

                    <x-formy.form_input class="col-sm-12 col-md-12 mb-3" inputType="text" floating='0' inputValue="{{ old('role', $user->phone) }}"
                            classLabel='mb-1' name="role" elementId="role" inputAttributes=""
                            label="Role" inputPlaceholder="Enter your role" required="required" disabled=''/>
                </div>
            </div>

            <!-- Weekly Activities -->
            <div class="form-section">
                <h3>Weekly Activities</h3>
                <div class="mb-3">
                    <label class="mb-1" for="main_activities">What were your main activities and responsibilities this week? <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="main_activities" name="main_activities" rows="4"
                        placeholder="Describe your main activities..." required>{{ old('main_activities') }}</textarea>
                </div>
                {{-- <small class="text-muted">Free text (mandatory)</small> --}}
            </div>

            <!-- Gained Experience -->
            <div class="form-section">
                <h3>Gained Experience</h3>
                <div class="mb-3">
                    <label class="mb-1" for="experience_gained">What experience did you gain this week? <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="experience_gained" name="experience_gained" rows="4"
                        placeholder="Describe your learning..." required>{{ old('experience_gained') }}</textarea>
                </div>
                {{-- <small class="text-muted">Free text (mandatory)</small> --}}
            </div>

            <!-- Innovation Section -->
            <div class="form-section">
                <h3>Innovation</h3>
                <div class="mb-3">
                    <label class="mb-1" for="innovation_description">Innovation observed this week <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="innovation_description" name="innovation_description" rows="3"
                        placeholder="Describe any innovations..." required>{{ old('innovation_description') }}</textarea>
                </div>
                {{-- <small class="text-muted">Short description (Free text – mandatory)</small> --}}

                <div class="form-group">
                    <label class="mb-1">Functional Area(s) impacted (Multi-select)</label>
                    <div class="checkbox-grid">
                        @foreach($functionalAreas->sortBy(function($area) { return strtolower($area->fa_code) === 'oth' ? 1 : 0; }) as $key => $area)
                            <div class="checkbox-group">
                                <input class="innovation-area-check" type="checkbox" id="innovation_area_{{ $area->id }}" name="innovation_functional_areas[]" value="{{ $area->id }}" {{ in_array($area->id, old('innovation_functional_areas', [])) ? 'checked' : '' }} @if(strtolower($area->fa_code) === 'oth') data-other="true" @endif>
                                <label for="innovation_area_{{ $area->id }}">{{ strtoupper($area->fa_code) === 'OTH' ? 'OTHER' : $area->fa_code }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="innovation_other_container" style="display:none;">
                    <div class="mb-3">
                        <label class="mb-1" for="innovation_other_area">Please specify other area</label>
                        <input type="text" class="form-control" id="innovation_other_area" name="innovation_other_area" value="{{ old('innovation_other_area') }}">
                    </div>
                </div>

            <!-- Challenges Section -->
            <div class="form-section">
                <h3>Challenges</h3>
                <div class="mb-3">
                    <label class="mb-1" for="challenges_description">Challenges faced this week <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="challenges_description" name="challenges_description" rows="3"
                        placeholder="Describe any challenges..." required>{{ old('challenges_description') }}</textarea>
                </div>
                {{-- <small class="text-muted">Short description (Free text – mandatory)</small> --}}

                <div class="form-group">
                    <label class="mb-1">Was it resolved? <span class="text-danger">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="challenges_resolved" id="challenges_resolved_yes" value="yes" {{ old('challenges_resolved') == 'yes' ? 'checked' : '' }} required>
                            <label for="challenges_resolved_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="challenges_resolved" id="challenges_resolved_no" value="no" {{ old('challenges_resolved') == 'no' ? 'checked' : '' }} required>
                            <label for="challenges_resolved_no">No</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="mb-1">Functional Area(s) impacted (Multi-select)</label>
                    <div class="checkbox-grid">
                        @foreach($functionalAreas->sortBy(function($area) { return strtolower($area->fa_code) === 'oth' ? 1 : 0; }) as $key => $area)
                            <div class="checkbox-group">
                                <input class="challenges-area-check" type="checkbox" id="challenges_area_{{ $area->id }}" name="challenges_functional_areas[]" value="{{ $area->id }}" {{ in_array($area->id, old('challenges_functional_areas', [])) ? 'checked' : '' }} @if(strtolower($area->fa_code) === 'oth') data-other="true" @endif>
                                <label for="challenges_area_{{ $area->id }}">{{ strtoupper($area->fa_code) === 'OTH' ? 'OTHER' : $area->fa_code }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="challenges_other_container" style="display:none;">
                    <div class="mb-3">
                        <label class="mb-1" for="challenges_other_area">Please specify other area</label>
                        <input type="text" class="form-control" id="challenges_other_area" name="challenges_other_area" value="{{ old('challenges_other_area') }}">
                    </div>
                </div>
            </div>

                {{-- <div id="challenges_other_area" style="display:none;">
                    <div class="mb-3">
                        <label class="mb-1" for="challenges_other_area">Please specify other area</label>
                        <input type="text" class="form-control" id="challenges_other_area" name="challenges_other_area" value="{{ old('challenges_other_area') }}">
                    </div>
                </div> --}}
            </div>

            <!-- Attach Photos -->
            <div class="form-section">
                <h3>Attach Photos</h3>
                <div class="form-group">
                    <label for="photoUpload">Photo Uploads (Max 10 MB)</label>
                    <div class="photo-upload-area" id="photoUploadArea">
                        <input type="file" id="photoUpload" name="photos[]" multiple accept="image/*" />
                        <div class="upload-placeholder">
                            <span class="material-icons">cloud_upload</span>
                            <p>Click to upload photos or drag and drop</p>
                            <span>Support for multiple photos</span>
                        </div>
                    </div>
                    <div id="photoPreview" class="photo-preview"></div>
                </div>
            </div>

            <!-- Value for Qatar -->
            <div class="form-section">
                <h3>Value for Qatar</h3>
                <div class="form-group">
                    <label class="mb-1">Did you see anything this week that should be applied for future events in Qatar? <span class="text-danger">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="value_for_qatar" id="value_for_qatar_yes" value="yes" {{ old('value_for_qatar') == 'yes' ? 'checked' : '' }} required>
                            <label for="value_for_qatar_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="value_for_qatar" id="value_for_qatar_no" value="no" {{ old('value_for_qatar') == 'no' ? 'checked' : '' }} required>
                            <label for="value_for_qatar_no">No</label>
                        </div>
                    </div>
                </div>

                <div id="value_qatar_conditional" style="display:none;">
                    <div class="form-group">
                        <label class="mb-1">Type (Single select)</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input class="form-check-input" type="radio" name="value_for_qatar_type" id="value_must_have" value="Must Have" {{ old('value_for_qatar_type') == 'Must Have' ? 'checked' : '' }}>
                                <label for="value_must_have">Must Have</label>
                            </div>
                            <div class="radio-item">
                                <input class="form-check-input" type="radio" name="value_for_qatar_type" id="value_good_have" value="Good to Have" {{ old('value_for_qatar_type') == 'Good to Have' ? 'checked' : '' }}>
                                <label for="value_good_have">Good to Have</label>
                            </div>
                            <div class="radio-item">
                                <input class="form-check-input" type="radio" name="value_for_qatar_type" id="value_further" value="Requires further assessment" {{ old('value_for_qatar_type') == 'Requires further assessment' ? 'checked' : '' }}>
                                <label for="value_further">Further assessment</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="mb-1" for="value_for_qatar_description">Short description</label>
                        <textarea class="form-control" id="value_for_qatar_description" name="value_for_qatar_description" rows="3"
                            placeholder="Provide details...">{{ old('value_for_qatar_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- HR / Wellbeing -->
            <div class="form-section">
                <h3>HR / Wellbeing</h3>
                <div class="form-group">
                    <label class="mb-1">How is your wellbeing this week? <span class="text-danger">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="wellbeing_status" id="wellbeing_good" value="Good" {{ old('wellbeing_status') == 'Good' ? 'checked' : '' }} required>
                            <label for="wellbeing_good">😊 Good</label>
                        </div>
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="wellbeing_status" id="wellbeing_moderate" value="Moderate" {{ old('wellbeing_status') == 'Moderate' ? 'checked' : '' }} required>
                            <label for="wellbeing_moderate">😐 Moderate</label>
                        </div>
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="wellbeing_status" id="wellbeing_challenging" value="Challenging" {{ old('wellbeing_status') == 'Challenging' ? 'checked' : '' }} required>
                            <label for="wellbeing_challenging">😟 Challenging</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="mb-1">Do you need support? <span class="text-danger">*</span></label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="needs_support" id="needs_support_yes" value="yes" {{ old('needs_support') == 'yes' ? 'checked' : '' }} required>
                            <label for="needs_support_yes">Yes</label>
                        </div>
                        <div class="radio-item">
                            <input class="form-check-input" type="radio" name="needs_support" id="needs_support_no" value="no" {{ old('needs_support') == 'no' ? 'checked' : '' }} required>
                            <label for="needs_support_no">No</label>
                        </div>
                    </div>
                </div>

                <div id="support_types_container" style="display:none;">
                    <div class="form-group">
                        <label class="mb-1">Support Types (Multi-select)</label>
                        <div class="checkbox-grid">
                            @foreach($supportTypes as $key => $type)
                                <div class="checkbox-group">
                                    <input class="support-type-check" type="checkbox" id="support_type_{{ $loop->index }}" name="support_types[]" value="{{ $type }}" {{ in_array($type, old('support_types', [])) ? 'checked' : '' }}>
                                    <label for="support_type_{{ $loop->index }}">{{ $type }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="support_other_container" style="display:none;">
                        <div class="mb-3">
                            <label class="mb-1" for="support_other_description">Please specify other support needed</label>
                            <input type="text" class="form-control" id="support_other_description" name="support_other_description" value="{{ old('support_other_description') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="mb-1" for="additional_comment">Additional comment (optional)</label>
                    <textarea class="form-control" id="additional_comment" name="additional_comment" rows="3"
                        placeholder="Any additional comments...">{{ old('additional_comment') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="secondary-btn" id="cancelReport">
                    Cancel
                </button>
                <button type="submit" class="primary-btn">
                    <span class="fa-solid fa-check me-2"></span>
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display Laravel validation errors as toastr
    @if ($errors->any())
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "8000",
                "extendedTimeOut": "3000"
            };
            
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}", "Validation Error");
            @endforeach
        }
    @endif

    // Cancel button
    document.getElementById('cancelReport').addEventListener('click', function() {
        window.location.href = '{{ route('swr.report') }}';
    });

    // Show/hide innovation other field
    document.querySelectorAll('.innovation-area-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const hasOther = document.querySelector('.innovation-area-check[data-other="true"]:checked');
            document.getElementById('innovation_other_container').style.display = hasOther ? 'block' : 'none';
        });
    });

    // Show/hide challenges other field
    document.querySelectorAll('.challenges-area-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const hasOther = document.querySelector('.challenges-area-check[data-other="true"]:checked');
            document.getElementById('challenges_other_container').style.display = hasOther ? 'block' : 'none';
        });
    });

    // Show/hide value for qatar conditional fields
    document.querySelectorAll('input[name="value_for_qatar"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('value_qatar_conditional').style.display = this.value === 'yes' ? 'block' : 'none';
        });
    });

    // Show/hide support types
    document.querySelectorAll('input[name="needs_support"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('support_types_container').style.display = this.value === 'yes' ? 'block' : 'none';
        });
    });

    // Show/hide support other field
    document.querySelectorAll('.support-type-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const hasOther = document.querySelector('.support-type-check[value*="Other"]:checked');
            document.getElementById('support_other_container').style.display = hasOther ? 'block' : 'none';
        });
    });

    // Trigger initial display state
    const valueQatarYes = document.getElementById('value_for_qatar_yes');
    if (valueQatarYes && valueQatarYes.checked) {
        document.getElementById('value_qatar_conditional').style.display = 'block';
    }

    const needsSupportYes = document.getElementById('needs_support_yes');
    if (needsSupportYes && needsSupportYes.checked) {
        document.getElementById('support_types_container').style.display = 'block';
    }

    // Trigger initial display for innovation other field
    const innovationOther = document.querySelector('.innovation-area-check[data-other="true"]:checked');
    if (innovationOther) {
        document.getElementById('innovation_other_container').style.display = 'block';
    }

    // Trigger initial display for challenges other field
    const challengesOther = document.querySelector('.challenges-area-check[data-other="true"]:checked');
    if (challengesOther) {
        document.getElementById('challenges_other_container').style.display = 'block';
    }

    // Prevent double submission
    const form = document.getElementById('swrForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnContent = submitBtn.innerHTML;

    form.addEventListener('submit', function(e) {
        // Prevent multiple submissions
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }

        // Check HTML5 validation
        if (!form.checkValidity()) {
            e.preventDefault();
            
            // Find all invalid fields
            const invalidFields = form.querySelectorAll(':invalid');
            const fieldNames = [];
            
            invalidFields.forEach(field => {
                // Get the label text for the field
                let label = '';
                
                // Check if field is a radio button or checkbox in a group
                const isRadio = field.type === 'radio';
                const isCheckbox = field.type === 'checkbox';
                
                if (isRadio || isCheckbox) {
                    // For radio/checkbox, get the parent form-group's main label (the question)
                    const parentGroup = field.closest('.form-group, .mb-3');
                    if (parentGroup) {
                        // Get all labels in the parent group
                        const labels = parentGroup.querySelectorAll('label');
                        // Find the main label (not inside radio-item or checkbox-group)
                        for (let lbl of labels) {
                            if (!lbl.closest('.radio-item') && !lbl.closest('.checkbox-group')) {
                                label = lbl.textContent.replace('*', '').trim();
                                break;
                            }
                        }
                    }
                } else {
                    // For other fields, get the direct label
                    if (field.id) {
                        const labelElement = form.querySelector(`label[for="${field.id}"]`);
                        if (labelElement) {
                            label = labelElement.textContent.replace('*', '').trim();
                        }
                    }
                    
                    // If no label found, try to get from parent structure
                    if (!label) {
                        const parentLabel = field.closest('.mb-3, .form-group')?.querySelector('label');
                        if (parentLabel) {
                            label = parentLabel.textContent.replace('*', '').trim();
                        }
                    }
                }
                
                // Fallback to field name
                if (!label) {
                    label = field.name || field.id || 'Field';
                }
                
                if (label && !fieldNames.includes(label)) {
                    fieldNames.push(label);
                }
            });
            
            // Show toastr with required fields
            if (fieldNames.length > 0 && typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000",
                    "extendedTimeOut": "2000"
                };
                
                const message = `Please fill in the following required fields:<br><br>• ${fieldNames.join('<br>• ')}`;
                toastr.error(message, 'Validation Error');
            }
            
            return false;
        }

        // Disable button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
        
        // Log form submission
        const formData = new FormData(this);
        const innovationAreas = formData.getAll('innovation_functional_areas[]');
        const challengeAreas = formData.getAll('challenges_functional_areas[]');
        console.log('Form Submission - Innovation Areas:', innovationAreas);
        console.log('Form Submission - Challenge Areas:', challengeAreas);
    });
});

// Photo upload handling
const input = document.getElementById('photoUpload');
const preview = document.getElementById('photoPreview');
let selectedFiles = [];

input.addEventListener('change', () => {
    const newFiles = Array.from(input.files);
    newFiles.forEach(f => {
        const exists = selectedFiles.some(x =>
            x.name === f.name &&
            x.size === f.size &&
            x.lastModified === f.lastModified
        );
        if (!exists) selectedFiles.push(f);
    });

    syncInputFiles();
    renderPreviews();
});

function syncInputFiles() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

function renderPreviews() {
    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'preview-img';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'photo-remove';
            btn.innerHTML = '&times;';
            btn.onclick = () => removeFile(index);

            wrapper.appendChild(img);
            wrapper.appendChild(btn);
            preview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    syncInputFiles();
    renderPreviews();
}
</script>

@endsection
