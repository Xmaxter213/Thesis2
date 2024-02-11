<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("location: ../../MainHospital/login_new.php");
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

$individualPatients = array();

$odi = array();

$t20 = array();

// Divider of the 2 Stacked Bar Charts

$individualPatients2 = array();

$odi2 = array();

$t21 = array();

$sql = "SELECT patient_ID, patient_Name, admission_Status, assistance_Status, gloves_ID, device_ID, ADL_Count, ADL_Avg_Response, immediate_Count,
immediate_Avg_Response, assistance_Given, pulse_Rate, battery_percent
FROM patient_List INNER JOIN arduino_Device_List ON patient_List.gloves_ID=arduino_Device_List.device_ID";
$result = mysqli_query($con, $sql);


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patientName = decryptthis($row["patient_Name"], $key);

        // Data Retrieval of Immediate Chart
        array_push($individualPatients, array("label" => $patientName, "y" => $row['immediate_Count']));
        array_push($odi, array("label" => $patientName, "y" => $row['pulse_Rate']));
        array_push($t20, array("label" => $patientName, "y" => $row['battery_percent']));

        // Data Retrieval of ADL Chart
        array_push($individualPatients2, array("label" => $patientName, "y" => $row['ADL_Count']));
        array_push($t21, array("label" => $patientName, "y" => $row['battery_percent']));
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

    <!-- For modal 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    -->

    <!-- <script>
        $(document).ready(function() {
            setInterval(function() {
                $("#chartContainer").load("./reports.php");
                refresh();
            }, 1000);
        });
    </script> -->

    <!-- Chart for Immediate Help Requests -->
    <script>
        window.onload = function () {

            var chart1 = new CanvasJS.Chart("chartContainer1", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "Immediate Reports"
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
                data: [
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Immediate Count",
                        color: "rgb(223,121,112)",
                        dataPoints: <?php echo json_encode($individualPatients, JSON_NUMERIC_CHECK); ?>
                    },
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Pulse Rate",
                        color: "rgb(90,145,209)",
                        dataPoints: <?php echo json_encode($odi, JSON_NUMERIC_CHECK); ?>
                    },
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Battery Percentage",
                        color: "rgb(155,187,88)",
                        dataPoints: <?php echo json_encode($t20, JSON_NUMERIC_CHECK); ?>
                    },
                ]
            });

            // Divider of 2 charts

            var chart2 = new CanvasJS.Chart("chartContainer2", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "ADL Reports"
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
                data: [
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "ADL Count",
                        color: "rgb(90,145,209)",
                        dataPoints: <?php echo json_encode($individualPatients2, JSON_NUMERIC_CHECK); ?>
                    },
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Battery Percentage",
                        color: "rgb(155,187,88)",
                        dataPoints: <?php echo json_encode($t21, JSON_NUMERIC_CHECK); ?>
                    },
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
                }
                else {
                    e.dataSeries.visible = true;
                }
                chart.render();
            }
            chart1.render();
            chart2.render();

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
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link"
                    href="../Reports Page/reports.php">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>Reports</span></a>
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
                    <a href="reports.php" class="btn btn-primary float-end">Overall Reports</a>
                    <a href="./individualReports.php" class="btn btn-primary float-end active">Individual Reports</a>
                    <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p> -->

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <?php
                            $count = 0;
                            $sql = "SELECT * FROM arduino_Report";
                            $result = mysqli_query($con, $sql);

                            //This is for pagination
                            // define how many results you want per page
                            $results_per_page = 3;
                            $number_of_results = mysqli_num_rows($result);

                            // determine number of total pages available
                            $number_of_pages = ceil($number_of_results / $results_per_page);

                            // determine which page number visitor is currently on
                            if (!isset($_GET['page'])) {
                                $page = 1;
                            } else {
                                $page = $_GET['page'];
                            }

                            // determine the sql LIMIT starting number for the results on the displaying page
                            $this_page_first_result = ($page - 1) * $results_per_page;

                            // retrieve selected results from database and display them on page
                            $sql = 'SELECT * FROM arduino_Report LIMIT ' . $this_page_first_result . ',' . $results_per_page;
                            $result = mysqli_query($con, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                echo "";
                                ?>
                                <table class="table table-bordered table-sortable" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_array($result)) {
                                            $count = $count + 1;
                                            ?>
                                            <div id="chartContainer1" style="height: 470px; width: 100%;"></div>

                                </div>
                                <?php
                                        }
                            } else {
                                echo "No Record Found";
                            }
                            ?>
                        </tbody>
                        </table>
                        <script>
                            src = "../Table Sorting/searchTable.js"
                        </script>
                    </div>

                    <!-- Chart Divider -->

                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <?php
                            $count = 0;
                            $sql = "SELECT * FROM arduino_Report";
                            $result = mysqli_query($con, $sql);

                            //This is for pagination
                            // define how many results you want per page
                            $results_per_page = 3;
                            $number_of_results = mysqli_num_rows($result);

                            // determine number of total pages available
                            $number_of_pages = ceil($number_of_results / $results_per_page);

                            // determine which page number visitor is currently on
                            if (!isset($_GET['page'])) {
                                $page = 1;
                            } else {
                                $page = $_GET['page'];
                            }

                            // determine the sql LIMIT starting number for the results on the displaying page
                            $this_page_first_result = ($page - 1) * $results_per_page;

                            // retrieve selected results from database and display them on page
                            $sql = 'SELECT * FROM arduino_Report LIMIT ' . $this_page_first_result . ',' . $results_per_page;
                            $result = mysqli_query($con, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                echo "";
                                ?>
                                <table class="table table-bordered table-sortable" id="dataTable" width="100%"
                                    cellspacing="0">
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_array($result)) {
                                            $count = $count + 1;
                                            ?>
                                            <div id="chartContainer2" style="height: 470px; width: 100%;"></div>

                                </div>
                                <?php
                                        }
                            } else {
                                echo "No Record Found";
                            }
                            ?>
                        </tbody>
                        </table>
                        <script>
                            src = "../Table Sorting/searchTable.js"
                        </script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
    <script src="PATH/dist/js/app.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</body>

</html>