<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $wdr->reference_number }}</title>

    <!-- Favicon -->
    <link rel="icon" href="./images/favicon.png" type="image/x-icon" />

    <!-- Invoice styling -->
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        /* CENTER WHOLE CONTENT */
        .container {
            width: 700px;
            /* fixed width for proper centering */
            margin: 0 auto;
            /* center horizontally */
            padding: 20px 0;
        }

        .header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .section-title {
            background: #8B1D3A;
            color: #fff;
            padding: 8px;
            font-weight: bold;
            margin-top: 25px;
            text-align: center;
        }

        /* CENTER KPI ROW */
        .kpi-row {
            text-align: center;
            /* centers inline-block cards */
            margin-top: 15px;
        }

        .kpi-card {
            width: 150px;
            display: inline-block;
            background: #f3f3f3;
            padding: 18px 10px;
            margin: 5px;
            text-align: center;
            border-radius: 6px;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: bold;
        }

        .kpi-label {
            font-size: 11px;
            color: #666;
            margin-top: 6px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 35px;
        }

        /* body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        } */

        .invoice-box table tr td h4 {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        /* body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        } */
        body h1 {
            font-size: 16;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
            /* border: 1px solid #eee; */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 30px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .centered-table th,
        .centered-table td {
            text-align: center !important;
        }

        @media print {
            .invoice-box {
                max-width: unset;
                box-shadow: none;
                border: 0px;
            }
        }
    </style>
</head>

<body>
    <!-- <h1>A simple, clean, and responsive HTML invoice template</h1>
    <h3>Because sometimes, all you need is something simple.</h3>
    Find the code on <a href="https://github.com/sparksuite/simple-html-invoice-template">GitHub</a>. Licensed under the
    <a href="http://opensource.org/licenses/MIT" target="_blank">MIT license</a>.<br /><br /><br /> -->

    {{-- @php
    $destination = $booking->rsp->latitude . ',' . $booking->rsp->longitude;
    @endphp --}}

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ public_path('assets/img/logos/sc_logo_gray_blue.png') }}"
                                    alt="Company logo" style="width: 120%; max-width: 150px" />
                            </td>
                            {{-- <td class="">
                                <h4 class="centertext">Workforce Daily Report (WDR)
                                    <br>WDR Ref: <b>{{ $wdr->reference_number }}</b>
                                    <br>Event: <b>{{ $wdr->event?->name }}</b>
                                    <br>Venue: <b>{{ $wdr->venue?->title }}</b>
                                </h4>
                            </td> --}}
                            {{-- <td class="title">
                                <!-- <p class="mb-0 ms-3 text-900 zoom"> {{ $qr_code }}</p> -->
                                <img src="data:image/png;base64, {{ $qr_code }}">
                                <!-- <img src="{{ public_path('assets/img/gallery/Qrcode_wikipedia.jpg') }}" alt="Company logo" style="width: 100%; max-width: 100px" /> -->
                            </td> --}}

                            <!-- <td>
                                Invoice #: 123<br />
                                Created: January 1, 2023<br />
                                Due: February 1, 2023
                            </td> -->
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h4 class="centertext">Workforce Daily Report (WDR)
                        <br>WDR Ref: <b>{{ $wdr->reference_number }}</b>
                        <br>Event: <b>{{ $wdr->event?->name }}</b>
                        <br>Venue: <b>{{ $wdr->venue?->title }}</b>
                    </h4>
                </td>
            </tr>
            <tr class="heading">
                <td colspan="2">WDR Details</td>
                <!-- <td>Price</td> -->
            </tr>

            <tr class="item">
                <td>Event</td>
                <td>{{ $wdr->event?->name }}</td>
            </tr>
            <tr class="item">
                <td>Venue Name:</td>
                <td>{{ $wdr->venue?->title }}</td>
            </tr>
            <tr class="item">
                <td>WDR Date:</td>
                <td>{{ $wdr->report_date }}</td>
            </tr>
            <tr class="item">
                <td>Day Type:</td>
                <td>{{ $wdr->dayType->title }}</td>
            </tr>
            {{-- <tr class="item">
                <td>RSP:</td>
                <td><a href="https://www.google.com/maps/dir/?api=1&destination={{ $destination }}" target="_blank"
                        class="btn btn-primary">{{ $booking->schedule->rsp->title }}</a></td>
            </tr> --}}
            {{-- <tr class="item">
                <td>RSP Arrival Date:</td>
                <td>{{ $rsp_arrival_date }}</td>
            </tr>
            <tr class="item">
                <td>RSP Arrival Time:</td>
                <td>{{ time_range_segment($booking->schedule?->rsp_booking_slot, 'from') }}</td>
            </tr> --}}
            <tr class="item">
                <td>Reporeted By :</td>
                <td>{{ $wdr->reportedBy->name }}</td>
            </tr>
        </table>

        <div class="header">
            Volunteer & Meal Operations Summary
        </div>

        {{-- Volunteer Attendance --}}
        <div class="section-title">Volunteer Attendance</div>
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->demand_of_day ?? 1111 }}</div>
                <div class="kpi-label">Demand of the Day</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->attended ?? 950 }}</div>
                <div class="kpi-label">Attended</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->attendance_percentage ?? '86%' }}</div>
                <div class="kpi-label">Attendance %</div>
            </div>
        </div>

        {{-- Volunteer Meals --}}
        <div class="section-title">Volunteer - Meal Redemption</div>
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->volunteers_meals_ordered ?? 'NA' }}</div>
                <div class="kpi-label">Meals Ordered</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->volunteers_meals_redeemed ?? 'NA' }}</div>
                <div class="kpi-label">Meals Redeemed</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->volunteer_meal_percentage ?? 'NA' }}</div>
                <div class="kpi-label">Meal Redemption %</div>
            </div>
        </div>

        {{-- LOC Staff Meals --}}
        <div class="section-title">LOC Staff - Meal Redemption</div>
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_staff_meals_ordered ?? 'NA' }}</div>
                <div class="kpi-label">Meals Ordered</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_staff_meals_redeemed ?? 'NA' }}</div>
                <div class="kpi-label">Meals Redeemed</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_staff_meal_percentage ?? 'NA' }}</div>
                <div class="kpi-label">Meal Redemption %</div>
            </div>
        </div>

        {{-- LOC External Meals --}}
        <div class="section-title">LOC External - Meal Redemption</div>
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_external_meals_ordered ?? 'NA' }}</div>
                <div class="kpi-label">Meals Ordered</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_external_meals_redeemed ?? 'NA' }}</div>
                <div class="kpi-label">Meals Redeemed</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-value">{{ $wdr->loc_external_meal_percentage ?? 'NA' }}</div>
                <div class="kpi-label">Meal Redemption %</div>
            </div>
        </div>

        <div class="footer">
            Generated on {{ now()->format('d M Y H:i') }}
        </div>
        <table>
            <tr>
                {{-- Incidents / Issues (bottom section) --}}
                <td colspan="2" style="padding-top:12px; page-break-inside:avoid;">
                    <div style="margin-top: 18px;">
                        <div
                            style="
                            background: #f3f4f6;
                            border: 1px solid #e5e7eb;
                            padding: 8px 10px;
                            font-weight: 700;
                            font-size: 13px;
                            text-transform: uppercase;
                            letter-spacing: .3px;
                        ">
                            Incidents / Issues
                        </div>

                        <div
                            style="
                            border: 1px solid #e5e7eb;
                            border-top: 0;
                            padding: 12px 10px;
                            min-height: 90px;
                            font-size: 12.5px;
                            line-height: 1.6;
                            color: #111827;
                            background: #ffffff;
                            white-space: pre-line;
                        ">
                            {{ filled($wdr->incidents) ? $wdr->incidents : 'No incidents reported.' }}
                        </div>
                    </div>
                    <div style="margin-top: 14px;">
                        <div
                            style="
                            background: #f3f4f6;
                            border: 1px solid #e5e7eb;
                            padding: 8px 10px;
                            font-weight: 700;
                            font-size: 13px;
                            text-transform: uppercase;
                            letter-spacing: .3px;
                        ">
                            Other Notes
                        </div>

                        <div
                            style="
                            border: 1px solid #e5e7eb;
                            border-top: 0;
                            padding: 12px 10px;
                            min-height: 70px;
                            font-size: 12.5px;
                            line-height: 1.6;
                            color: #111827;
                            background: #ffffff;
                            white-space: pre-line;
                        ">
                            {{ filled($wdr->other_notes) ? $wdr->other_notes : 'No additional notes.' }}
                        </div>
                    </div>
                    {{-- Images --}}
                    @if ($wdr->photos && $wdr->photos->isNotEmpty())
                        <div style="margin-top: 16px; page-break-inside: avoid;">
                            <div
                                style="
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
                padding: 8px 10px;
                font-weight: 700;
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: .3px;
            ">
                                Incident Images
                            </div>

                            <div
                                style="
                border: 1px solid #e5e7eb;
                border-top: 0;
                padding: 10px;
                background: #ffffff;
            ">
                                <table width="100%" cellspacing="6" cellpadding="0"
                                    style="border-collapse: collapse;">
                                    <tr>
                                        @foreach ($wdr->photos as $i => $doc)
                                            @php
                                                $img = private_image_base64($doc->disk, $doc->path);
                                            @endphp

                                            @if ($img)
                                                <td width="33.33%"
                                                    style="text-align:center; vertical-align:top; page-break-inside:avoid;">
                                                    <img src="{{ $img }}"
                                                        style="
                                        width:100%;
                                        max-width:180px;
                                        height:auto;
                                        border:1px solid #e5e7eb;
                                        padding:4px;
                                        background:#fff;
                                     ">
                                                    <div style="font-size:10px; margin-top:4px; color:#6b7280;">
                                                        {{ $doc->custom_name }}
                                                    </div>
                                                </td>
                                            @endif

                                            @if (($i + 1) % 3 === 0)
                                    </tr>
                                    <tr>
                    @endif
                    @endforeach
            </tr>
        </table>
    </div>
    </div>
    @endif

    </td>
    </tr>
    </table>
    </div>
</body>

</html>
