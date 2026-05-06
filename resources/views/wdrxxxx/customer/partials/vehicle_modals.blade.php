<div class="modal fade" id="create_vehicles_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_vehicles', 'Add vehicle') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{route('vapp.setting.vehicle.store')}}" method="POST">
                @csrf
                <input type="hidden" name="table" value="vehicles_table">
                <div class="modal-body">

                    <div class="mb-2 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('vehicle_type', 'Vehicle Type') ?></label>
                        <select class="form-select" name="vehicle_type_id">
                            <option value="" selected>Select vehicle type...</option>
                            @foreach ($vehicle_types as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('vehicle_type', 'Vehicle Type') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="type" placeholder="<?= get_label('please_enter_vehicle_type', 'Please enter vehicle type') ?>" />
                    </div> -->

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('vehicles_make', 'Vehicle Make') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="make" placeholder="<?= get_label('please_enter_vehicles_make', 'Please enter vehicle make') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('license_plate', 'License Plate') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="license_plate" placeholder="<?= get_label('please_enter_license_plate', 'Please enter license plate') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="text-1000 fw-bold mb-2">Status</label>
                        <select name="status_id" class="form-select" id="editDriverStatusSelection" required>
                            <option selected="selected" value="">Select</option>
                            @foreach ($vehicle_statuses as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save','Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_vehicles_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event" action="{{route('vapp.setting.vehicle.update')}}" method="POST">
                @csrf
                <input type="hidden" id="edit_vehicles_id" name="id" value="">
                <input type="hidden" id="edit_vehicles_table" name="table">
                <div class="modal-body">
                    <div class="mb-2 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('vehicle_type', 'Vehicle Type') ?></label>
                        <select class="form-select" name="vehicle_type_id" id="edit_vehicles_type_id">
                            <option value="" selected>Select vehicle type...</option>
                            @foreach ($vehicle_types as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('edit_vehicles_make', 'Vehicle Make') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_vehicles_make" class="form-control" name="make" placeholder="<?= get_label('please_enter_vehicles_make', 'Please enter vehicle make') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('license_plate', 'License Plate') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_vehicles_license_plate" class="form-control" name="license_plate" placeholder="<?= get_label('please_enter_license_plate', 'Please enter licence plate') ?>" />
                    </div>
                    <div class="mb-2 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('status', 'Status') ?></label>
                        <select class="form-select" name="status_id" id="edit_vehicles_status_id">
                            <option value="" selected>Select status...</option>
                            @foreach ($vehicle_statuses as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save','Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="vehicleStatusModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class=" text-white mb-0" id="staticBackdropLabel">Change Status</h3>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-danger"></span></button>
            </div>
            <form class="needs-validation form-submit-event" novalidate="" action="{{route('vapp.vehicle.status.update')}}" method="POST" id="task_status">
                <!-- <form class="needs-validation" novalidate="" action="{{url('/tracki/task/status/update')}}" method="POST" id="task_status"> -->
                @csrf
                <div class="modal-body">
                    <div class="modal-body px-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <input type="hidden" id="editVehicleId" name="id">
                                <input type="hidden" id="vehicleStatusTable" name="table">
                                <div class="mb-4">
                                    <label class="text-1000 fw-bold mb-2">Status</label>
                                    <select name="status_id" class="form-select" id="editVehicleStatusSelection" required>
                                        <option selected="selected" value="">Select</option>
                                        @foreach ($vehicle_statuses as $key => $item )
                                        <option value="{{ $item->id  }}">
                                            {{ $item->title }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <!-- <input class="form-control" type="number" max="100" min="0" name="prorgress_number" id="editPoregessNumber" required /> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" id="submit_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>