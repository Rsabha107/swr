<div class="modal fade" id="create_intervalss_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_intervalss', 'Create intervals') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{route('vapp.setting.interval.store')}}" method="POST">
                @csrf
                <input type="hidden" name="table" value="intervals_table">
                <input type="hidden" name="delivery_schedule_id" value="{{$schedule->id}}">
                <input type="hidden" name="venue_id" value="{{$schedule->venue_id}}">
                <div class="modal-body">

                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="inputEmail4"><?= get_label('period_date', 'Period date') ?></label>
                        <input class="form-control datetimepicker" data-target="#floatingInputStartDate" name="period_date" type="date" placeholder="dd/mm/yyyy" data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' required>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('period', 'Period') ?> <span class="asterisk">*</span></label>
                        <input required type="text" class="form-control" name="period" placeholder="<?= get_label('please_enter_period', 'Please enter period') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('used_slot', 'Max slots') ?> <span class="asterisk">*</span></label>
                        <input required type="text" class="form-control" name="max_slots" placeholder="<?= get_label('please_enter_max_slot', 'Please enter max slots') ?>" />
                    </div>
                    <!-- <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('available_slots', 'Available slots') ?> <span class="asterisk">*</span></label>
                        <input required type="text" class="form-control" name="available_slots" placeholder="<?= get_label('please_enter_available_slots', 'Please enter available slots') ?>" />
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('used_slot', 'Used slots') ?> <span class="asterisk">*</span></label>
                        <input required type="text" class="form-control" name="used_slots" placeholder="<?= get_label('please_enter_used_slot', 'Please enter used slots') ?>" />
                    </div> -->
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

<div class="modal fade" id="edit_intervals_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event" action="{{route('vapp.setting.interval.update')}}" method="POST">
                @csrf
                <input type="hidden" id="edit_intervals_id" name="id" value="">
                <input type="hidden" id="edit_intervals_table" name="table">
                <div class="modal-body">

                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="inputEmail4"><?= get_label('regime_start_date', 'Regime start date') ?></label>
                        <input class="form-control datetimepicker" data-target="#floatingInputStartDate" id="edit_regime_start_date" name="regime_start_date" type="date" placeholder="dd/mm/yyyy" data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' required>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="inputEmail4"><?= get_label('regime_end_date', 'Regime end date') ?></label>
                        <input class="form-control datetimepicker" data-target="#floatingInputEndDate" id="edit_regime_end_date" name="regime_end_date" type="date" placeholder="dd/mm/yyyy" data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' required>
                    </div>

                    <div class="mb-2 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('venue', 'Venue') ?><span class="asterisk">*</span></label>
                        <select class="form-select" name="interval_venue_id" id="edit_interval_venue_id" required>
                            <option value="" selected>Select venue...</option>
                            @foreach ($venues as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('rsp', 'RSP') ?><span class="asterisk">*</span></label>
                        <select class="form-select" name="interval_rsp_id" id="edit_schedule_rsp_id" required>
                            <option value="" selected>Select RSP...</option>
                            @foreach ($rsps as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label for="nameBasic" class="form-label"><?= get_label('timeslots', 'Timeslots') ?> <span class="asterisk">*</span></label>
                        <input required type="text" class="form-control" id="edit_time_slots" name="time_slots" placeholder="<?= get_label('please_enter_timeslots', 'Please enter timeslots') ?>" />
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