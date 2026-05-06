<div class="modal fade" id="create_zones_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_zone', 'Add Zone') ?></h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{route('vapp.setting.zone.store')}}" method="POST">
                @csrf
                <input type="hidden" name="table" value="zones_table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input required type="text" id="nameBasic" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    {{-- <!-- <div class="mb-3 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('venue', 'Venue') ?></label>
                        <select class="form-select" name="venue_id" id="add_zone_venue_id">
                            <option value="" selected>Select venue...</option>
                            @foreach ($venues as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div> --> --}}
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

<div class="modal fade" id="edit_zones_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Loading Zone</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="edit_form_submit_event" action="{{route('vapp.setting.zone.update')}}" method="POST">
                @csrf
                <input type="hidden" id="edit_zone_id" name="id" value="">
                <input type="hidden" id="edit_zone_table" name="table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_zone_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    {{-- <!-- <div class="mb-3 col-md-12">
                        <label class="form-label" for="bootstrap-wizard-validation-gender"><?= get_label('venue', 'Venue') ?></label>
                        <select class="form-select" name="venue_id" id="edit_zone_venue_id">
                            <option value="" selected>Select venue...</option>
                            @foreach ($venues as $key => $item )
                            <option value="{{ $item->id  }}">
                                {{ $item->title }}
                            </option>
                            @endforeach
                        </select>
                    </div> --> --}}
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