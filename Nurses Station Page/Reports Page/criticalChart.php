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
if (isset($_SESSION['selectedHospitalID'])) {
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

if (isset($_POST["daily"]) || isset($_POST["weekly"]) || isset($_POST["monthly"]) || isset($_POST["yearly"])) {
    $selectedRange = "";

    if (isset($_POST['daily'])) {
        $selectedRange = "arduino_Reports.date_Called >= now()";
    }
    if (isset($_POST['weekly'])) {
        $selectedRange = "arduino_Reports.date_Called > date_sub(now(), INTERVAL 1 week)";
    }
    if (isset($_POST['monthly'])) {
        $selectedRange = "arduino_Reports.date_Called > date_sub(now(), INTERVAL 1 month)";
    }
    if (isset($_POST['yearly'])) {
        $selectedRange = "arduino_Reports.date_Called > date_sub(now(), INTERVAL 1 year)";
    }

    $dataNames = array();

    $sql = "SELECT 
arduino_device_ID,
MAX(arduino_date_Called) AS arduino_date_Called,
MAX(nurse_ID) AS nurse_ID,
MAX(patient_ID) AS patient_ID,
MAX(patient_Name) AS patient_Name,
MAX(admission_Status) AS admission_Status,
MAX(patient_gloves_ID) AS patient_gloves_ID,
MAX(activated) AS activated,
MAX(delete_at) AS delete_at,
patient_device_ID,
MAX(pulse_Rate) AS pulse_Rate,
SUM(CASE WHEN pulse_Rate_Status = 'High Pulse Rate' AND patient_device_ID = 'patient_device_ID_1' THEN 1 ELSE 0 END) AS high_pulse_rate_count_patient_device_ID_1,
SUM(CASE WHEN pulse_Rate_Status = 'High Pulse Rate' AND patient_device_ID = 'patient_device_ID_2' THEN 1 ELSE 0 END) AS high_pulse_rate_count_patient_device_ID_2,
SUM(CASE WHEN pulse_Rate >= 100 THEN 1 ELSE 0 END) AS total_high_pulse_rate_count
FROM 
(
    SELECT 
        arduino_Reports.device_ID AS arduino_device_ID, 
        arduino_Reports.date_Called AS arduino_date_Called, 
        arduino_Reports.nurse_ID, 
        MAX(arduino_Reports.patient_ID) AS patient_ID,
        patient_List.patient_Name,  
        patient_List.admission_Status, 
        patient_List.gloves_ID AS patient_gloves_ID, 
        patient_List.activated, 
        patient_List.delete_at, 
        arduino_Reports.device_ID AS patient_device_ID,
        arduino_Reports.pulse_rate AS pulse_Rate,
        CASE 
            WHEN arduino_Reports.pulse_rate >= 100 THEN 'High Pulse Rate'
            ELSE 'Normal Pulse Rate'
        END AS pulse_Rate_Status
    FROM 
        arduino_Reports 
    INNER JOIN 
        patient_List 
    ON 
        arduino_Reports.patient_ID = patient_List.patient_ID 
    WHERE 
        patient_List.admission_Status = 'Admitted' AND $selectedRange
    GROUP BY 
        arduino_Reports.device_ID, 
        arduino_Reports.date_Called, 
        arduino_Reports.nurse_ID, 
        patient_List.patient_Name,  
        patient_List.admission_Status, 
        patient_List.gloves_ID, 
        patient_List.activated, 
        patient_List.delete_at, 
        arduino_Reports.device_ID, 
        arduino_Reports.pulse_rate
) AS subquery
GROUP BY 
arduino_device_ID, 
patient_device_ID";

    $result = mysqli_query($con, $sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patientName = decryptthis($row["patient_Name"], $key);

            array_push(
                $dataNames,
                array(
                    "label" => $patientName,
                    "y" => $row['total_high_pulse_rate_count'],
                    "additionalData" => $row['arduino_date_Called'],
                    "additionalData2" => $row['pulse_Rate']
                )
            );

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

    <title>Critical Pulse Rate Reports</title>

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
    <link  href="../Assistance Card Page/button.css" rel="stylesheet">

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

            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light2",
                animationEnabled: true,
                exportEnabled: true,
                title: {
                    text: "Critical Counts"
                },
                subtitles: [{
                    text: "How many times pulse rate were critical including date and time"
                }],
                axisY: {
                    includeZero: true
                },
                data: [{
                    type: "column",
                    indexLabelFontColor: "#5A5757",
                    indexLabelFontSize: 16,
                    indexLabelPlacement: "outside",
                    dataPoints: <?php echo json_encode($dataNames, JSON_NUMERIC_CHECK); ?>
                }],

                toolTip: {
                    contentFormatter: function (e) {
                        var content = "Critical Counts: " + e.entries[0].dataPoint.y;
                        if (e.entries[0].dataPoint.additionalData) {
                            content += "<br/>Date: " + e.entries[0].dataPoint.additionalData;
                        }
                        if (e.entries[0].dataPoint.additionalData2) {
                            content += "<br/>Pulse Rate: " + e.entries[0].dataPoint.additionalData2;
                        }
                        return content;
                    }
                }
            });

            chart.render();
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
                            <a class="nav-link" href="../Online_Help/reports_Guide.php">
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
                <div class="container-fluid ">
                    <br>
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Reports</h1>
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <a href="./overallTest.php" class="btn btn-secondary">Overall Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./individualTest.php" class="btn btn-secondary">Individual Reports</a>
                        </div>
                        <div class="col-auto">
                            <a href="./criticalChart.php" class="btn btn-secondary active">Critical Pulse Rate
                                Reports</a>
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
                            <div id="chartContainer" style="height: 400px; width: 100%;"></div>
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