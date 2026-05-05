<!-- meetings -->

<div class="card mt-4 mb-4 report-table-card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{ $slot }}
            <input type="hidden" id="data_type" value="report">
            <div class="mx-2 mb-2">
                <table id="report_table" data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 align-middle border-top border-translucent"
                    data-loading-template="loadingTemplate" data-url="{{ route('swr.admin.report.list') }}"
                    data-icons-prefix="bx" data-icons="icons" data-show-export="true"
                    data-export-types="['csv', 'txt', 'doc', 'excel', 'xlsx', 'pdf']"
                    data-show-columns-toggle-all="true" data-show-refresh="true" data-show-toggle="true"
                    data-total-field="total" data-trim-on-search="false" data-data-field="rows"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-searchable="true"
                    data-strict-search="true" data-side-pagination="server" data-show-columns="true"
                    data-pagination="true" data-filter-control="true" data-filter-control-visible="true"
                    data-show-search-clear-button="true" data-sort-name="id" data-sort-order="desc"
                    data-mobile-responsive="true" data-buttons-class="secondary" data-query-params="guestQueryParams">

                    <thead>
                        <tr>
                            <th data-field="id" data-visible="false">ID</th>
                            <th data-field="image" data-class="text-col-center"></th>
                            <th data-field="ref_number" data-class="text-col-center">Reference Number</th>
                            <th data-field="name" data-class="text-col-center">Name</th>
                            <th data-field="role" data-class="text-col-center">Role</th>
                            <th data-field="city" data-class="text-col-center">City</th>
                            <th data-field="venue_id" data-class="text-col-center">Venue</th>
                            <th data-field="reporting_week" data-class="text-col-center">Reporting Week</th>
                            <th data-field="event_id" data-class="text-col-center">Event</th>
                            <th data-field="main_activities" data-class="text-col-center">Main Activities</th>
                            <th data-field="experience_gained" data-class="text-col-center">Experience Gained</th>
                            <th data-field="innovation_description" data-class="text-col-center">Innovation</th>
                            <th data-field="challenges_description" data-class="text-col-center">Challenges</th>
                            <th data-field="challenges_resolved" data-class="text-col-center">Resolved</th>
                            <th data-field="wellbeing_status" data-class="text-col-center">Wellbeing Status</th>
                            <th data-field="needs_support" data-class="text-col-center">Needs Support</th>
                            <th data-field="value_for_qatar" data-class="text-col-center">Value for Qatar</th>
                            <th data-field="status" data-class="text-col-center">Status</th>
                            <th data-field="created_at" data-visible="false">Created At</th>
                            <th data-field="updated_at" data-visible="false">Updated At</th>

                            <th data-field="action" class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    ("use strict");

    function guestQueryParams(p) {
        return {
            page: p.offset / p.limit + 1,
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            search: p.search,
            filter: p.filter ? p.filter : '',
        };
    }

    window.icons = {
        refresh: "bx-refresh",
        toggleOn: "bx-toggle-right",
        toggleOff: "bx-toggle-left",
        fullscreen: "bx-fullscreen",
        columns: "bx-list-ul",
        export_data: "bx-list-ul",
        clearSearch: "bx-x-circle",
    };

    $('#participant_table').on('post-header.bs.table', function() {
        $('#participant_table').bootstrapTable('initFilterControls');
    });

    function loadingTemplate(message) {
        return '<i class="bx bx-loader-circle bx-spin bx-flip-vertical" ></i>';
    }

    $("#mds_schedule_event_filter,#mds_schedule_venue_filter,#mds_schedule_rsp_filter").on("change", function(e) {
        e.preventDefault();
        console.log("tasks.js on change");
        $("#participant_table").bootstrapTable("refresh");
    });
</script>
