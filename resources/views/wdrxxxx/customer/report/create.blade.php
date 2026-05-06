@extends('wdr.customer.layout.template')
@section('main')

    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->


    <main id="mainContent" class="main-content" style="display: block">

        <div id="createPage" class="page-content" style="display: block">
            <div class="page-header">
                <h2>Create New Report</h2>
                <a href="{{ route('wdr.report') }}"><button class="btn btn-subtle-primary px-3 px-sm-5 me-2"><span class="fa-solid fa-arrow-left me-sm-2"></span><span class="d-none d-sm-inline">Back</span></button></a>

            </div>
            @if (session('message'))
                <div class="alert">{{ session('message') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="spinner-form" class="report-form needs-validation" action="{{ route('wdr.report.store') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <!-- General Information -->
                <div class="form-section">
                    <h3>General Information</h3>
                    <div class="form-row">


                        <x-formy.form_date_input class="col-sm-12 col-md-12  mb-3" inputType="text" floating='0'
                            classLabel='mb-1' inputValue="{{ old('report_date') }}" name="report_date" elementId="edit_report_date" label="Report Date"
                            required="required" disabled=""/>
                        <x-formy.floating-select class="col-sm-12 col-md-12  mb-3" id="venue_id" floating='0'
                            classLabel='mb-1' name="venue_id" :options="$venues" label="Venue" required="required" multiple='0'
                            selectedValue="title" itemIdForeach="id" itemTitleForeach="title" inputValue="{{ old('venue_id') }}" />
                    </div>

                    <x-formy.floating-select class="col-sm-12 col-md-12  mb-3" id="day_type_id" name="day_type_id"
                        floating='0' classLabel='mb-1' :options="$day_types" label="Day Type" required="required" multiple='0'
                        selectedValue="title" itemIdForeach="id" itemTitleForeach="title" />

                </div>

                <!-- Volunteer Attendance -->
                <div class="form-section">
                    <h3>Volunteer Attendance</h3>
                    <div class="form-row">
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0' inputValue="{{ old('demand_of_day') }}"
                            classLabel='mb-1' name="demand_of_day" elementId="demand_of_day"
                            label="Demand of the Day" inputPlaceholder="Number of volunteers planned/scheduled"
                            inputAttributes="min=0" required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0' inputValue="{{ old('attended') }}"
                            classLabel='mb-1' name="attended" elementId="attended" label="Attended"
                            inputAttributes="min=0" inputPlaceholder="Number of volunteers who checked in"
                            required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0' inputValue="{{ old('attendance_percentage') }}"
                            classLabel='mb-1' name="attendance_percentage" elementId="attendance_percentage"
                            inputPlaceholder="" label="Attendance % (Auto-calculated)" inputAttributes="min=0"
                            required="required" disabled="disabled" />
                    </div>
                </div>

                <!-- Volunteer Attendance -->
                <div class="form-section">
                    <h3>Volunteer Meal Redemption</h3>
                    <div class="form-row">
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('volunteers_meals_ordered') }}" name="volunteers_meals_ordered" elementId="volunteers_meals_ordered"
                            label="Meals Ordered" inputPlaceholder="Manual number" inputAttributes="min=0"
                            required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('volunteers_meals_redeemed') }}" name="volunteers_meals_redeemed"
                            elementId="volunteers_meals_redeemed" label="Meals Redeemed"
                            inputAttributes="min=0" inputPlaceholder="Manual number" required="required"
                            disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('volunteer_meal_percentage') }}" name="volunteer_meal_percentage"
                            elementId="volunteer_meal_percentage" inputPlaceholder=""
                            label="Meal Redemption % (Auto-calculated)" inputAttributes="min=0" required="required"
                            disabled="disabled" />
                    </div>
                </div>

                <!-- LOC Staff Meal Redemption -->
                <div class="form-section">
                    <h3>LOC Staff - Meal Redemption</h3>
                    <div class="form-row">
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('loc_staff_meals_ordered') }}" name="loc_staff_meals_ordered" elementId="loc_staff_meals_ordered"
                            label="Meals Ordered" inputPlaceholder="Manual number" inputAttributes="min=0"
                            required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('loc_staff_meals_redeemed') }}" name="loc_staff_meals_redeemed"
                            elementId="loc_staff_meals_redeemed" label="Meals Redeemed" inputAttributes="min=0"
                            inputPlaceholder="Manual number" required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="" name="loc_staff_meal_percentage"
                            elementId="loc_staff_meal_percentage" inputPlaceholder=""
                            label="Meal Redemption % (Auto-calculated)" inputAttributes="min=0" required="required"
                            disabled="disabled" />
                    </div>
                </div>

                                <!-- LOC External Meal Redemption -->
                <div class="form-section">
                    <h3>LOC External - Meal Redemption</h3>
                    <div class="form-row">
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('loc_external_meals_ordered') }}" name="loc_external_meals_ordered" elementId="loc_external_meals_ordered"
                            label="Meals Ordered" inputPlaceholder="Manual number" inputAttributes="min=0"
                            required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="{{ old('loc_external_meals_redeemed') }}" name="loc_external_meals_redeemed"
                            elementId="loc_external_meals_redeemed" label="Meals Redeemed" inputAttributes="min=0"
                            inputPlaceholder="Manual number" required="required" disabled='' />
                        <x-formy.form_input class="col-sm-12 col-md-12  mb-3" inputType="number" floating='0'
                            classLabel='mb-1' inputValue="" name="loc_external_meal_percentage"
                            elementId="loc_external_meal_percentage" inputPlaceholder=""
                            label="Meal Redemption % (Auto-calculated)" inputAttributes="min=0" required="required"
                            disabled="disabled" />
                    </div>
                </div>

                <!-- Additional Notes & Media -->
                <div class="form-section">
                    <h3>Additional Notes & Media</h3>


                    <div class="mb-3">
                        <label class="mb-1" for="incidents">Incidents / Issues</label>
                        <textarea class="form-control" id="incidents" name="incidents" rows="4"
                            placeholder="Describe any incidents or issues...">{{ old('incidents') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="mb-1" for="otherNotes">Other Notes</label>
                        <textarea class="form-control" id="otherNotes" name="other_notes" rows="4"
                            placeholder="Catering, Logistics, IT/Overlay, Medical, Safety, Next Day prep, etc.">{{ old('other_notes') }}</textarea>
                    </div>

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

                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelReport">
                        Cancel
                    </button>
                    <button type="submit" class="primary-btn">
                        <span class="material-icons">save</span>
                        Submit Report
                    </button>
                </div>
            </form>

        </div>
        {{-- </div> --}}
        <!-- <br /> -->
        <!-- &nbsp; -->
    </main>

    <script src="{{ asset('assets/js/pages/wdr/create.js') }}"></script>

    {{-- @include('mds.admin.modals.booking_modals') --}}

@endsection

@push('script')
    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

        });



        const input = document.getElementById('photoUpload');
        const preview = document.getElementById('photoPreview');

        let selectedFiles = [];

        input.addEventListener('change', () => {
            const newFiles = Array.from(input.files);

            // ✅ Append new files, avoid duplicates (same name+size+lastModified)
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
@endpush
