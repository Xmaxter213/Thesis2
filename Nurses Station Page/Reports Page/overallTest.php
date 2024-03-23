<?php
require_once ('../../dbConnection/connection.php');
require_once ('./getWard.php');
//include('message.php');http://localhost/thesis2/Nurses%20Station%20Page/Nurses%20List/NursesList.php

//The functions for the encryption
include ('../../dbConnection/AES encryption.php');

// LOGOUT
if (isset ($_GET['logout'])) {
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
if (!isset ($_SESSION['userID'])) {
    header("location: ../../MainHospital/login_new.php");
} else {
    $status = $_SESSION['userStatus'];

    if ($status === 'Nurse') {
        header("location: ../../Nurse Page/Assistance Card Page/assistanceCard.php");
    }
    if ($status === 'Super Admin') {
        header("location: ../../Super Admin/index.php");
    }
}

// SELECTED HOSPITAL !EXPIRED
if (isset ($_SESSION['selectedHospitalID'])) {
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
        $expirationDate = new DateTime($row['Expiration']);
        $currentDate = new DateTime();

        if ($expirationDate < $currentDate) {
            header("location: ../../expiredPage/expired.php");
        }
    } else {
        echo "Error executing the query: " . mysqli_error($con);
    }
}

$name = isset ($_SESSION['idNUM']) ? $_SESSION['idNUM'] : null;
$assignedWard = $_SESSION['assignedWard'];

if (isset ($_POST["daily"]) || isset ($_POST["weekly"]) || isset ($_POST["monthly"]) || isset ($_POST["yearly"])) {
    $selectedRange = "";

    if (isset ($_POST['daily'])) {
        $selectedRange = "ar.`date_Called` >= now()";
    }
    if (isset ($_POST['weekly'])) {
        $selectedRange = "ar.`date_Called` > date_sub(now(), INTERVAL 1 week)";
    }
    if (isset ($_POST['monthly'])) {
        $selectedRange = "ar.`date_Called` > date_sub(now(), INTERVAL 1 month)";
    }
    if (isset ($_POST['yearly'])) {
        $selectedRange = "ar.`date_Called` > date_sub(now(), INTERVAL 1 year)";
    }

    $immediate_Counts = 0;
    $ADL_Counts = 0;
    $firstProcessedRequests = false;

    $graveyardCounts = 0;
    $nightCounts = 0;
    $morningCounts = 0;
    $firstProcessedShifts = false;

    $firstTotal = false;

    // Query for overall response rates
    $sql = "WITH arduino_sums AS (
        SELECT 
            SUM(CASE WHEN assistance_Type = 'IMMEDIATE' THEN 1 ELSE 0 END) AS immediate_count,
            SUM(CASE WHEN assistance_Type = 'ADL' THEN 1 ELSE 0 END) AS adl_count,
            SUM(CASE WHEN assistance_Type = 'ADL' THEN TIMESTAMPDIFF(SECOND, `date_Called`, `Nurse_Assigned_Status`) ELSE 0 END) AS total_time_adl,
            SUM(CASE WHEN assistance_Type = 'IMMEDIATE' THEN TIMESTAMPDIFF(SECOND, `date_Called`, `Nurse_Assigned_Status`) ELSE 0 END) AS total_time_immediate
        FROM 
            arduino_Reports
    ),
    shift_sums AS (
        SELECT 
            SUM(CASE WHEN `shift_Schedule` = 'Graveyard Shift' THEN 1 ELSE 0 END) AS `Total_Graveyard_Shift_Count`,
            SUM(CASE WHEN `shift_Schedule` = 'Morning Shift' THEN 1 ELSE 0 END) AS `Total_Morning_Shift_Count`,
            SUM(CASE WHEN `shift_Schedule` = 'Night Shift' THEN 1 ELSE 0 END) AS `Total_Night_Shift_Count`
        FROM 
            staff_List
    )
    SELECT 
        pl.`patient_ID`, 
        pl.`patient_Name`,
        SUM(CASE WHEN ar.`assistance_Type` = 'ADL' THEN TIMESTAMPDIFF(SECOND, ar.`date_Called`, ar.`Nurse_Assigned_Status`) ELSE 0 END) AS `Total_Time_ADL`,
        SUM(CASE WHEN ar.`assistance_Type` = 'IMMEDIATE' THEN TIMESTAMPDIFF(SECOND, ar.`date_Called`, ar.`Nurse_Assigned_Status`) ELSE 0 END) AS `Total_Time_IMMEDIATE`,
        (SELECT immediate_count FROM arduino_sums) AS immediate_count,
        (SELECT adl_count FROM arduino_sums) AS adl_count,
        (SELECT Total_Graveyard_Shift_Count FROM shift_sums) AS Total_Graveyard_Shift_Count,
        (SELECT Total_Morning_Shift_Count FROM shift_sums) AS Total_Morning_Shift_Count,
        (SELECT Total_Night_Shift_Count FROM shift_sums) AS Total_Night_Shift_Count,
        (SELECT total_time_adl FROM arduino_sums) AS total_time_adl_all,
        (SELECT total_time_immediate FROM arduino_sums) AS total_time_immediate_all
    FROM 
        `arduino_Reports` AS ar
    INNER JOIN 
        `patient_List` AS pl ON ar.`patient_ID` = pl.`patient_ID`
    WHERE $selectedRange
    GROUP BY
        pl.`patient_ID`, 
        pl.`patient_Name`    
";

    // Add WHERE clause to calculate for the $nurse_Assigned_Ward

    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            // For the overall # of patient requests
            if (!$firstProcessedRequests) {
                $ADL_Counts = $row["adl_count"];
                $immediate_Counts = $row["immediate_count"];
                $firstProcessedRequests = true;
            }
            // For # of nurses on specific shift schedules
            if (!$firstProcessedShifts) {
                $graveyardCounts = $row["Total_Graveyard_Shift_Count"];
                $morningCounts = $row["Total_Morning_Shift_Count"];
                $nightCounts = $row["Total_Night_Shift_Count"];
                $firstProcessedShifts = true;
            }

            $patientName = decryptthis($row['patient_Name'], $key);

            if (!$firstTotal) {
                $adlPercent = $row['total_time_adl_all'];
                $immediatePercent = $row['total_time_immediate_all'];
    
                $referenceValue = 24 * 60;
                // For ADL response rate
                $adlPercentage = ($adlPercent / $referenceValue) * 100;
                $adlPercentage = number_format($adlPercentage, 2);
                // For Immediate response rate
                $immediatePercentage = ($immediatePercent / $referenceValue) * 100;
                $immediatePercentage = number_format($immediatePercentage, 2);

                $firstTotal = true;
            }
        }
    }
}



// echo '<script>setTimeout(function(){location.reload()}, 20000);</script>';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Overall Reports</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./styles/overallReports.css">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="PATH/dist/css/app.css" rel="stylesheet">
    <link href="../Assistance Card Page/button.css" rel="stylesheet">

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
                    dataPoints: [{
                        y: <?php echo json_encode($ADL_Counts, JSON_NUMERIC_CHECK); ?>,
                        label: "ADL Count"
                    },
                    {
                        y: <?php echo json_encode($immediate_Counts, JSON_NUMERIC_CHECK); ?>,
                        label: "Immediate Count"
                    },
                    ]
                }]
            });
            overallCommand.render();


            var overallShifts = new CanvasJS.Chart("containerShifts", {
                theme: "light2",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Shift Nurse Totals"
                },
                subtitles: [{
                    text: "Number of nurses for each shift schedule"
                }],
                legend: {
                    cursor: "pointer",
                    itemclick: explodePie
                },
                data: [{
                    type: "pie",
                    showInLegend: true,
                    toolTipContent: "{name}: <strong>{y}</strong>",
                    indexLabel: "{name} - {y}",
                    dataPoints: [{
                        y: <?php echo json_encode($graveyardCounts, JSON_NUMERIC_CHECK); ?>,
                        name: "Graveyard Shifts"
                    },
                    {
                        y: <?php echo json_encode($morningCounts, JSON_NUMERIC_CHECK); ?>,
                        name: "Morning Shifts"
                    },
                    {
                        y: <?php echo json_encode($nightCounts, JSON_NUMERIC_CHECK); ?>,
                        name: "Night Shifts"
                    },
                    ]
                }]
            });
            overallShifts.render();

            var overallRates = new CanvasJS.Chart("containerRate", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "Overall Response Time"
                },
                subtitles: [{
                    text: "Total average time in seconds"
                }],
                axisX: {
                    interval: 1
                },
                axisY2: {
                    interlacedColor: "rgba(1,77,101,.2)",
                    gridColor: "rgba(1,77,101,.1)",
                },
                data: [{
                    type: "bar",
                    color: "rgb(108,117,125)",
                    axisYType: "secondary",
                    dataPoints: [
                        { y: <?php echo json_encode($adlPercentage, JSON_NUMERIC_CHECK); ?>, label: "ADL Average", color: "rgb(109,120,173)" },
                        { y: <?php echo json_encode($immediatePercentage, JSON_NUMERIC_CHECK); ?>, label: "Immediate Average", color: "rgb(223,121,112)" },
                    ]
                }]
            });
            overallRates.render();
            
            function explodePie(e) {
                for (var i = 0; i < e.dataSeries.dataPoints.length; i++) {
                    if (i !== e.dataPointIndex)
                        e.dataSeries.dataPoints[i].exploded = false;
                }
            }

            function explodePie(e) {
                if (typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
                } else {
                    e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
                }
                e.chart.render();

            }

        }
    </script>
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
                <a onclick="showSnackbar('redirect to assistance page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Page</span>
                </a>
            </li>

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Patients List/PatientsList.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients List</span>
                </a>
            </li>

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Reports Page/overallTest.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Logs/Logs.php">
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
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
                    style="background-color: rgb(28,35,47);">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
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
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg"
                                    style="filter: invert(1);">
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
                        <div class="dropdown col-auto">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Choose Date
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <form id="dateSearch" method="POST">
                                    <button class="dropdown-item" type="submit" name="daily">Daily</button>
                                    <button class="dropdown-item" type="submit" name="weekly">Weekly</button>
                                    <button class="dropdown-item" type="submit" name="monthly">Monthly</button>
                                    <button class="dropdown-item" type="submit" name="yearly">Yearly</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div id="containerCommands" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div id="containerShifts" style="height: 400px; width: 100%;"></div>
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
        // JavaScript to handle click event on dropdown items
        document.querySelectorAll('.ward-item').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault(); // Prevent default link behavior
                const wardValue = event.target.getAttribute('data-value'); // Get the ward value
                document.getElementById('searchInput').value = wardValue; // Set the search input value
            });
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