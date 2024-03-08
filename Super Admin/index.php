<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../vendor/autoload.php';
    require_once('../dbConnection/connection.php');


    if (isset($_GET['logout'])) {
        $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store

        date_default_timezone_set('Asia/Manila');
        
        $currentDateTime = date("Y-m-d H:i:s");

        // Insert into superAdminLogs
        $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Logout', '$currentDateTime')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);

        if ($query_run_logs) 
        {
            session_destroy();
            unset($_SESSION);
            header("location: login_new.php");
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }

    }

    if (!isset($_SESSION['userID'])) {
        header("location: login_new.php");
    }

    if(isset($_POST['add']))
    {
        $Subscriber_FirstName = $_POST['Subscriber_first_Name'];
        $Subscriber_LastName = $_POST['Subscriber_last_Name'];

        $Subscriber_Name = $Subscriber_FirstName . $Subscriber_LastName;

        $Hospital_Name = $_POST['Hospital_Name'];
        $Subscriber_Email = $_POST['Subscriber_email'];
        $Hospital_Status = "Active";
        $Subscription = $_POST['Subscription_Duration'];

        date_default_timezone_set('Asia/Manila');
        $Creation_Date = date("Y-m-d H:i:s");

        $Expiration_Date = date("Y-m-d H:i:s", strtotime("+" . $Subscription . " months", strtotime($Creation_Date)));

        $sqladdHospital = "INSERT INTO Hospital_Table (Subscriber_Name, hospitalName, hospitalStatus, email, creation_Date, Expiration) VALUES ('$Subscriber_Name', '$Hospital_Name', '$Hospital_Status', '$Subscriber_Email', '$Creation_Date', '$Expiration_Date')";
        $query_run_addHospital = mysqli_query($con, $sqladdHospital);

        if($query_run_addHospital)
        {
            $hospital_ID = mysqli_insert_id($con);
            $query = "INSERT INTO userLogin ( email, password, userName, status, verifyPassword, hospital_ID) 
            VALUES ('$Subscriber_Email','$Subscriber_Name', '$Subscriber_Name', 'Admin', '0', '$hospital_ID')";
            $query_run = mysqli_query($con, $query);

            $queryStaff = "INSERT INTO staff_List (hospital_ID, nurse_Name, assigned_Ward, contact_No, nurse_Sex, nurse_birth_Date, shift_Schedule, employment_Status, date_Employment, activated) 
            VALUES ($hospital_ID, 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', '1')";
            $query_run = mysqli_query($con, $queryStaff);

            $userName = $_SESSION['userID'];  // Assuming userName is the correct field you want to store
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = date("Y-m-d H:i:s");
            // Insert into superAdminLogs
            
            $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Added Hospital : $Hospital_Name', '$currentDateTime')";
            $query_run_logs = mysqli_query($con, $sqlAddLogs);

            if(!$query_run_logs)
            {
                echo 'Error inserting logs: ' . mysqli_error($con);
            }
        }
        else
        {
            echo 'Error inserting hospital: ' . mysqli_error($con);
        }
    }

    if(isset($_POST['extend']))
    {
        $hospital_ID = $_POST['hospital_ID'];
        $extension = $_POST['Subscription_Duration'];

        $sql = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
        $query_run = mysqli_query($con, $sql);
        if ($query_run) {
            if (mysqli_num_rows($query_run) > 0) {
                $query_result = mysqli_fetch_assoc($query_run);
                $currentExpiration = $query_result['Expiration'];

                // Convert current expiration to DateTime object
                $currentExpirationDateTime = new DateTime($currentExpiration);

                // Calculate new expiration by adding extension duration
                $newExpirationDateTime = clone $currentExpirationDateTime;
                $newExpirationDateTime->add(new DateInterval("P{$extension}M"));

                // Now $newExpirationDateTime contains the new expiration date
                $new_expiration = $newExpirationDateTime->format('Y-m-d');

                $sqlupdate = "UPDATE Hospital_Table SET Expiration = '$new_expiration' WHERE hospital_ID = $hospital_ID";

                $query_update = mysqli_query($con, $sqlupdate);

                if ($query_update) {

                } else {
                    echo "Update failed: " . mysqli_error($con);
                }

            } else {
                echo "No results found";
            }
        } else {
            echo "Query failed: " . mysqli_error($con);
        }
    }

?>
<!DOCTYPE html>
<html>
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
            

            <hr class="sidebar-divider d-none d-md-block">
           

            <li class="nav-item active">
                <br><br>
                <a onclick="showSnackbar('redirect to Hospital Management')" class="nav-link" href="index.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Hospitals</span></a>
            </li>

             <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to Account List')" class="nav-link" href="Account Management/Account List.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Account Management</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="Logs/Logs.php">
                    <i class="bi bi-clipboard2-data"></i>
                    <span>Logs</span></a>
            </li>

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

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Hospitals</h6>
                           <a class="btn btn-primary" data-toggle="modal" data-target="#addHospital">Add</a>
                            
                        </div>


<!--MODAL HERE -->
                            <!-- Add hospital modal -->
                            <div class="modal fade" id="addHospital" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add Hospital</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <div>
                                                    <label>Subscriber's First Name</label>
                                                    <input type="text" name="Subscriber_first_Name" id="Subscriber_first_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Subscriber's First Name" required title="Must only contain letters">
                                                </div>
                                                <br>

                                                <div>
                                                    <label>Subscriber's Last Name</label>
                                                    <input type="text" name="Subscriber_last_Name" id="Subscriber_last_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Subscriber's Last Name" required title="Must only contain letters" >
                                                </div>
                                                <br>
                                                
                                                <div>
                                                    <label>Subscriber's Email</label>
                                                    <input type="text" name="Subscriber_email" id="Subscriber_email" class="form-control" placeholder="Enter Subscriber's Email">
                                                </div>
                                                <br>

                                                <div>
                                                    <label>Hospital Name</label>
                                                    <input type="text" name="Hospital_Name" id="Hospital_Name" class="form-control" placeholder="Enter Hospital Name">
                                                </div>
                                                <br>

                                               <div>
                                                    <?php 
                                                        // retrieve selected results from the database and display them on the page
                                                        $sqlDuration = 'SELECT * FROM Subscription_Duration';
                                                        $resultDuration = mysqli_query($con, $sqlDuration);

                                                        if (mysqli_num_rows($resultDuration) > 0) {
                                                    ?>
                                                        <label>Subscription Duration</label>
                                                        <select id="Subscription_Duration" name="Subscription_Duration">
                                                            <?php
                                                                while ($row = mysqli_fetch_array($resultDuration)) {
                                                            ?>
                                                                <option value="<?php echo $row["Duration_Month"]; ?>">
                                                                    <?php echo $row["Duration_Month"] . " Months"; ?>
                                                                </option>
                                                            <?php
                                                                }
                                                            ?>
                                                        </select>
                                                    <?php
                                                        }
                                                    ?>
                                                </div>



                                            <br>
                                             
                                            </div>
                                        <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button onclick="showSnackbar('add nurse')" type = "submit" class = "btn btn-primary" name = "add" >Add</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <?php

                                $count = 0;
                                $sql = "SELECT * FROM Hospital_Table";
                                $result = mysqli_query($con, $sql);

                                //This is for pagination
                                $limit = isset($_POST["limit-records"]) ? $_POST["limit-records"] : 10;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $result = $con->query("SELECT * FROM Hospital_Table LIMIT $start, $limit");
                                $hospitals = $result->fetch_all(MYSQLI_ASSOC);

                                $result1 = $con->query("SELECT count(hospital_ID) AS hospital_ID FROM Hospital_Table");
                                $count2 = $result1->fetch_all(MYSQLI_ASSOC);
                                $total = $count2[0]['hospital_ID'];
                                $pages = ceil( $total / $limit );

                                $Previous = $page - 1;
                                $Next = $page + 1;

                                $result = mysqli_query($con, $sql);

                                if (mysqli_num_rows($result) > 0) {
                                    echo "";
                                ?>
                                    <table class="table table-bordered table-sortable" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Hospital ID<input type="text" class="search-input" placeholder="Hospital ID"></th>
                                                <th>Subscriber Name<input type="text" class="search-input" placeholder="Subscriber's Name"></th>
                                                <th>Hospital Name<input type="text" class="search-input" placeholder="Hospital Name"></th>
                                                <th>Hospital Email<input type="text" class="search-input" placeholder="Hospital Email"></th>
                                                <th>Hospital Status <input type="text" class="search-input" placeholder="Hospital Status"></th>
                                                <th>Duration <input type="text" class="search-input" placeholder="Duration"></th>
                                                <th>Creation Date <input type="text" class="search-input" placeholder="Creation Date"></th>
                                                <th>Expiration <input type="text" class="search-input" placeholder="Expiration"></th>
                                                <th>Extend Subscription</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach($hospitals as $hospital) :
                                                $currentDate = new DateTime();
                                                $expirationDate = new DateTime($hospital['Expiration']);

                                                $interval = $currentDate->diff($expirationDate);

                                                // Check if the status is empty or if the expiration is less than 0 days
                                                if (empty($hospital['hospitalStatus']) || $interval->format('%R%a') < 0) {
                                                    // Update the hospital status to 'Expired' in the database
                                                    $hospitalID = $hospital['hospital_ID'];
                                                    $updateQuery = "UPDATE Hospital_Table SET hospitalStatus = 'Expired' WHERE hospital_ID = '$hospitalID'";
                                                    mysqli_query($con, $updateQuery);
                                                } else {
                                                    // Set the status to 'Active' if the duration is greater than or equal to 0 days
                                                    $hospitalID = $hospital['hospital_ID'];
                                                    $updateQuery = "UPDATE Hospital_Table SET hospitalStatus = 'Active' WHERE hospital_ID = '$hospitalID'";
                                                    mysqli_query($con, $updateQuery);
                                                }
                                                ?>


                                                <tr>
                                                    <td><?php echo $hospital['hospital_ID'] ?></td>
                                                    <td><?php echo $hospital['Subscriber_Name'] ?></td>
                                                    <td><?php echo $hospital['hospitalName'] ?></td>
                                                    <td><?php echo $hospital['email'] ?></td>
                                                    <td><?php echo $hospital['hospitalStatus'] ?></td>
                                                    <td>
                                                        <?php
                                                        // Check if there are years, months, or days
                                                        $formattedInterval = '';

                                                        if ($interval->y > 0) {
                                                            $formattedInterval .= $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
                                                        }

                                                        if ($interval->m > 0) {
                                                            $formattedInterval .= ($formattedInterval ? ', ' : '') . $interval->m . ' month' . ($interval->m > 1 ? 's' : '');
                                                        }

                                                        if ($interval->d > 0 || empty($formattedInterval)) {
                                                            $formattedInterval .= ($formattedInterval ? ', ' : '') . $interval->d . ' day' . ($interval->d > 1 ? 's' : '');
                                                        }

                                                        // Check if the expiration date is greater than the current date
                                                        if ($currentDate > $expirationDate) {
                                                            echo '-' . $formattedInterval; // Display negative duration
                                                        } else {
                                                            echo $formattedInterval; // Display positive duration
                                                        }
                                                        ?>
                                                    </td>

                                                    <td><?php echo $hospital['creation_Date'] ?></td>
                                                    <td><?php echo $hospital['Expiration'] ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#extendModal<?php echo $hospital['hospital_ID']; ?>">
                                                            Extend Subscription
                                                        </button>
                                                    </td>
<!--MODAL HERE -->
                                                        <!-- Extension Modal -->
                                                        <div class="modal fade" id="extendModal<?php echo $hospital['hospital_ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="extendModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="extendModalLabel">Extend Subscription</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form method="post" action="">
                                                                            <input type="hidden" name="hospital_ID" value="<?php echo $hospital['hospital_ID']; ?>">
                                                                            <div class="form-group">
                                                                                <?php 
                                                                                    // retrieve selected results from the database and display them on the page
                                                                                    $sqlDuration = 'SELECT * FROM Subscription_Duration';
                                                                                    $resultDuration = mysqli_query($con, $sqlDuration);

                                                                                    if (mysqli_num_rows($resultDuration) > 0) {
                                                                                ?>
                                                                                    <label>Subscription Duration</label>
                                                                                    <select id="Subscription_Duration" name="Subscription_Duration">
                                                                                        <?php
                                                                                            while ($row = mysqli_fetch_array($resultDuration)) {
                                                                                        ?>
                                                                                            <option value="<?php echo $row["Duration_Month"]; ?>">
                                                                                                <?php echo $row["Duration_Month"] . " Months"; ?>
                                                                                            </option>
                                                                                        <?php
                                                                                            }
                                                                                        ?>
                                                                                    </select>
                                                                                <?php
                                                                                    }
                                                                                ?>
                                                                            </div>
                                                                            <button type = "submit" class = "btn btn-primary" name = "extend" >extend</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                </tr>
                                            <?php endforeach;
                                            } ?>
                                        </tbody>
                                    </table>

                                    <!-- For showing and hiding input field on deletion -->
                                    <script>
                                        src = "../Table Sorting/searchTable.js"
                                    </script>
                                    
                                    <!-- Pagination start -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li class="page-item">
                                            <a class="page-link" href="index.php?page=<?= $Previous; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo; Previous</span>
                                            </a>
                                            </li>
                                            <?php for($i = 1; $i<= $pages; $i++) : ?>
                                                <li class="page-item"><a class="page-link" href="index.php?page=<?= $i; ?>"><?= $i; ?></a></li>
                                            <?php endfor; ?>
                                            <li class="page-item">
                                            <a class="page-link" href="index.php?page=<?= $Next; ?>" aria-label="Next">
                                                <span aria-hidden="true">Next &raquo;</span>
                                            </a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <div class="text-center" style="margin-top: 20px; " class="col-md-2">
                                            <form method="post" action="#">
                                                    <select name="limit-records" id="limit-records">
                                                        <option disabled="disabled" selected="selected">---Limit Records---</option>
                                                        <?php foreach([10,100,500,1000,5000] as $limit): ?>
                                                            <option <?php if( isset($_POST["limit-records"]) && $_POST["limit-records"] == $limit) echo "selected" ?> value="<?= $limit; ?>"><?= $limit; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- Pagination end -->
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

    <!--GARBAGE -->
    <script>
        window.addEventListener('change', event => {
            showSnackbar('added');
        });
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <!-- Pagination -->
    <script type="text/javascript">
    $(document).ready(function(){
        $("#limit-records").change(function(){
            // alert(this.value)
            $('form').submit();
        })
    })
    </script>
</body>
</html>

