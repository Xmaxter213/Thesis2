<?php

$individualPatients = array();
$adlCount = array();
$pulse_Rate = array();
$battery_Percent = array();

$time_Response_Adl = array();
$time_Response_Immediate = array();

$sql = "SELECT patient_ID, patient_Name, admission_Status, assistance_Status, gloves_ID, device_ID, ADL_Count, ADL_Avg_Response, immediate_Count,
immediate_Avg_Response, assistance_Given, pulse_Rate, battery_percent
FROM patient_List INNER JOIN arduino_Device_List ON patient_List.gloves_ID=arduino_Device_List.device_ID";
$result = mysqli_query($con, $sql);


if ($result->num_rows > 0) {
    // This code is preparing to calculate some statistics on a set of responses, specifically the total number of responses and the total response time.
    $Adl_total_responses = $result->num_rows;
    $Adl_total_response_time = 0;

    $Immediate_total_responses = $result->num_rows;
    $Immediate_total_response_time = 0;

    while ($row = $result->fetch_assoc()) {
        $patientName = decryptthis($row["patient_Name"], $key);

        // Calculation for the series of ADL activities
        // $time_parts = explode(":", $row["ADL_Avg_Response"]);
        // $Adl_total_response_time += $time_parts[0] * 3600 + $time_parts[1] * 60 + $time_parts[2];
        // $Adl_average_response_time = $Adl_total_response_time / $Adl_total_responses;

        // Calculation for the series of Immediate activities
        // $time_parts2 = explode(":", $row["immediate_Avg_Response"]);
        // $Immediate_total_response_time += $time_parts2[0] * 3600 + $time_parts2[1] * 60 + $time_parts2[2];
        // $Immediate_average_response_time = $Immediate_total_response_time / $Immediate_total_responses;

        // Data Retrieval of Individual Reports Chart
        array_push($individualPatients, array("label" => $patientName, "y" => $row['immediate_Count']));
        array_push($adlCount, array("label" => $patientName, "y" => $row['ADL_Count']));
        array_push($battery_Percent, array("label" => $patientName, "y" => $row['battery_percent']));
        array_push($pulse_Rate, array("label" => $patientName, "y" => $row['pulse_Rate']));

        // Data Retrieval of Response Time Chart
        array_push($time_Response_Adl, array("label" => $patientName, "y" => $row['ADL_Avg_Response']));
        array_push($time_Response_Immediate, array("label" => $patientName, "y" => $row['immediate_Avg_Response']));
    }
}

?>

<script>
    // $('#nav-tab a[#nav-individual-reports]').on("show.bs.tab", function(e) {
    //     console.log(e);
    // })
    window.onload = function () {
        // console.log("This fired!");
        var chart1 = new CanvasJS.Chart("immediateChart", {
            theme: "light2",
            exportEnabled: true,
            animationEnabled: true,
            title: {
                text: "Patient Reports"
            },
            axisY: {
                includeZero: true
            },
            legend: {
                cursor: "pointer",
                itemclick: toggleDataSeries
            },
            toolTip: {
                shared: true,
                content: toolTipFormatter
            },
            data: [{
                type: "bar",
                showInLegend: true,
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 16,
                indexLabelFontWeight: "bolder",
                name: "Immediate Count",
                color: "rgb(223,121,112)",
                dataPoints: <?php echo json_encode($individualPatients, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 16,
                indexLabelFontWeight: "bolder",
                name: "ADL Count",
                color: "rgb(119,160,51)",
                dataPoints: <?php echo json_encode($adlCount, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 16,
                indexLabelFontWeight: "bolder",
                name: "Battery Percentage",
                color: "rgb(128,100,161)",
                dataPoints: <?php echo json_encode($battery_Percent, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                indexLabel: "{y}",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 16,
                indexLabelFontWeight: "bolder",
                name: "Pulse Rate",
                color: "rgb(74,172,197)",
                dataPoints: <?php echo json_encode($pulse_Rate, JSON_NUMERIC_CHECK); ?>
            },
            ]
        });

        var chart2 = new CanvasJS.Chart("responseRate", {
            theme: "light2",
            exportEnabled: true,
            animationEnabled: true,
            title: {
                text: "Response Rates"
            },
            subtitles: [{
                text: "Here is the sample column chart of the ADL average response rate & Immediate average response rate of individual patients"
            }],

            toolTip: {
                shared: true
            },
            legend: {
                cursor: "pointer",
                itemclick: toggleDataSeries2
            },

            data: [{
                type: "column",
                name: "ADL Average Response Rate",
                legendText: "ADL Average Response Rate",
                ValueFormatString: "s",
                showInLegend: true,
                dataPoints: <?php echo json_encode($time_Response_Adl, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "column",
                name: "Immediate Average Response Rate",
                legendText: "Immediate Average Response Rate",
                ValueFormatString: "s",
                axisYType: "secondary",
                showInLegend: true,
                dataPoints: <?php echo json_encode($time_Response_Immediate, JSON_NUMERIC_CHECK); ?>
            }
            ]

            // Sample Data

            // data: [{
            //     type: "column",
            //     name: "Proven Oil Reserves (bn)",
            //     legendText: "Proven Oil Reserves",
            //     showInLegend: true,
            //     dataPoints: [
            //         { label: "Saudi", y: 266.21 },
            //         { label: "Venezuela", y: 302.25 },
            //         { label: "Iran", y: 157.20 },
            //         { label: "Iraq", y: 148.77 },
            //         { label: "Kuwait", y: 101.50 },
            //         { label: "UAE", y: 97.8 }
            //     ]
            // },
            // {
            //     type: "column",
            //     name: "Oil Production (million/day)",
            //     legendText: "Oil Production",
            //     axisYType: "secondary",
            //     showInLegend: true,
            //     dataPoints: [
            //         { label: "Saudi", y: 10.46 },
            //         { label: "Venezuela", y: 2.27 },
            //         { label: "Iran", y: 3.99 },
            //         { label: "Iraq", y: 4.45 },
            //         { label: "Kuwait", y: 2.92 },
            //         { label: "UAE", y: 3.1 }
            //     ]
            // }]
        });

        function toolTipFormatter(e) {
            var str = "";
            var str2;
            for (var i = 0; i < e.entries.length; i++) {
                var str1 = "<span style= \"color:" + e.entries[i].dataSeries.color + "\">" + e.entries[i].dataSeries.name + "</span>: <strong>" + e.entries[i].dataPoint.y + "</strong> <br/>";
                str = str.concat(str1);
            }
            str2 = "<strong>" + e.entries[0].dataPoint.label + "</strong> <br/>";
            return str2.concat(str);
        }

        function toggleDataSeries(e) {
            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }

        function toggleDataSeries2(e) {
            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }

    }

    chart1.render();
    chart2.render();
</script>

<div class="card shadow mb-3">
    <div class="card-body">
        <div id="immediateChart" style="height: 400px; width: 100%;"></div>
    </div>
</div>
<div class="card shadow mb-3">
    <div class="card-body">
        <div id="responseRate" style="height: 400px; width: 100%;"></div>
    </div>
</div>