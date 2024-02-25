<?php
require_once('../../dbConnection/connection.php');

// Check if session is not already active before starting a new one
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION);
    header("location: ../../MainHospital/login_new.php");
}

if (!isset($_SESSION['userID'])) {
    header("location: ../../MainHospital/login_new.php");
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

    <!-- For Modal -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <!-- For div refresh -->
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

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top shadow">


                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <!-- Lnd Name -->
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col text-center">
                                <div class="mx-auto">
                                    <div class="navbar-brand">
                                        <i class="fas fa-laugh-wink"></i>
                                        <span class="fa-regular fa-hand ml-2">Helping Hand</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php ?></span>
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
                    <div id="refresh" class="d-flex flex-wrap">
                        <?php require_once("assistanceCards.php") ?>
                    </div>
                    <h1 class="font-weight-bold">ADL Assistance</h1>
                </div>

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

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

    

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>

    <!--GARBAGE -->
    
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
