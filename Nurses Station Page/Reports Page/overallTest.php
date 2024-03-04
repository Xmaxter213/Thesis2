<?php
require_once('../../dbConnection/connection.php');
//include('message.php');http://localhost/thesis2/Nurses%20Station%20Page/Nurses%20List/NursesList.php

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

if (isset($_GET['logout'])) {
    $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store

    date_default_timezone_set('Asia/Manila');

    $currentDateTime = date("Y-m-d H:i:s");

    // Insert into superAdminLogs
    $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time) VALUES ('$userName', 'Logout', '$currentDateTime')";
    $query_run_logs = mysqli_query($con, $sqlAddLogs);

    if ($query_run_logs) {
        session_destroy();
        unset($_SESSION);
        header("location: ../MainHospital/login_new.php");
    } else {
        echo 'Error inserting logs: ' . mysqli_error($con);
    }
}

if (!isset($_SESSION['userID'])) {
    header("location: ../../MainHospital/login_new.php");
} else {

    $status = $_SESSION['userStatus'];


    if ($status === 'Nurse') {
        header("location: ../../dumHomePage/index.php");
    }
}

//This is to make sure that deactivated accounts that are due for deletion are deleted

//This code runs after the NursesList.php page i think

// $sql = "SELECT * FROM arduino_Report";
// $result = mysqli_query($con, $sql);

// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $dataPoints = array(
//             array("label" => "Total Assistance", "y" => $row['number_of_Assistance']),
//             array("label" => "Assistance Given", "y" => $row['assistance_Given']),
//             array("label" => "Immediate Count", "y" => $row['immediate_Count']),
//             array("label" => "ADL Count", "y" => $row['adl_Count']),
//         );
//     }
// }

$immediate_Counts = 0;
$ADL_Counts = 0;
$total_Counts = 0;

// Query for immediate sum & ADL sum
$sql = "SELECT SUM(CASE WHEN assistance_Type = 'IMMEDIATE' THEN 1 ELSE 0 END) AS immediate_count, 
SUM(CASE WHEN assistance_Type = 'ADL' THEN 1 ELSE 0 END) AS adl_count FROM arduino_Reports";

$result = mysqli_query($con, $sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $ADL_Counts = $row["adl_count"];
        $immediate_Counts = $row["immediate_count"];
    }
}

$timeArray = array();
$timeArray2 = array();

// Query for dummy data of overall response rates
$sqlQuery2 = "SELECT 
adl_data.patient_ID,
adl_data.patient_Name,
adl_data.admission_Status,
adl_data.assistance_Status,
adl_data.patient_gloves_ID,
adl_data.activated,
adl_data.delete_at,
adl_data.patient_device_ID,
adl_data.total_ADL_Time,
immediate_data.total_Immediate_Time,
CASE 
    WHEN adl_data.total_ADL_Time IS NOT NULL THEN 'ADL'
    ELSE NULL
END AS adl_assistance_type,
CASE 
    WHEN immediate_data.total_Immediate_Time IS NOT NULL THEN 'IMMEDIATE'
    ELSE NULL
END AS immediate_assistance_type
FROM 
(SELECT 
    patient_List.patient_ID, 
    patient_List.patient_Name, 
    patient_List.admission_Status, 
    patient_List.assistance_Status, 
    patient_List.gloves_ID AS patient_gloves_ID, 
    patient_List.activated, 
    patient_List.delete_at, 
    arduino_Reports.device_ID AS patient_device_ID,
    SUM(arduino_Reports.response_Time) AS total_ADL_Time
FROM 
    patient_List 
INNER JOIN 
    arduino_Reports 
ON 
    patient_List.patient_ID = arduino_Reports.patient_ID 
WHERE 
    patient_List.admission_Status = 'Admitted' AND 
    arduino_Reports.assistance_Type = 'ADL'
GROUP BY 
    patient_List.patient_ID, arduino_Reports.device_ID) AS adl_data
LEFT JOIN 
(SELECT 
    patient_List.patient_ID AS patient_ID_immediate, 
    SUM(arduino_Reports.response_Time) AS total_Immediate_Time
FROM 
    patient_List 
INNER JOIN 
    arduino_Reports 
ON 
    arduino_Reports.patient_ID = patient_List.patient_ID 
WHERE 
    patient_List.admission_Status = 'Admitted' AND 
    arduino_Reports.assistance_Type = 'IMMEDIATE'
GROUP BY 
    patient_List.patient_ID) AS immediate_data
ON 
adl_data.patient_ID = immediate_data.patient_ID_immediate
";

$result2 = mysqli_query($con, $sqlQuery2);
if ($result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {

        $patientName = decryptthis($row2['patient_Name'], $key);
        // Getting the time in seconds
        // $timeFromDatabase = $row2['total_ADL_Time'];
        // if ($timeFromDatabase !== null) {
        //     $timeParts = explode(":", $timeFromDatabase);
        //     $totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];
        // } else {
        //     $totalSeconds = 0;
        // }

        // $referenceValue = 24 * 3600;
        // if ($referenceValue != 0) {
        //     $percentage = ($totalSeconds / $referenceValue) * 100;
        // } else {
        //     $percentage = 0;
        // }
        $label = $row2['adl_assistance_type'] == 'ADL' ? 'ADL Total Response Time' : 'Immediate Total Response Time';

        array_push($timeArray, array("y" => $row2['total_ADL_Time'], "label" => $patientName));

        // $timeFromDatabase2 = $row2['total_Immediate_Time'];
        // if ($timeFromDatabase2 !== null) {
        //     $timeParts2 = explode(":", $timeFromDatabase2);
        //     $totalSeconds2 = ($timeParts2[0] * 3600) + ($timeParts2[1] * 60) + $timeParts2[2];
        // } else {
        //     $totalSeconds2 = 0;
        // }

        // $referenceValue2 = 24 * 3600;
        // if ($referenceValue2 != 0) {
        //     $percentage2 = ($totalSeconds2 / $referenceValue2) * 100;
        // } else {
        //     $percentage2 = 0;
        // }
        $label2 = $row2['immediate_assistance_type'] == 'IMMEDIATE' ? 'Immediate Total Response Time' : 'ADL Total Response Time';

        array_push($timeArray2, array("y" => $row2['total_Immediate_Time'], "label" => $patientName));
    }
}

// echo '<script>setTimeout(function(){location.reload()}, 10000);</script>';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Helping Hand - Tables</title>

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
        window.onload = function () {

            var overallCommand = new CanvasJS.Chart("containerCommands", {
                theme: "light2",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Overall Request Count of all Patients"
                },
                subtitles: [{
                    fontSize: 18
                }],
                data: [{
                    type: "pie",
                    indexLabelFontSize: 18,
                    radius: 200,
                    indexLabel: "{label} = {y}",
                    yValueFormatString: "#",
                    click: explodePie,
                    dataPoints: [
                        { y: <?php echo json_encode($ADL_Counts, JSON_NUMERIC_CHECK); ?>, label: "ADL Count" },
                        { y: <?php echo json_encode($immediate_Counts, JSON_NUMERIC_CHECK); ?>, label: "Immediate Count" },
                    ]
                }]
            });
            overallCommand.render();

            var overallRates = new CanvasJS.Chart("containerRate", {
                theme: "light2",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Overall Response Time"
                },
                subtitles: [{
                    text: "This is the average time overall in seconds"
                }],
                data: [
                    {
                        type: "line",
                        startAngle: 25,
                        toolTipContent: "<b>{label}</b>: {y}s",
                        showInLegend: "true",
                        legendText: "ADL Total Response Time",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - {y}s",
                        dataPoints: <?php echo json_encode($timeArray, JSON_NUMERIC_CHECK); ?>
                    },
                    {
                        type: "line",
                        startAngle: 25,
                        toolTipContent: "<b>{label}</b>: {y}s",
                        showInLegend: "true",
                        legendText: "Immediate Total Response Time",
                        indexLabelFontSize: 16,
                        indexLabel: "{label} - {y}s",
                        color: "rgb(195,89,87)",
                        dataPoints: <?php echo json_encode($timeArray2, JSON_NUMERIC_CHECK); ?>
                    }]
            });
            overallRates.render();

            function explodePie(e) {
                for (var i = 0; i < e.dataSeries.dataPoints.length; i++) {
                    if (i !== e.dataPointIndex)
                        e.dataSeries.dataPoints[i].exploded = false;
                }
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
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php" style="background-color: rgb(28,35,47);">
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

            <!-- Divider -->
        </ul>
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow" style="background-color: rgb(28,35,47);">

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
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php

                                    ?>
                                </span>
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg">
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
                            <a href="./overallTest.php" class="btn btn-secondary active">Overall Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./individualTest.php" class="btn btn-secondary">Individual Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./criticalChart.php" class="btn btn-secondary">Critical Pulse Rate Reports</a>
                        </div>
                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div id="containerCommands" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div id="containerRate" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
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
        // For div refresh of the 2 charts

        // setInterval(function () {
        //     $('#containerCommands').load(location.href + ' #containerCommands');
        //     $('#containerRate').load(location.href + ' #containerRate');
        // }, 3000);
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