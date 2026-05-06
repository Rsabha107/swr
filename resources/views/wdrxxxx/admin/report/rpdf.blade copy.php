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
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

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
            border: 1px solid #eee;
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
        {{-- list demand of the day, attended, % in a table --}}
        <table class="centered-table" width="100%" cellspacing="0" cellpadding="6"
            style="border-collapse:collapse;margin-top:16px;">
            <colgroup>
                <col width="33.33%">
                <col width="33.33%">
                <col width="33.33%">
            </colgroup>

            <thead>
                <tr style="background:#f3f4f6; font-weight:700;">
                    <th>Demand of the Day</th>
                    <th>Attended</th>
                    <th>Percentage (%)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{{ $wdr->demand_of_day }}</td>
                    <td>{{ $wdr->attended }}</td>
                    <td style="font-weight:700;">{{ $wdr->attendance_percentage }}%</td>
                </tr>
            </tbody>
        </table>
        <table class="centered-table" width="100%" cellspacing="0" cellpadding="6"
            style="border-collapse:collapse;margin-top:16px;">
            <colgroup>
                <col width="33.33%">
                <col width="33.33%">
                <col width="33.33%">
            </colgroup>

            <thead>
                <tr style="background:#f3f4f6; font-weight:700;">
                    <th>Meals Ordered (Volunteers)</th>
                    <th>Meals Redeemed (Volunteers)</th>
                    <th>Percentage (%)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{{ $wdr->meals_ordered_volunteers }}</td>
                    <td>{{ $wdr->meals_redeemed_volunteers }}</td>
                    <td style="font-weight:700;">{{ $wdr->volunteers_meal_percentage }}%</td>
                </tr>
            </tbody>
        </table>
        <table class="centered-table" width="100%" cellspacing="0" cellpadding="6"
            style="border-collapse:collapse;margin-top:16px;">
            <colgroup>
                <col width="33.33%">
                <col width="33.33%">
                <col width="33.33%">
            </colgroup>

            <thead>
                <tr style="background:#f3f4f6; font-weight:700;">
                    <th>Meals Ordered (Staff)</th>
                    <th>Meals Redeemed (Staff)</th>
                    <th>Percentage (%)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>{{ $wdr->meals_ordered_staff }}</td>
                    <td>{{ $wdr->meals_redeemed_staff }}</td>
                    <td style="font-weight:700;">{{ $wdr->staff_meal_percentage }}%</td>
                </tr>
            </tbody>
        </table>

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
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
