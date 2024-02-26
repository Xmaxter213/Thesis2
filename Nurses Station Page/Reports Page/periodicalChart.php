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
    exit;
} else {

    $status = $_SESSION['userStatus'];


    if ($status === 'Nurse') {
        header("location: ../../dumHomePage/index.php");
        exit;
    }
}

$dataNames = array();

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

        // Getting the time in seconds
        $timeFromDatabase = "01:40:56";
        $timeParts = explode(":", $timeFromDatabase);
        $totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];

        array_push($dataNames, $patientName);

    }
}

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

    <!-- Put charts here -->

    <script>
        window.onload = function () {

            var dailyChart = new CanvasJS.Chart("containerDaily", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Daily Response Time"
                },
                subtitles: [{
                    text: "Total of how long it took for the patient to receive assistance in a day"
                }],
                axisY: {
                    title: "Response Time (seconds)"
                },
                data: [{
                    type: "line",
                    indexLabel: "{y} 'seconds'",
                    indexLabelFontSize: 16,
                    dataPoints: [{
                        y: 7.45,
                        label: "Tony, Stark"
                    }, // Representing 0.45 seconds
                    {
                        y: 4.41,
                        label: "Tobey, Maguire"
                    }, // Representing 0.414 seconds
                    {
                        y: 8.52,
                        label: "Tom, Holland"
                    }, // Representing 0.52 seconds
                    {
                        y: 5.46,
                        label: "Jonas, Bohol"
                    }, // Representing 0.46 seconds
                    ]
                }]
            });

            var weeklyChart = new CanvasJS.Chart("containerWeekly", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Weekly Response Time"
                },
                subtitles: [{
                    text: "Total of how long it took for the patient to receive assistance in a week"
                }],
                axisY: {
                    title: "Response Time (seconds)"
                },
                data: [{
                    type: "line",
                    indexLabel: "{y} 'seconds'",
                    dataPoints: [{
                        y: 23.45,
                        label: "Tony, Stark"
                    }, // Representing 0.45 seconds
                    {
                        y: 45.41,
                        label: "Tobey, Maguire"
                    }, // Representing 0.414 seconds
                    {
                        y: 87.52,
                        label: "Tom, Holland"
                    }, // Representing 0.52 seconds
                    {
                        y: 39.46,
                        label: "Jonas, Bohol"
                    }, // Representing 0.46 seconds
                    ]
                }]
            });

            var monthlyChart = new CanvasJS.Chart("containerMonthly", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Monthly Response Time"
                },
                subtitles: [{
                    text: "Total of how long it took for the patient to receive assistance for a month"
                }],
                axisY: {
                    title: "Response Time (seconds)"
                },
                data: [{
                    type: "line",
                    indexLabel: "{y} 'seconds'",
                    dataPoints: [{
                        y: 250.45,
                        label: "Tony, Stark"
                    }, // Representing 0.45 seconds
                    {
                        y: 189.41,
                        label: "Tobey, Maguire"
                    }, // Representing 0.414 seconds
                    {
                        y: 210.52,
                        label: "Tom, Holland"
                    }, // Representing 0.52 seconds
                    {
                        y: 198.46,
                        label: "Jonas, Bohol"
                    }, // Representing 0.46 seconds
                    ]
                }]
            });

            var annuallyChart = new CanvasJS.Chart("containerAnnually", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Annually Response Time"
                },
                subtitles: [{
                    text: "Total of how long it took for the patient to receive assistance for a year"
                }],
                axisY: {
                    title: "Response Time (seconds)"
                },
                data: [{
                    type: "line",
                    indexLabel: "{y} 'seconds'",
                    dataPoints: [{
                        y: 4420.81,
                        label: "Tony, Stark"
                    }, // Representing aggregated response time for John over the year
                    {
                        y: 9065.304,
                        label: "Tobey, Maguire"
                    }, // Representing aggregated response time for Alice over the year
                    {
                        y: 3946.4,
                        label: "Tom, Holland"
                    }, // Representing aggregated response time for Bob over the year
                    {
                        y: 6418.56,
                        label: "Jonas, Bohol"
                    }, // Representing aggregated response time for Emily over the year
                    ]
                }]
            });

            dailyChart.render();
            weeklyChart.render();
            monthlyChart.render();
            annuallyChart.render();

        }
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
                    <br>
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
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-daily-tab" data-toggle="tab"
                                        href="#nav-daily" role="tab" aria-controls="nav-daily"
                                        aria-selected="true">Daily</a>
                                    <a class="nav-item nav-link" id="nav-weekly-tab" data-toggle="tab"
                                        href="#nav-weekly" role="tab" aria-controls="nav-weekly"
                                        aria-selected="false">Weekly</a>
                                    <a class="nav-item nav-link" id="nav-monthly-tab" data-toggle="tab"
                                        href="#nav-monthly" role="tab" aria-controls="nav-monthly"
                                        aria-selected="false">Monthly</a>
                                    <a class="nav-item nav-link" id="nav-annually-tab" data-toggle="tab"
                                        href="#nav-annually" role="tab" aria-controls="nav-annually"
                                        aria-selected="false">Annually</a>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-daily" role="tabpanel"
                                    aria-labelledby="nav-daily-tab">
                                    <!-- Tab Content -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav-overall-reports" role="tabpanel">
                                            <div id="containerDaily" style="height: 400px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-weekly" role="tabpanel"
                                    aria-labelledby="nav-weekly-tab">
                                    <!-- Tab Content -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav-overall-reports" role="tabpanel">
                                            <div id="containerWeekly" style="height: 400px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-monthly" role="tabpanel"
                                    aria-labelledby="nav-monthly-tab">
                                    <!-- Tab Content -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav-overall-reports" role="tabpanel">
                                            <div id="containerMonthly" style="height: 400px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-annually" role="tabpanel"
                                    aria-labelledby="nav-annually-tab">
                                    <!-- Tab Content -->
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav-overall-reports" role="tabpanel">
                                            <div id="containerAnnually" style="height: 400px; width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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