<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

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

$individualPatients = array();
$adlCount = array();
$pulse_Rate = array();
$battery_Percent = array();

$time_Response_Adl = array();
$time_Response_Immediate = array();

$sql = "SELECT patient_List.patient_ID, patient_List.patient_Name, patient_List.room_Number, patient_List.birth_Date, patient_List.reason_Admission, 
patient_List.admission_Status, patient_List.nurse_ID, patient_List.assistance_Status, patient_List.gloves_ID
AS patient_gloves_ID, patient_List.activated, patient_List.delete_at, arduino_Device_List.device_ID AS patient_device_ID,
arduino_Device_List.ADL_Count, arduino_Device_List.ADL_Avg_Response, arduino_Device_List.immediate_Count, arduino_Device_List.immediate_Avg_Response,
arduino_Device_List.assistance_Given, arduino_Device_List.nurses_In_Charge, arduino_Device_List.pulse_Rate, arduino_Device_List.battery_percent, 
arduino_Device_List.date_called FROM patient_List INNER JOIN arduino_Device_List ON patient_List.gloves_ID = arduino_Device_List.device_ID WHERE 
patient_List.admission_Status = 'Admitted'";
$result = mysqli_query($con, $sql);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patientName = decryptthis($row["patient_Name"], $key);

        // Data Retrieval of Individual Reports Chart
        array_push($individualPatients, array("label" => $patientName, "y" => $row['immediate_Count']));
        array_push($adlCount, array("label" => $patientName, "y" => $row['ADL_Count']));
        array_push($battery_Percent, array("label" => $patientName, "y" => $row['battery_percent']));
        array_push($pulse_Rate, array("label" => $patientName, "y" => $row['pulse_Rate']));

        // Getting the time in seconds for ADL
        $timeFromDatabase = $row['ADL_Avg_Response'];
        $timeParts = explode(":", $timeFromDatabase);
        $totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];

        // Getting the percentage value for ADL
        // $referenceValue = 24 * 3600;
        // $percentage = ($totalSeconds / $referenceValue) * 100;
        // $percentage = number_format($percentage, 2);

        // Getting the time in seconds for Immediate
        $timeFromDatabase2 = $row['immediate_Avg_Response'];
        $timeParts2 = explode(":", $timeFromDatabase2);
        $totalSeconds2 = ($timeParts2[0] * 3600) + ($timeParts2[1] * 60) + $timeParts2[2];

        // Getting the percentage value for Immediate
        // $referenceValue2 = 24 * 3600;
        // $percentage2 = ($totalSeconds2 / $referenceValue2) * 100;
        // $percentage2 = number_format($percentage2, 2);


        // Data Retrieval of Response Time Chart
        array_push($time_Response_Adl, array("label" => $patientName, "y" => $totalSeconds));
        array_push($time_Response_Immediate, array("label" => $patientName, "y" => $totalSeconds2));

    }
}

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
        // $('#nav-tab a[href="#nav-overall-reports"]').tab('show')

        // $("#nav-tab a").on("click", function(e) {
        //     e.preventDefault();
        //     $(this).tab('show');
        // });
        // console.log($('a[data-toggle="tab"]'), "report.php");
        // $(document).ready(function () {
        //     setInterval(function () {
        //         $("#refresh").load(".card-body");
        //         refresh();
        //     }, 1000);
        // });
    </script>

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
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="fa-regular fa-hand"> Helping Hand </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">




            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a onclick="showSnackbar('redirect to assistance page')" class="nav-link"
                    href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Cards</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page')" class="nav-link"
                    href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link"
                    href="../Patients List/PatientsList.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients List</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="./overallTest.php">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>Reports</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page')" class="nav-link" href="../Logs/Logs.php">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>Logs</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

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
                                <button class="btn btn-primary" type="button">
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
                    <a href="./overallTest.php"><button type="button" class="btn btn-primary">Overall
                            Reports</button></a>
                    <a href="./individualTest.php"><button type="button" class="btn btn-primary">Individual
                            Reports</button></a>
                    <a href="./periodicalChart.php"><button type="button" class="btn btn-primary">Periodical
                            Reports</button></a>

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