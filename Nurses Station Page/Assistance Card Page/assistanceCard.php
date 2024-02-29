<?php
#require_once('../dbConnection/connection.php');
require_once('../../dbConnection/connection.php');


if (isset($_GET['logout'])) {
    $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store

        date_default_timezone_set('Asia/Manila');

        $currentDateTime = date("Y-m-d H:i:s");

        // Insert into superAdminLogs
        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time) VALUES ('$userName', 'Logout', '$currentDateTime')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);

        if ($query_run_logs) 
        {
            session_destroy();
            unset($_SESSION);
            header("location: ../MainHospital/login_new.php");
        } 
        else 
        {
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
require_once('../../dbConnection/connection2.php');
    $hospitalName = "Helping Hand";
    $query = "SELECT hospitalStatus FROM Hospital_Table WHERE hospitalName = ?";
    $stmt = mysqli_prepare($con2, $query);
    mysqli_stmt_bind_param($stmt, "s", $hospitalName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hospitalStatus);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Check if the hospital status is 'Active'
    if ($hospitalStatus != 'Active') {
        header("location: ../../expired.php");
    }


    $verpass = $_SESSION['verifyPass'];
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
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>

    <!-- For table sorting -->
    <link rel="stylesheet" href="../Table Sorting/tablesort.css">

    <!-- For table sorting -->
    <link rel="stylesheet" href="../Table Sorting/tablesort.css">

    <!-- For Modal -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- for div refresh -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // setInterval(function() {
            //     $("#refresh").load("assistanceCards.php");
            //     refresh();
            // }, 1000);
        });
    </script>
</head>

<body id="page-top">
    <!-- Font Awesome -->
    <script src="js/scripts.js"></script>
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
            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to assistance page')" class="nav-link" href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Cards</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page')" class="nav-link" href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="../Patients List/PatientsList.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients List</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="../Reports Page/overallTest.php">
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

            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#smsSettingsModal">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>Settings</span>
                </a>
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
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
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
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php

                                                                                            ?></span>
                                <img class="img-profile" src="./Images/logout.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?logout=true" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                                <a class="dropdown-item" data-toggle="modal" data-target="#setPasswordModal">
                                    <i class="fas fa-unlock fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Change password
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="px-4" style="color: black;">
                    <h1 class="font-weight-bold">Immediate Assistance</h1>
                    <br>
                    <div id="refresh" class="d-flex flex-wrap">
                        <?php
                        require_once("assistanceCards.php")
                        ?>
                    </div>
                    <br>
                    <h1 class="font-weight-bold">ADL Assistance</h1>
                    <br>
                    <!-- TODO -->
                    <!-- Create a condition here to look for the ADL cards -->
                    <h1 class="font-weight-bold">Completed</h1>
                    <br>
                    <!-- TODO -->
                    <!-- Create a condition here to look for the Completed cards -->
                </div>
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <a class="btn btn-primary" href="?logout=1">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SMS Modal-->
    <div class="modal fade" id="smsSettingsModal" tabindex="-1" role="dialog" aria-labelledby="smsSettingsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="smsSettingsModalLabel">SMS Settings</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="smsToggle" name="smsSetting">
                            <label class="form-check-label" for="smsToggle" id="smsStatusLabel"></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="location.reload()">Save changes</button>
                    </div>
                </div>
            </div>
        </div>


    <!-- password Modal-->
    <div class="modal fade" id="setPasswordModal" tabindex="-1" role="dialog" aria-labelledby="setPasswordModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setPasswordModalLabel">Set New Password</h5>
                    <?php if ($verpass == 1): ?>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php endif; ?>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="input-group-text" onclick="password_show_hide();">
                                    <i class="fas fa-eye" id="show_eye"></i>
                                    <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                <span class="input-group-text" onclick="confirm_password_show_hide();">
                                    <i class="fas fa-eye" id="showConfirmPassword"></i>
                                    <i class="fas fa-eye-slash d-none" id="hideConfirmPassword"></i>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <?php if ($verpass == 1): ?>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary" id="savePassword">Save</button>
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
    <button onclick="showSnackbar('added')">Show Snackbar</button>

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>

    <!--GARBAGE -->
    <script>
        window.addEventListener('change', event => {
            showSnackbar('added');
        });
    </script>


    <script>
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
                document.getElementById("snackbar").innerHTML = "Opening nurses list page...";
            } else if (msg.includes('redirect to patients list page')) {
                document.getElementById("snackbar").innerHTML = "Refreshing patients list page...";
            }

            // Add the "show" class to DIV
            x.className = "show";

            // After 3 seconds, remove the show class from DIV
            setTimeout(function() {
                x.className = x.className.replace("show", "");
            }, 3000);
        }
    </script>
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
        // Add an event listener for the checkbox click
        document.addEventListener('DOMContentLoaded', function() {
            var smsToggle = document.getElementById('smsToggle');
            var smsStatusLabel = document.getElementById('smsStatusLabel');

            // Check if there is a cookie for smsSetting
            var smsSetting = getCookie('smsSetting');
            if (smsSetting === 'on') {
                smsToggle.checked = true;
                smsStatusLabel.textContent = 'SMS On';
            } else {
                smsToggle.checked = false;
                smsStatusLabel.textContent = 'SMS Off';
            }

            // Add event listener for checkbox click
            smsToggle.addEventListener('click', function() {
                if (smsToggle.checked) {
                    smsStatusLabel.textContent = 'SMS On';
                    setCookie('smsSetting', 'on', 365); // Set cookie with expiry of 1 year
                } else {
                    smsStatusLabel.textContent = 'SMS Off';
                    setCookie('smsSetting', 'off', 365); // Set cookie with expiry of 1 year
                }
            });
        });

        // Function to set a cookie
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Function to get a cookie by name
        function getCookie(name) {
            var nameEQ = name + "=";
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i];
                while (cookie.charAt(0) == ' ') {
                    cookie = cookie.substring(1, cookie.length);
                }
                if (cookie.indexOf(nameEQ) == 0) {
                    return cookie.substring(nameEQ.length, cookie.length);
                }
            }
            return null;
        }
    </script>

    <!-- function for change password -->
    <script>
        function password_show_hide() {
            var passwordField = $('#password');
            var showEye = $('#show_eye');
            var hideEye = $('#hide_eye');
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                showEye.addClass('d-none');
                hideEye.removeClass('d-none');
            } else {
                passwordField.attr('type', 'password');
                showEye.removeClass('d-none');
                hideEye.addClass('d-none');
            }
        }

        function confirm_password_show_hide() {
            var confirmPasswordField = $('#confirmPassword');
            var showConfirmPassword = $('#showConfirmPassword');
            var hideConfirmPassword = $('#hideConfirmPassword');
            if (confirmPasswordField.attr('type') === 'password') {
                confirmPasswordField.attr('type', 'text');
                showConfirmPassword.addClass('d-none');
                hideConfirmPassword.removeClass('d-none');
            } else {
                confirmPasswordField.attr('type', 'password');
                showConfirmPassword.removeClass('d-none');
                hideConfirmPassword.addClass('d-none');
            }
        }

        
        function showSnackbar(message) {
            var snackbar = $("#snackbar");
            snackbar.text(message);
            snackbar.addClass("show");
            setTimeout(function() { snackbar.removeClass("show"); }, 3000);
        }

        $(document).ready(function() {
            <?php if ($verpass == 0): ?>
                $('#setPasswordModal').modal('show');
            <?php endif; ?>

            $('#savePassword').click(function() {
                var password = $('#password').val();
                var confirmPassword = $('#confirmPassword').val();

                if (password !== confirmPassword) {
                    alert("Passwords do not match");
                    return;
                }

                if (password == '' && confirmPassword == '') {
                    alert("Fields are empty");
                    return;
                }

                // Log a message before sending AJAX request
                console.log("Sending AJAX request...");

                $.ajax({
                    type: "POST",
                    url: "change_First_Pass.php",
                    data: { password: password },
                    success: function(response) {
                        console.log("AJAX request successful:", response);
                        $('#setPasswordModal').modal('hide');
                        showSnackbar("Password changed successfully");
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request failed:", error);
                    }
                });
            });
        });
    </script>


    <!-- For modal -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>

</html>