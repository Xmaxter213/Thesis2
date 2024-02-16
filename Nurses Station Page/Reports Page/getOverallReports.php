<?php

$immediate_Counts = 0;
$ADL_Counts = 0;
$avg_Immediate = 0;
$avg_ADL = 0;

$dataPoints = array();

// Query for immediate sum,  ADL sum & the average of ADL Response & Immediate Response
$sql = "SELECT SUM(ADL_Count) AS total_ADL_Count, AVG(ADL_Avg_Response) AS avg_ADL_Response, 
SUM(immediate_Count) AS total_Immediate_Count, AVG(immediate_Avg_Response) AS avg_Immediate_Response FROM arduino_Device_List";

$result = mysqli_query($con, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $ADL_Counts += $row["total_ADL_Count"];
        $immediate_Counts += $row["total_Immediate_Count"];
        $avg_ADL = $row["avg_ADL_Response"];
        $avg_Immediate = $row["avg_Immediate_Response"];
    }
    array(
        $dataPoints,
        array("label" => "Total Immediate Requests", "y" => $immediate_Counts),
        array("label" => "Total ADL Requests", "y" => $ADL_Counts)
    );
}

?>

<script>
    window.onload = function () {


        var chart3 = new CanvasJS.Chart("overall", {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: "Overall Reports"
            },
            data: [{
                type: "pie",
                indexLabel: "{y}",
                yValueFormatString: "#,##0.##",
                indexLabelPlacement: "inside",
                indexLabelFontColor: "#36454F",
                indexLabelFontSize: 18,
                indexLabelFontWeight: "bolder",
                showInLegend: true,
                legendText: "{label}",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart3.render();

    }
</script>

<!-- <h1>Overall Reports</h1> -->
<div class="card shadow mb-3">
    <div class="card-body">
        <div id="overall" style="height: 400px; width: 100%;"></div>
    </div>
</div>