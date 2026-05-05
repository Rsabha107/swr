<div class="modal fade" id="booking_details_modal" tabindex="-1" aria-labelledby="bookingDetails_modal" aria-hidden="true">

    <!-- <div class="d-flex justify-content-center">
        <div class="spinner-border" style="display:block;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div> -->
    <div id="booking_details_modal_control"></div>
</div>

<div class="modal fade" id="booking_calendar_modal" tabindex="-1" aria-labelledby="bookingDetails_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('select_time', 'Select time') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="d-flex justify-content-center">
                {{-- <div id="cover-spin" style="display:none;" role="status"> --}}
                    <div class="spinner-border text-primary" id="cover-spin" style="display:none;">
                    {{-- <span class="visually-hidden">Loading...</span> --}}
                </div>
            </div>

            <div class="modal-body text-center mb-3">
                <div id="calendar"></div>
                <div class="spinner-border text-primary" id="loading" style="display:none;"></div>
                    {{-- <img id="loading-image" src="{{ asset('assets/img/loader/Loading_2.gif') }}" alt="Loading..." /></div> --}}
            </div>
            
            {{-- <div class="modal-body text-center mb-3">
                <div id="calendar"></div>
            </div> --}}
            {{-- <div class="modal-body text-center">
                <div class="d-flex justify-content-center">
                    <div id="cover-spin" style="display:none;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div> --}}
            <div class="modal-body text-center mb-3">
                <div id="available-time">Available time slots (pick a date)</div>
                <div class="modal-body">
                    <div class="mb-3 col-md-12">
                        <!-- <label class="form-label" for="bootstrap-wizard-validation-gender"></label> -->
                        <select class="form-select" name="schedule_period_id" id="add_schedule_times_cal">
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="select_time_cal_btn"><?= get_label('select', 'Select') ?></label></button>
            </div>
        </div>
    </div>
    <!-- <div id="calendar"></div> -->
</div>

<div class="modal fade" id="delivery_schedule_times_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('select_time', 'Select time') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <input type="hidden" name="table" value="zones_table">
            <div class="modal-body">
                <div class="mb-3 col-md-12">
                    <!-- <label class="form-label" for="bootstrap-wizard-validation-gender"></label> -->
                    <select class="form-select" name="schedule_period_id" id="add_schedule_times">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary"
                    id="select_time_btn"><?= get_label('select', 'Select') ?></label></button>
            </div>
        </div>
    </div>
</div>