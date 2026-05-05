$(document).ready(function () {
    function calculatePercentage(
        orderedSelector,
        redeemedSelector,
        outputSelector,
    ) {
        const ordered = parseFloat($(orderedSelector).val());
        const redeemed = parseFloat($(redeemedSelector).val());

        // stop if either is null / empty / zero
        if (!ordered || !redeemed) {
            $(outputSelector).val("");
            return;
        }

        const pct = (redeemed / ordered) * 100;
        $(outputSelector).val(pct.toFixed(2));
    }

    // 🔹 Attendance
    $("#demand_of_day, #attended").on("input change keyup", function () {
        calculatePercentage(
            "#demand_of_day",
            "#attended",
            "#attendance_percentage",
        );
    });

    // 🔹 Volunteer meal redemption
    $("#volunteers_meals_ordered, #volunteers_meals_redeemed").on(
        "input change keyup",
        function () {
            calculatePercentage(
                "#volunteers_meals_ordered",
                "#volunteers_meals_redeemed",
                "#volunteer_meal_percentage",
            );
        },
    );

    // 🔹 Staff meal redemption
    $("#loc_staff_meals_ordered, #loc_staff_meals_redeemed").on(
        "input change keyup",
        function () {
            calculatePercentage(
                "#loc_staff_meals_ordered",
                "#loc_staff_meals_redeemed",
                "#loc_staff_meal_percentage",
            );
        },
    );

    // 🔹 LOC Staff meal redemption
    $("#loc_external_meals_ordered, #loc_external_meals_redeemed").on(
        "input change keyup",
        function () {
            calculatePercentage(
                "#loc_external_meals_ordered",
                "#loc_external_meals_redeemed",
                "#loc_external_meal_percentage",
            );
        },
    );
});
