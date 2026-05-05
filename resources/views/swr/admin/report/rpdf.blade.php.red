<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance & Meal Report</title>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }

    /* CENTER WHOLE CONTENT */
    .container {
        width: 700px;              /* fixed width for proper centering */
        margin: 0 auto;            /* center horizontally */
        padding: 20px 0;
    }

    .header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
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
        text-align: center;        /* centers inline-block cards */
        margin-top: 15px;
    }

    .kpi-card {
        width: 200px;
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
</style>
</head>
<body>

<div class="container">

    <div class="header">
        Volunteer & Meal Operations Summary
    </div>

    {{-- Volunteer Attendance --}}
    <div class="section-title">Volunteer Attendance</div>
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_demand ?? 1111 }}</div>
            <div class="kpi-label">Demand of the Day</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_attended ?? 950 }}</div>
            <div class="kpi-label">Attended</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_percentage ?? '86%' }}</div>
            <div class="kpi-label">Attendance %</div>
        </div>
    </div>

    {{-- Volunteer Meals --}}
    <div class="section-title">Volunteer - Meal Redemption</div>
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_meals_ordered ?? 1111 }}</div>
            <div class="kpi-label">Meals Ordered</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_meals_redeemed ?? 950 }}</div>
            <div class="kpi-label">Meals Redeemed</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->volunteer_meal_percentage ?? '86%' }}</div>
            <div class="kpi-label">Meal Redemption %</div>
        </div>
    </div>

    {{-- LOC Staff Meals --}}
    <div class="section-title">LOC Staff - Meal Redemption</div>
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_staff_meals_ordered ?? 1111 }}</div>
            <div class="kpi-label">Meals Ordered</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_staff_meals_redeemed ?? 950 }}</div>
            <div class="kpi-label">Meals Redeemed</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_staff_meal_percentage ?? '86%' }}</div>
            <div class="kpi-label">Meal Redemption %</div>
        </div>
    </div>

    {{-- LOC External Meals --}}
    <div class="section-title">LOC External - Meal Redemption</div>
    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_external_meals_ordered ?? 1111 }}</div>
            <div class="kpi-label">Meals Ordered</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_external_meals_redeemed ?? 950 }}</div>
            <div class="kpi-label">Meals Redeemed</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $op->loc_external_meal_percentage ?? '86%' }}</div>
            <div class="kpi-label">Meal Redemption %</div>
        </div>
    </div>

    <div class="footer">
        Generated on {{ now()->format('d M Y H:i') }}
    </div>

</div>

</body>
</html>