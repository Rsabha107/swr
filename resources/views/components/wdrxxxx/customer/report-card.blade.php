<!-- meetings -->
<style>
    th[colspan] {
        border-bottom: 2px solid #dee2e6;
    }

    th.bg-light {
        background-color: #e7e997 !important;
    }

    th.bg-light-green {
        background-color: #d4edda !important;
    }
</style>
<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            {{ $slot }}
            <input type="hidden" id="data_type" value="report">
            <div class="mx-2 mb-2">
                <table id="report_table" data-toggle="table"
                    data-classes="table table-hover  fs-9 mb-0 border-top border-translucent"
                    data-loading-template="loadingTemplate" data-url="{{ route('wdr.report.list') }}"
                    data-icons-prefix="bx" data-icons="icons"
                    data-show-columns-toggle-all="true" data-show-refresh="true" data-show-toggle="true"
                    data-total-field="total" data-trim-on-search="false" data-data-field="rows"
                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-searchable="true"
                    data-strict-search="true" data-side-pagination="server" data-show-columns="true"
                    data-pagination="true" data-filter-control="true" data-filter-control-visible="true"
                    data-show-search-clear-button="true" data-sort-name="id" data-sort-order="desc"
                    data-mobile-responsive="true" data-buttons-class="secondary" data-query-params="guestQueryParams">

                    <thead>
                        {{-- <tr> --}}
                        {{-- <th rowspan="2"></th> --}}

                        {{-- <th colspan="2" class="text-center bg-light fw-semibold">

                            </th>

                            <th colspan="3" class="text-center bg-light-green fw-semibold">
                                Guardian Details
                            </th>

                            <th colspan="11" class="text-center bg-light fw-semibold">
                                Participant Details
                            </th> --}}

                        {{-- <th rowspan="1" class="text-end">
                                Action
                            </th> --}}
                        {{-- </tr> --}}
                        <tr>
                            <th data-field="id" data-visible="false">ID</th>
                            <th data-field="image"></th>
                            <th data-field="ref_number">Ref Number</th>
                            <th data-field="venue_id">Venue</th>
                            <th data-field="reported_by">Report By</th>
                            <th data-field="report_date">Report Date</th>
                            <th data-field="event_id">Event</th>
                            <th data-field="day_type_id">Day Type</th>
                            <th data-field="demand_of_day" data-class="text-col-center">Demand of the Day</th>
                            <th data-field="attended" data-class="text-col-center">Attended</th>
                            <th data-field="attendance_percentage" data-class="text-col-center">Attendance %</th>
                            <th data-field="volunteers_meals_ordered" data-class="text-col-center">Meals Ordered (Volunteers)</th>
                            <th data-field="volunteers_meals_redeemed" data-class="text-col-center">Meals Redeemed (Volunteers)</th>
                            <th data-field="volunteer_meal_percentage" data-class="text-col-center">Volunteer Meal %</th>
                            <th data-field="loc_staff_meals_ordered" data-class="text-col-center">Meals Ordered (LOC Staff)</th>
                            <th data-field="loc_staff_meals_redeemed" data-class="text-col-center">Meals Redeemed (LOC Staff)</th>
                            <th data-field="loc_staff_meal_percentage" data-class="text-col-center">LOC Staff Meal %</th>
                            <th data-field="incidents">Incedents</th>
                            <th data-field="other_notes">Other Notes</th>
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
