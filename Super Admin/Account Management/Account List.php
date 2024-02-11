<?php
    require_once('../../dbConnection/connection2.php');


    if (isset($_GET['logout'])) {
        $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store

        date_default_timezone_set('Asia/Manila');

        $currentDateTime = date("Y-m-d H:i:s");

        // Insert into superAdminLogs
        $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Logout', '$currentDateTime')";
        $query_run_logs = mysqli_query($con2, $sqlAddLogs);

        if ($query_run_logs) 
        {
            session_destroy();
            unset($_SESSION);
            header("location: ../login_new.php");
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con2);
        }

    }
    if (!isset($_SESSION['userID'])) {
        header("location: ../login_new.php");
    }


if (isset($_POST['add'])) {
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $status = $_POST['status'];
    
    $query = "INSERT INTO superAdminAccounts ( userName, email, password, status) 
    VALUES ('$userName','$email', '$password', '$status')";
    $query_run = mysqli_query($con2, $query);

    if ($query_run) {

        $_SESSION['message'] = "Catagory Added Successfully";

        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');

        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Created Account', '$currentDateTime')";
        $query_run_logs = mysqli_query($con2, $sqlAddLogs);

         if ($query_run_logs) 
        {
            header('Location: Account List.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con2);
        }

        
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: Account List.php');
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

    <!-- For table sorting -->
    <link rel="stylesheet" href="tablesort.css">
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

            <li class="nav-item">
                <br><br>
                <a onclick="showSnackbar('redirect to Hospital Management')" class="nav-link" href="../index.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Hospitals</span></a>
            </li>

             <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item active" >
                <a onclick="showSnackbar('redirect to Account List')" class="nav-link" href="Account List.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Account Management</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="../Logs/Logs.php">
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

                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Account Management</h6>
                            <br>
                            <a onclick="showSnackbar('add nurse')" href="AddAccount.php" class="btn btn-primary float-end">Add Account</a>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">

                                <?php
                                $count = 0;
                                $sql = "SELECT * FROM superAdminAccounts";
                                $result = mysqli_query($con2, $sql);

                                $results_per_page = 10;
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
                                $sql = 'SELECT * FROM superAdminAccounts LIMIT ' . $this_page_first_result . ',' .  $results_per_page;
                                $result = mysqli_query($con2, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    echo "";

                                ?>

                                    <table class="table table-bordered table-sortable" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Account ID <input type="text" class="search-input" placeholder="AccountID"></th>
                                                <th>User Name <input type="text" class="search-input" placeholder="User Name"></th>
                                                <th>email <input type="text" class="search-input" placeholder="email"></th>
                                                <th>status <input type="text" class="search-input" placeholder="status"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_array($result)) {
                                                $count = $count + 1;

                                            ?>

                                                <tr>
                                                    <td><?php echo $row['ID']; ?></td>
                                                    <td><?php echo $row['userName'] ?></td>
                                                    <td><?php echo $row['email']; ?></td>
                                                    <td><?php echo $row['status']; ?></td>

                                                </tr>
                                        <?php

                                            }
                                        } else {
                                            echo "No Record Found";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <!-- For showing and hiding input field on deletion -->
                                    <script type="text/javascript">
                                        function getValue(x, ID) {
                                            if(x.value == 'Other'){
                                                document.getElementById("reasonForDeletionInputField" + ID).style.display = 'block'; // you need a identifier for changes
                                                document.getElementById("reasonForDeletion" + ID).value = ""; // you need a identifier for changes
                                            } else if(x.value == "Account will not be used"){
                                                document.getElementById("reasonForDeletionInputField" + ID).style.display = 'none';  // you need a identifier for changes
                                                document.getElementById("reasonForDeletion" + ID).value = "Account will not be used";
                                            } else if(x.value == "Worker does not work in the hospital anymore"){
                                                document.getElementById("reasonForDeletionInputField" + ID).style.display = 'none';  // you need a identifier for changes
                                                document.getElementById("reasonForDeletion" + ID).value = "Worker does not work in the hospital anymore";
                                            }
                                            
                                            // Store the reason in local storage
                                            localStorage.setItem('reasonForDeletion', document.getElementById("reasonForDeletion").value);
                                            

                                            // For debugging
                                            // // alert(document.getElementById("reasonForDeletion" + ID).id); //Checks if tamang nurse ID yung radio buttons

                                            // var str,
                                            // element = document.getElementById("reasonForDeletion");
                                            // if (element != null) {
                                            //     str = element.value;
                                            //     alert("WORKS: " + str);
                                            // }
                                            // else {
                                            //     str = null;
                                            //     alert("NO WORK: " + str);
                                            // }
                                        }
                                    </script>
                                    
                                    <?php
                                    // display the links to the pages
                                    for ($page = 1; $page <= $number_of_pages; $page++) {
                                        echo '<a class="btn btn-primary btn-sm" href="PatientsList.php?page=' . $page . '">' . $page . '</a> ';
                                    }
                                    ?>
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
                document.getElementById("snackbar").innerHTML = "Add Account page opening...";
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

    <!-- For modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>

</html>