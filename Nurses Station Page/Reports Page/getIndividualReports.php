<?php

$individualPatients = array();
$adlCount = array();
$pulse_Rate = array();
$battery_Percent = array();

// Divider of the 2 Stacked Bar Charts

$sql = "SELECT patient_ID, patient_Name, admission_Status, assistance_Status, gloves_ID, device_ID, ADL_Count, ADL_Avg_Response, immediate_Count,
immediate_Avg_Response, assistance_Given, pulse_Rate, battery_percent
FROM patient_List INNER JOIN arduino_Device_List ON patient_List.gloves_ID=arduino_Device_List.device_ID";
$result = mysqli_query($con, $sql);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patientName = decryptthis($row["patient_Name"], $key);

        // Data Retrieval of Immediate Chart
        array_push($individualPatients, array("label" => $patientName, "y" => $row['immediate_Count']));
        array_push($adlCount, array("label" => $patientName, "y" => $row['ADL_Count']));
        array_push($battery_Percent, array("label" => $patientName, "y" => $row['battery_percent']));
        array_push($pulse_Rate, array("label" => $patientName, "y" => $row['pulse_Rate']));

        // Data Retrieval of ADL Chart
        // array_push($individualPatients2, array("label" => $patientName, "y" => $row['ADL_Count']));
        // array_push($t21, array("label" => $patientName, "y" => $row['battery_percent']));
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
                name: "Immediate Count",
                color: "rgb(223,121,112)",
                dataPoints: <?php echo json_encode($individualPatients, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                name: "ADL Count",
                color: "rgb(119,160,51)",
                dataPoints: <?php echo json_encode($adlCount, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                name: "Battery Percentage",
                color: "rgb(128,100,161)",
                dataPoints: <?php echo json_encode($battery_Percent, JSON_NUMERIC_CHECK); ?>
            },
            {
                type: "bar",
                showInLegend: true,
                name: "Pulse Rate",
                color: "rgb(74,172,197)",
                dataPoints: <?php echo json_encode($pulse_Rate, JSON_NUMERIC_CHECK); ?>
            },
            ]
        });

        var chart2 = {
            theme: "light2",
            title: {
                text: "Response Rates"
            },
            subtitles: [{
                text: "Here is the sample pie chart of the ADL average response rate & Immediate average response rate of individual patients"
            }],
            toolTip: {
                
            },
            animationEnabled: true,
            data: [{
                type: "pie",
                startAngle: 40,
                toolTipContent: "<b>{label}</b>: {y}%",
                showInLegend: "true",
                legendText: "{label}",
                indexLabelFontSize: 16,
                indexLabel: "{label} - {y}%",
                dataPoints: [
                    { y: 48.36, label: "Patient 1" },
                    { y: 26.85, label: "Patient 2" },
                    { y: 37.49, label: "Patient 3" },
                ]
            }]
        };

        $("#responseRate").CanvasJSChart(chart2);

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

        chart1.render();
        chart2.render();
    }
</script>

<div class="card shadow mb-3">
    <div class="card-body">
        <div id="immediateChart" style="height: 400px; width: 100%;"></div>
    </div>
</div>
<div class="card shadow mb-3">
    <div class="card-body">
        <div id="responseRate" style="height: 450px; width: 100%;"></div>
    </div>
</div>