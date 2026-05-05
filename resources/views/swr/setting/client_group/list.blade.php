@extends('wdr.layout.admin_template')
@section('main')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    {{-- <div class="content"> --}}
    {{-- <div class="container-fluid"> --}}
    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('client_groups', 'Client Group') ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <x-button_insert_modal bstitle='Add Client Group' bstarget="#create_client_groups_modal" />
            <!-- <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_event_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_event', 'Create Event') ?>"><i class="bx bx-plus"></i></button></a> -->
        </div>
    </div>
    <x-setting.client_group-card />
    {{-- </div> --}}

    @include('wdr.setting.modals.gms_client_group_modals')
    <script>
        var label_update = '<?= get_label('update', 'Update') ?>';
        var label_delete = '<?= get_label('delete', 'Delete') ?>';
        var label_not_assigned = '<?= get_label('not_assigned', 'Not assigned') ?>';
        var label_duplicate = '<?= get_label('duplicate', 'Duplicate') ?>';
    </script>
    <script src="{{ asset('assets/js/pages/mds/events.js') }}"></script>
@endsection

@push('script')
    <script>
        // showing the offcanvas for the task creation
        $(document).ready(function() {
            console.log('ready');
            $('.dropify').dropify();

        });
    </script>
@endpush
