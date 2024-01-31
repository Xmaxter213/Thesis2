<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

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

if (isset($_POST['add'])) {
    $work_Shift = $_POST['work_Shift'];
    $time_Range = $_POST['time_Range'];

    $query = "INSERT INTO shift_Schedule(ID, work_Shift, time_Range) VALUES (NULL,'$work_Shift', '$time_Range')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Catagory Added Successfully";
        header('Location: EditShiftSchedule.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: EditShiftSchedule.php');
        exit(0);
    }
}

if (isset($_POST['editsave'])) {
    $ID = $_POST['ID'];
    $work_Shift = $_POST['work_Shift'];
    $time_Range = $_POST['time_Range'];
    $query = "UPDATE shift_Schedule SET work_Shift='$work_Shift', time_Range='$time_Range' WHERE ID='$ID'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Catagory Added Successfully";
        header('Location: EditShiftSchedule.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: EditShiftSchedule.php');
        exit(0);
    }
}

if (isset($_POST['delete'])) {
    $ID = $_POST['ID'];

    $query = "DELETE FROM shift_Schedule WHERE ID ='$ID'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['message'] = "Catagory Added Successfully";
        header('Location: EditShiftSchedule.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: EditShiftSchedule.php');
        exit(0);
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

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>

    <!-- For table sorting -->
    <link rel="stylesheet" href="tablesort.css">

    <!-- For modal 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    -->
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
                <a onclick="showSnackbar('redirect to assistance page')" class="nav-link" href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Cards</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to nurses list page')" class="nav-link" href="NursesList.php">
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
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="../Reports Page/reports.php">
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
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?logout=true" data-toggle="modal" data-target="#logoutModal">
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
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <a href="NursesList.php" class="btn btn-primary float-end">Nurses List Table</a>
                    <a href="EditShiftSchedule.php" class="btn btn-primary float-end active">Shift Schedules Table</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
                            <a onclick="showSnackbar('add nurse')" class="btn btn-primary float-end" data-toggle="modal" data-target="#add">Add</a>

                            <!-- Add modal -->
                            <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <label>Work Shift</label>
                                                <input type="text" name="work_Shift" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter work shift" required title="Must only contain letters">

                                                <label>Time</label>
                                                <input type="text" name="time_Range" required pattern ="\S(.*\S)?[A-Za-z0-9]+"  class="form-control" placeholder="Enter time frame" required title="Must only contain letters">
                                            </div>
                                        <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button onclick="showSnackbar('add')" type = "submit" class = "btn btn-success" name = "add" >Add</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <?php
                                // retrieve selected results from database and display them on page
                                $sql = 'SELECT * FROM shift_Schedule';
                                $result = mysqli_query($con, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    echo "";
                                ?>
                                    <table class="table table-bordered table-sortable" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Work Shift</th>
                                                <th>Time</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_array($result)) {
                                            ?>

                                                <tr>
                                                    <td><?php echo $row['work_Shift'] ?></td>
                                                    <td><?php echo $row['time_Range'] ?></td>
                                                    <td>
                                                        <a onclick="showSnackbar('edit')" class="btn btn-info" data-toggle="modal" data-target="#edit<?= $row['ID'] ?>">Edit</a>

                                                        <!-- Edit modal -->
                                                        <div class="modal fade" id="edit<?= $row['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div>
                                                                                <input type="hidden" name="ID" value="<?=  $row['ID'] ?>">
                                                                            </div>

                                                                            <label>Work Shift</label>
                                                                            <input type="text" name="work_Shift" value="<?=  $row['work_Shift'] ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">

                                                                            <label>Time</label>
                                                                            <input type="text" name="time_Range" value="<?=  $row['time_Range'] ?>" required pattern ="\S(.*\S)?[A-Za-z0-9]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">
                                                                        
                                                                        </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button onclick="showSnackbar('edit save')" type = "submit" class = "btn btn-success" name = "editsave" >Save</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $row['ID'] ?>">
                                                            Delete
                                                        </button>

                                                        <!-- Delete modal -->
                                                        <div class="modal fade" id="delete<?= $row['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete?</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        The schedule will be permanently deleted. Are you sure?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <form action="" method="POST">
                                                                            <div>
                                                                                <input type="hidden" name="ID" value="<?=  $row['ID'] ?>">
                                                                            </div>

                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button onclick="showSnackbar('delete')" type = "submit" class = "btn btn-danger" name = "delete" >Delete</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
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
        function showSnackbar(msg) {
            // Get the snackbar DIV
            var x = document.getElementById("snackbar");

            //Change text
            if (msg.includes('add')) {
                document.getElementById("snackbar").innerHTML = "Add nurse page opening...";
            } else if (msg.includes('edit')) {
                document.getElementById("snackbar").innerHTML = "Opening edit modal...";
            } else if (msg.includes('edit save')) {
                document.getElementById("snackbar").innerHTML = "Saving available shifts...";
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
    <!-- For modal 
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>

</html>