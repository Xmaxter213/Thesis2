<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

// LOGOUT
if (isset($_GET['logout'])) {
    $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store

    date_default_timezone_set('Asia/Manila');

    $currentDateTime = date("Y-m-d H:i:s");

    // Insert into superAdminLogs
    $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Logout', '$currentDateTime', '$hospital_ID')";
    $query_run_logs = mysqli_query($con, $sqlAddLogs);

    if ($query_run_logs) {
        session_destroy();
        unset($_SESSION);
        header("location: ../MainHospital/login_new.php");
    } else {
        echo 'Error inserting logs: ' . mysqli_error($con);
    }
}

// USER LOGGED IN
if (!isset($_SESSION['userID'])) {
    header("location: ../../MainHospital/login_new.php");
} 
else 
{
    $status = $_SESSION['userStatus'];

    if ($status === 'Nurse') {
        header("location: ../../Nurse page/assistanceCard.php");
    }
    if ($status === 'Super Admin')
    {
        header("location: ../../Super Admin/index.php");
    }
}

// SELECTED HOSPITAL !EXPIRED
if(isset($_SESSION['selectedHospitalID']))
{
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $row = mysqli_fetch_assoc($query_run);
        $expirationDate = new DateTime($row['Expiration']);
        $currentDate = new DateTime();

        if($expirationDate < $currentDate)
        {
            header("location: ../../expiredPage/expired.php");
        }
    }
    else
    {
        echo "Error executing the query: " . mysqli_error($con);
    }

    
}

if (isset($_POST['search'])) {

    $selected_patient_ID = $_POST['selected_patient_ID'];

    $individualPatients = array();
    $adlCount = array();
    $battery_Percent = array();
    $pulse_Rate = array();
    $oxygen = array();

    // For Response time
    $time_Response_Adl = array();
    $time_Response_Immediate = array();


    // For Resolve time
    $time_Resolved_Adl = array();
    $time_Resolved_Immediate = array();

    // AND patient_List.patient_Name LIKE '$selected_patient_ID'

    $sql = "WITH PatientArduinoData AS (
        SELECT
            patient_List.patient_ID, 
            MAX(patient_List.patient_Name) AS patient_Name, 
            MAX(patient_List.room_Number) AS room_Number, 
            MAX(patient_List.birth_Date) AS birth_Date, 
            MAX(patient_List.reason_Admission) AS reason_Admission, 
            MAX(patient_List.admission_Status) AS admission_Status, 
            MAX(patient_List.assistance_Status) AS assistance_Status, 
            MAX(patient_List.gloves_ID) AS patient_gloves_ID, 
            MAX(patient_List.activated) AS activated, 
            MAX(patient_List.delete_at) AS delete_at,  
            MAX(arduino_Reports.date_Called) AS date_called,
            (SELECT pulse_rate FROM arduino_Reports WHERE device_ID = patient_List.gloves_ID ORDER BY date_Called DESC LIMIT 1) AS pulse_Rate,
            (SELECT oxygen_levels FROM arduino_Reports WHERE device_ID = patient_List.gloves_ID ORDER BY date_Called DESC LIMIT 1) AS oxygen_Levels,
            (SELECT battery_percent FROM arduino_Reports WHERE device_ID = patient_List.gloves_ID ORDER BY date_Called DESC LIMIT 1) AS battery_Percent
        FROM 
            patient_List 
        INNER JOIN 
            arduino_Reports ON patient_List.gloves_ID = arduino_Reports.device_ID
        WHERE 
            patient_List.admission_Status = 'Admitted'
            AND patient_List.patient_ID = $selected_patient_ID
            AND (patient_List.assistance_Status = 'On the way' OR patient_List.assistance_Status = 'Unassigned')
            AND patient_List.admission_Status = 'Admitted'
        GROUP BY
            patient_List.patient_ID
    ),
    ArduinoReportsData AS (
        SELECT 
            `patient_ID`, 
            COUNT(*) AS `total_calls`,
            SUM(CASE WHEN `assistance_Type` = 'ADL' THEN 1 ELSE 0 END) AS `ADL_calls`,
            SUM(CASE WHEN `assistance_Type` = 'IMMEDIATE' THEN 1 ELSE 0 END) AS `IMMEDIATE_calls`
        FROM 
            `arduino_Reports`
        WHERE
            `assistance_Type` IN ('ADL', 'IMMEDIATE')
        GROUP BY 
            `patient_ID`
    ),
    ReportsWithData AS (
        SELECT 
            `ID`, 
            `device_ID`, 
            `assistance_Type`, 
            `assistance_Given`, 
            `date_Called`, 
            TIMESTAMPDIFF(SECOND, `date_Called`, `Assitance_Finished`) AS `resolve_Time`, 
            `Nurse_Assigned_Status`, 
            TIMESTAMPDIFF(SECOND, `date_Called`, `Nurse_Assigned_Status`) AS `response_Time`, 
            `Assitance_Finished`,  
            `nurse_ID`, 
            `patient_ID` 
        FROM 
            `arduino_Reports`
    )
    SELECT 
        PAD.patient_ID,
        PAD.patient_Name,
        PAD.room_Number,
        PAD.birth_Date,
        PAD.reason_Admission,
        PAD.admission_Status,
        PAD.assistance_Status,
        PAD.patient_gloves_ID,
        PAD.activated,
        PAD.delete_at,
        PAD.pulse_Rate,
        PAD.oxygen_Levels,
        PAD.battery_Percent,
        MAX(ARD.total_calls) AS total_calls,
        MAX(ARD.ADL_calls) AS ADL_calls,
        MAX(ARD.IMMEDIATE_calls) AS IMMEDIATE_calls,
        MAX(CASE WHEN RWDA.assistance_Type = 'ADL' THEN RWDA.response_Time END) AS max_ADL_response_Time,
        MAX(CASE WHEN RWDA.assistance_Type = 'ADL' THEN RWDA.resolve_Time END) AS max_ADL_resolve_Time,
        MAX(CASE WHEN RWDB.assistance_Type = 'IMMEDIATE' THEN RWDB.response_Time END) AS max_IMMEDIATE_response_Time,
        MAX(CASE WHEN RWDB.assistance_Type = 'IMMEDIATE' THEN RWDB.resolve_Time END) AS max_IMMEDIATE_resolve_Time
    FROM 
        PatientArduinoData PAD
    LEFT JOIN 
        ArduinoReportsData ARD ON PAD.patient_ID = ARD.patient_ID
    LEFT JOIN
        ReportsWithData RWDA ON PAD.patient_gloves_ID = RWDA.device_ID AND RWDA.assistance_Type = 'ADL'
    LEFT JOIN
        ReportsWithData RWDB ON PAD.patient_gloves_ID = RWDB.device_ID AND RWDB.assistance_Type = 'IMMEDIATE'
    GROUP BY
        PAD.patient_ID,
        PAD.patient_Name,
        PAD.room_Number,
        PAD.birth_Date,
        PAD.reason_Admission,
        PAD.admission_Status,
        PAD.assistance_Status,
        PAD.patient_gloves_ID,
        PAD.activated,
        PAD.delete_at
    ";

    $result = mysqli_query($con, $sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $patientName = decryptthis($row["patient_Name"], $key);

            // Data Retrieval of Individual Reports Chart
            array_push($individualPatients, array("label" => $patientName, "y" => $row['IMMEDIATE_calls']));
            array_push($adlCount, array("label" => $patientName, "y" => $row['ADL_calls']));
            array_push($battery_Percent, array("label" => $patientName, "y" => $row['battery_Percent']));
            array_push($pulse_Rate, array("label" => $patientName, "y" => $row['pulse_Rate']));
            array_push($oxygen, array("label" => $patientName, "y" => $row['oxygen_Levels']));

            // Data Retrieval of Response Time $ Resolve Time Chart
            array_push($time_Response_Adl, array("label" => $patientName, "y" => $row['max_ADL_response_Time']));
            array_push($time_Response_Immediate, array("label" => $patientName, "y" => $row['max_IMMEDIATE_response_Time']));
            array_push($time_Resolved_Adl, array("label" => $patientName, "y" => $row['max_ADL_resolve_Time']));
            array_push($time_Resolved_Immediate, array("label" => $patientName, "y" => $row['max_IMMEDIATE_resolve_Time']));
        }
    }
}

$sqlQuery2 = "WITH PatientArduinoData AS (
    SELECT 
        patient_List.patient_ID, 
        MAX(patient_List.patient_Name) AS patient_Name
    FROM 
        patient_List 
    WHERE 
        patient_List.admission_Status = 'Admitted'
    GROUP BY
        patient_List.patient_ID
    ) SELECT * FROM PatientArduinoData";

$result2 = mysqli_query($con, $sqlQuery2);


// Getting the time in seconds
$timeFromDatabase = "00:00:23";
$timeParts = explode(":", $timeFromDatabase);
$totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];

// Check if the duration is less than a minute (60 seconds)
if ($totalSeconds < 60) {
    $timeOutput = "$totalSeconds seconds";
} else {
    // Calculate total minutes
    $totalMinutes = (int) ($totalSeconds / 60); // Explicitly cast to int

    // Getting the percentage value for response rates
    $referenceValue = 24 * 60; // Reference value in minutes
    $percentage = ($totalMinutes / $referenceValue) * 100;
    $percentage = number_format($percentage, 2);

    // Check if the duration is more than an hour
    if ($totalMinutes >= 60) {
        // Convert to hours and minutes
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        // Check if there are more than 1 hour
        if ($hours == 1) {
            $timeOutput = "$hours hour $minutes minutes";
        } else {
            $timeOutput = "$hours hours $minutes minutes";
        }
    } else {
        $timeOutput = "$totalMinutes minutes";
    }
}

echo '<script>setTimeout(function(){location.reload()}, 20000);</script>';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Individual Reports</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="PATH/dist/css/app.css" rel="stylesheet">

    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>

    <!-- For table sorting -->
    <link rel="stylesheet" href="tablesort.css">

    <!-- For modal  -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css"
        integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- for div refresh -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script>
        // $(document).ready(function () {
        //     setInterval(function () {
        //         $("body").load("./individualTest.php");
        //         body();
        //     }, 4000);
        // });
    </script>

    <script>
        window.onload = function () {
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
                {
                    type: "bar",
                    showInLegend: true,
                    indexLabel: "{y}",
                    indexLabelPlacement: "inside",
                    indexLabelFontColor: "#36454F",
                    indexLabelFontSize: 16,
                    indexLabelFontWeight: "bolder",
                    name: "Oxygen Level",
                    color: "rgb(247,150,71)",
                    dataPoints: <?php echo json_encode($oxygen, JSON_NUMERIC_CHECK); ?>
                },
                ]
            });
            chart1.render();

            var chart2 = new CanvasJS.Chart("responseRate", {
                theme: "light2",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Response Time"
                },
                axisY: {
                    title: "ADL Time (seconds)"
                },
                axisY2: {
                    title: "Immediate Time (seconds)"
                },
                subtitles: [{
                    text: "How long it took for the patient to receive assistance in seconds"
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
                    indexLabel: "{y} 'seconds'",
                    name: "ADL Response Time",
                    legendText: "ADL Response Time",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($time_Response_Adl, JSON_NUMERIC_CHECK); ?>
                },
                {
                    type: "column",
                    indexLabel: "{y} 'seconds'",
                    name: "Immediate Response Time",
                    legendText: "Immediate Response Time",
                    axisYType: "secondary",
                    showInLegend: true,
                    dataPoints: <?php echo json_encode($time_Response_Immediate, JSON_NUMERIC_CHECK); ?>
                }
                ]

            });
            chart2.render();

            var chart3 = new CanvasJS.Chart("resolveTime", {
                theme: "light2",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Resolve Time"
                },
                axisY: {
                    title: "ADL Resolved (seconds)"
                },
                axisY2: {
                    title: "Immediate Resolved (seconds)"
                },
                subtitles: [{
                    text: "How long it took for ADL & Immediate request to be resolved in seconds"
                }],

                toolTip: {
                    shared: true
                },
                legend: {
                    cursor: "pointer",
                    itemclick: toggleDataSeries3
                },

                data: [{
                    type: "column",
                    indexLabel: "{y} 'seconds'",
                    name: "ADL Resolved Time",
                    legendText: "ADL Resolved Time",
                    showInLegend: true,
                    color: "rgb(76,156,160)",
                    dataPoints: <?php echo json_encode($time_Resolved_Adl, JSON_NUMERIC_CHECK); ?> // TODO: Change into resolved time data
                },
                {
                    type: "column",
                    indexLabel: "{y} 'seconds'",
                    name: "Immediate Resolved Time",
                    legendText: "Immediate Resolved Time",
                    axisYType: "secondary",
                    showInLegend: true,
                    color: "rgb(223,121,112)",
                    dataPoints: <?php echo json_encode($time_Resolved_Immediate, JSON_NUMERIC_CHECK); ?> // TODO: Change into resolved time data
                }
                ]

            });
            chart3.render();

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
                chart1.render();
            }

            function toggleDataSeries2(e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                chart2.render();
            }

            function toggleDataSeries3(e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                chart3.render();
            }

        }
    </script>

    <style>
        .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: white;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        @keyframes bubbleAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .bubble {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            animation: bubbleAnimation 1s ease-out;
        }

        .dropdown-menu .row {
            display: flex;
            flex-wrap: wrap;
        }

        .dropdown-menu .col {
            flex: 1;
            margin: 0 5px;
        }
    </style>
    <!-- Bubble animation -->
    <script>
        function showBubbleAnimation(event) {
            const navLink = event.currentTarget;
            const rect = navLink.getBoundingClientRect();
            const bubble = document.createElement('span');
            bubble.classList.add('bubble');
            bubble.style.top = `${event.clientY - rect.top}px`;
            bubble.style.left = `${event.clientX - rect.left}px`;
            navLink.appendChild(bubble);
            setTimeout(() => {
                bubble.remove();
            }, 1000);
        }
    </script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar"
            style="background-color: rgb(17,24,39); font-family: 'Inter var', sans-serif;">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php"
                style="background-color: rgb(28,35,47);">
                <div class="fa-regular fa-hand"> Helping Hand </div>
            </a>

            <!-- Divider -->

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a onclick="showSnackbar('redirect to assistance page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Cards</span>
                </a>
            </li>

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link" href="../Patients List/PatientsList.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients List</span>
                </a>
            </li>

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link" href="./overallTest.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Logs/Logs.php">
                    <i class="bi bi-file-ruled"></i>
                    <span>Logs</span>
                </a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
                    style="background-color: rgb(28,35,47);">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>


                        <!-- Nav Item - User Information -->
                        <li class="nav-item">
                            <a class="nav-link" href="../Online_Help/reports_Guide.php" target="_blank">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    Need Help?
                                </span>
                                <i class="bi bi-info-circle"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php

                                    ?>
                                </span>
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg" style="filter: invert(1);">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?logout=true" data-toggle="modal"
                                    data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Reports</h1>
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <a href="./overallTest.php" class="btn btn-secondary">Overall Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./individualTest.php" class="btn btn-secondary active">Individual Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./criticalChart.php" class="btn btn-secondary">Critical Pulse Rate Reports</a>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    Search for Specific Patient
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form id="searchForm" method="POST" action="">
                                        <div class="input-group">
                                            <input type="text" class="form-control search-input"
                                                placeholder="Search Patient" name="selected_patient_ID"
                                                id="searchInput">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-outline-primary" name="search"
                                                    id="searchButton">Search</button>
                                            </div>
                                        </div>
                                        <div class="dropdown-divider"></div>
                                        <div class="row">
                                            <?php
                                            if ($result2->num_rows > 0) {
                                                while ($row2 = $result2->fetch_assoc()) {
                                                    $patientNames = decryptthis($row2["patient_Name"], $key);
                                                    $patientID = $row2["patient_ID"];
                                                    echo '<div class="col"><a class="dropdown-item" href="#" data-value="' . $patientID . '">' . $patientNames . '</a></div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>


                    </div>
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
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div id="resolveTime" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                    <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->
                    <!-- DataTales Example -->
                    <!-- <div class="card shadow mb-3">
                        <div class="card-body" id="refresh">
                            <div id="chartContainer" style="height: 470px; width: 100%;"></div>
                        </div>
                    </div> -->
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->


        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="?logout=true">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!-- Use a button to open the snackbar -->
    <!-- button onclick="showSnackbar('added')">Show Snackbar</button> -->

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>

    <!--GARBAGE -->
    <script>
        window.addEventListener('change', event => {
            showSnackbar('added');
        });
    </script>


    <!-- <script>
        function showSnackbar(msg) {
            // Get the snackbar DIV
            var x = document.getElementById("snackbar");

            //Change text
            if (msg.includes('add nurse')) {
                document.getElementById("snackbar").innerHTML = "Add nurse page opening...";
            } else if (msg.includes('edit nurse')) {
                document.getElementById("snackbar").innerHTML = "Opening edit page...";
            } else if (msg.includes('delete nurse')) {
                document.getElementById("snackbar").innerHTML = "Item is being deleted...";
            } else if (msg.includes('error')) {
                document.getElementById("snackbar").innerHTML = "Error.. Please try again.";
            } else if (msg.includes('redirect to nurses list page')) {
                document.getElementById("snackbar").innerHTML = "Refreshing nurses list page...";
            } else if (msg.includes('redirect to patients list page')) {
                document.getElementById("snackbar").innerHTML = "Opening patients list page...";
            }

            // Add the "show" class to DIV
            x.className = "show";

            // After 3 seconds, remove the show class from DIV
            setTimeout(function() {
                x.className = x.className.replace("show", "");
            }, 3000);
        }
    </script> -->
    <script src="../Table Sorting/tablesort.js"></script>
    <script>
        //Script for searching
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".search-input").forEach((inputField) => {
                const tableRows = inputField
                    .closest("table")
                    .querySelectorAll("tbody > tr");
                const headerCell = inputField.closest("th");
                const otherHeaderCells = headerCell.closest("tr").children;
                const columnIndex = Array.from(otherHeaderCells).indexOf(headerCell);
                const searchableCells = Array.from(tableRows).map(
                    (row) => row.querySelectorAll("td")[columnIndex]
                );

                inputField.addEventListener("input", () => {
                    const searchQuery = inputField.value.toLowerCase();

                    for (const tableCell of searchableCells) {
                        const row = tableCell.closest("tr");
                        const value = tableCell.textContent.toLowerCase().replace(",", "");

                        row.style.visibility = null;

                        if (value.search(searchQuery) === -1) {
                            row.style.visibility = "collapse";
                        }
                    }
                });
            });
        });
    </script>

    <script>
        // Javascript for enter key
        document.getElementById("searchInput").addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                document.getElementById("searchButton").click();
            }
        });
    </script>

    <script>
        // JavaScript for searching dropdown items
        $(document).ready(function () {
            $(".search-input").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $(".dropdown-item").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
            $(".dropdown-item").click(function () {
                var patientID = $(this).attr("data-value");
                $(".search-input").val(patientID); // Set the search input value to the selected patient ID
            });
        });
    </script>

    <!-- For charts -->
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

    <!-- For modal 
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
    <script src="PATH/dist/js/app.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
</body>

</html>