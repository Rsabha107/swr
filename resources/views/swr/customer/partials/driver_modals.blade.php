<div class="modal fade" id="create_drivers_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_drivers', 'Create vehicle types') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{route('vapp.setting.driver.store')}}" method="POST">
                @csrf
                <input type="hidden" name="table" value="drivers_table">
                <div class="modal-body">

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('first_name', 'First Name') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="first_name" placeholder="<?= get_label('please_enter_first_name', 'Please enter first name') ?>" />
                    </div>

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('last_name', 'Last Name') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="last_name" placeholder="<?= get_label('please_enter_last_name', 'Please enter last name') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('mobile_number', 'Mobile Number') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="mobile_number" placeholder="<?= get_label('please_enter_mobile_number', 'Please enter mobile number') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('qid_passport', 'QID/Passport') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="nameBasic" class="form-control" name="national_identifier_number" placeholder="<?= get_label('please_enter_national_identifier_number', 'Please enter QID/Passport') ?>" />
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

<div class="modal fade" id="edit_drivers_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event" action="{{route('vapp.setting.driver.update')}}" method="POST">
                @csrf
                <input type="hidden" id="edit_drivers_id" name="id" value="">
                <input type="hidden" id="edit_drivers_table" name="table">
                <div class="modal-body">
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('first_name', 'First Name') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_drivers_first_name" class="form-control" name="first_name" placeholder="<?= get_label('please_enter_first_name', 'Please enter first name') ?>" />
                    </div>

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('last_name', 'Last Name') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_drivers_last_name" class="form-control" name="last_name" placeholder="<?= get_label('please_enter_last_name', 'Please enter last name') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('mobile_number', 'Mobile Number') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_drivers_mobile_number" class="form-control" name="mobile_number" placeholder="<?= get_label('please_enter_mobile_number', 'Please enter mobile number') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('qid_passport', 'QID/Passport') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="edit_drivers_national_identifier_number" class="form-control" name="national_identifier_number" placeholder="<?= get_label('please_enter_national_identifier_number', 'Please enter QID/Passport') ?>" />
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

<div class="modal fade" id="driverStatusModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class=" text-white mb-0" id="staticBackdropLabel">Change Status</h3>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span class="fas fa-times fs--1 text-danger"></span></button>
            </div>
            <form class="needs-validation form-submit-event" novalidate="" action="{{route('vapp.driver.status.update')}}" method="POST" id="task_status">
                <!-- <form class="needs-validation" novalidate="" action="{{url('/tracki/task/status/update')}}" method="POST" id="task_status"> -->
                @csrf
                <div class="modal-body">
                    <div class="modal-body px-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <input type="hidden" id="editDriverId" name="id">
                                <input type="hidden" id="driverStatusTable" name="table">
                                <div class="mb-4">
                                    <label class="text-1000 fw-bold mb-2">Status</label>
                                    <select name="status_id" class="form-select" id="editDriverStatusSelection" required>
                                        <option selected="selected" value="">Select</option>
                                        @foreach ($driver_statuses as $key => $item )
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