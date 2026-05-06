<div class="modal fade" id="create_nationalities_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                Create Nationality'
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="form_submit_event" action="{{ route('wdr.setting.nationality.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="nationalities_table">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input required type="text" class="form-control" name="title" placeholder="Enter title" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Num Code</label>
                            <input type="text" class="form-control" name="num_code" placeholder="Enter num code" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alpha2 Code</label>
                            <input type="text" class="form-control" name="alpha_2_code" maxlength="2" placeholder="Enter alpha2 code" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alpha3 Code</label>
                            <input type="text" class="form-control" name="alpha_3_code" maxlength="3" placeholder="Enter alpha3 code" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">English Short Name</label>
                            <input type="text" class="form-control" name="en_short_name" placeholder="Enter English short name" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= get_label('save','Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit_nationalities_modal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Edit Nationality</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="needs-validation" id="edit_form_submit_event" action="{{ route('wdr.setting.nationality.update') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_nationalities_id" name="id">
                <input type="hidden" id="edit_nationalities_table" name="table">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_nationalities_title" class="form-control" name="title" placeholder="Enter title" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Num Code</label>
                            <input type="text" id="edit_nationalities_num_code" class="form-control" name="num_code" placeholder="Enter num code" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alpha2 Code</label>
                            <input type="text" id="edit_nationalities_alpha_2_code" class="form-control" name="alpha_2_code" maxlength="2" placeholder="Enter alpha2 code" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alpha3 Code</label>
                            <input type="text" id="edit_nationalities_alpha3_code" class="form-control" name="alpha_3_code" maxlength="3" placeholder="Enter alpha3 code" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">English Short Name</label>
                            <input type="text" id="edit_nationalities_en_short_name" class="form-control" name="en_short_name" placeholder="Enter English short name" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= get_label('save','Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
