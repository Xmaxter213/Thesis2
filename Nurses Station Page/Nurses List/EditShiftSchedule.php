<?php
require_once('../../dbConnection/connection.php');

$hospital_ID = $_SESSION['selectedHospitalID'];

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
        header("location: ../../MainHospital/login_new.php");
    } else {
        echo 'Error inserting logs: ' . mysqli_error($con);
    }
}

// USER LOGGED IN
if (!isset($_SESSION['userID'])) {
    header("location: ../../MainHospital/login_new.php");
} 
else 
{
    $status = $_SESSION['userStatus'];

    if ($status === 'Nurse') {
        header("location: ../../Nurse Page/Assistance Card Page/assistanceCard.php");
    }
    if ($status === 'Super Admin')
    {
        header("location: ../../Super Admin/index.php");
    }
}

// SELECTED HOSPITAL !EXPIRED
if(isset($_SESSION['selectedHospitalID']))
{
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $row = mysqli_fetch_assoc($query_run);
        $expirationDate = new DateTime($row['Expiration']);
        $currentDate = new DateTime();

        if($expirationDate < $currentDate)
        {
            header("location: ../../expiredPage/expired.php");
        }
    }
    else
    {
        echo "Error executing the query: " . mysqli_error($con);
    }

    
}

if (isset($_POST['add'])) {
    $work_Shift = $_POST['work_Shift'];
    $time_Range = $_POST['time_Range'];
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "INSERT INTO shift_Schedule(ID, work_Shift, time_Range, hospital_ID) VALUES (NULL,'$work_Shift', '$time_Range', $hospital_ID)";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {

        // Prepare the SELECT query using mysqli
        $query = "SELECT ID FROM shift_Schedule WHERE work_Shift = ?";
        $getnurseID = $con->prepare($query);
        $getnurseID->bind_param("s", $work_Shift);

        // Execute the SELECT query
        $database = $getnurseID->execute();

        // Store and fetch the result
        $getnurseID->store_result();
        $getnurseID->bind_result($ID);
        $getnurseID->fetch();

        // Close the statement
        $getnurseID->close();

        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Created Shift Schedule ID: $ID', '$currentDateTime', $hospital_ID)";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: EditShiftSchedule.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }
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
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Updated Shift Schedule ID: $ID', '$currentDateTime', $hospital_ID)";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: EditShiftSchedule.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }
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
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Deleted Shift Schedule ID: $ID', '$currentDateTime', $hospital_ID)";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: EditShiftSchedule.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }
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
                <a onclick="showSnackbar('redirect to assistance page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Cards</span>
                </a>
            </li>

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link" href="../Patients List/PatientsList.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <span>Patients List</span>
                </a>
            </li>

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                    class="nav-link" href="../Reports Page/overallTest.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Logs/Logs.php">
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
                            <a class="nav-link" href="../Online_Help/nurses_List_Guide.php" target="_blank">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    Need Help?
                                </span>
                                <i class="bi bi-info-circle"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown no-arnurse">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php

                                                                                            ?></span>
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg" style="filter: invert(1);">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--gnurse-in" aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php?logout=true" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <a href="NursesList.php" class="btn btn-secondary float-end">Nurses List</a>
                    <a href="EditShiftSchedule.php" class="btn btn-secondary float-end active">Shift Schedules List</a>
                    <a href="RestoreNurse.php" class="btn btn-secondary float-end">Restore Nurse</a>
                    <a href="DeletedNursesList.php" class="btn btn-secondary float-end">Deleted Nurses List</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">Shift Schedules Table</h6>
                            <a onclick="showSnackbar('add nurse')" class="btn btn-secondary float-end" data-toggle="modal" data-target="#addShiftSchedulePasswordVerificationModal">Add Shift Schedule</a>

                            <!-- Modal for add schedule, password verification -->
                            <div class="modal fade" id="addShiftSchedulePasswordVerificationModal" tabindex="-1" role="dialog" aria-labelledby="addShiftSchedulePasswordVerificationModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addNursePasswordVerificationModalLabel">Password Verification</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <div class="form-group">
                                                    <label for="password">Enter Your Password:</label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="verifyAddShiftSchedule">Verify Password</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add modal -->
                            <div class="modal fade" id="addShiftSchedule" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                //This is for pagination
                                $limit = isset($_POST["limit-records"]) ? $_POST["limit-records"] : 10;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $result = $con->query("SELECT * FROM shift_Schedule WHERE hospital_ID = '$hospital_ID' LIMIT $start, $limit");
                                $shiftSchedules = $result->fetch_all(MYSQLI_ASSOC);

                                $result1 = $con->query("SELECT count(ID) AS ID FROM shift_Schedule WhERE hospital_ID = '$hospital_ID'");
                                $custCount = $result1->fetch_all(MYSQLI_ASSOC);
                                $total = $custCount[0]['ID'];
                                $pages = ceil( $total / $limit );

                                $Previous = $page - 1;
                                $Next = $page + 1;
                                
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
                                            foreach($shiftSchedules as $shiftSchedule) :
                                            ?>

                                                <tr>
                                                    <td><?php echo $shiftSchedule['work_Shift'] ?></td>
                                                    <td><?php echo $shiftSchedule['time_Range'] ?></td>
                                                    <td>
                                                        <a onclick="showSnackbar('edit')" class="btn btn-info" data-toggle="modal" data-target="#editShiftSchedulePasswordVerificationModal<?= $shiftSchedule['ID'] ?>">Edit</a>

                                                        <!-- Modal for edit shift schedule, password verification -->
                                                        <div class="modal fade" id="editShiftSchedulePasswordVerificationModal<?= $shiftSchedule['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="editShiftSchedulePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editShiftSchedulePasswordVerificationModalLabel">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="ID" name="ID" value="<?=  $shiftSchedule['ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyEditShiftSchedule">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Edit modal -->
                                                        <div class="modal fade" id="edit<?= $shiftSchedule['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                                                <input type="hidden" name="ID" value="<?=  $shiftSchedule['ID'] ?>">
                                                                            </div>

                                                                            <label>Work Shift</label>
                                                                            <input type="text" name="work_Shift" value="<?=  $shiftSchedule['work_Shift'] ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">

                                                                            <label>Time</label>
                                                                            <input type="text" name="time_Range" value="<?=  $shiftSchedule['time_Range'] ?>" required pattern ="\S(.*\S)?[A-Za-z0-9]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">
                                                                        
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
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteNursePasswordVerificationModal<?= $shiftSchedule['ID'] ?>">
                                                            Delete
                                                        </button>

                                                        <!-- Modal for delete nurse, password verification -->
                                                        <div class="modal fade" id="deleteNursePasswordVerificationModal<?= $shiftSchedule['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteNursePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="deleteNursePasswordVerificationModalLabel">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="ID" name="ID" value="<?=  $shiftSchedule['ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyDeleteShiftSchedule">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Delete modal -->
                                                        <div class="modal fade" id="delete<?= $shiftSchedule['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                                                <input type="hidden" name="ID" value="<?=  $shiftSchedule['ID'] ?>">
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
                                                <?php endforeach;
                                                } ?>
                                        </tbody>
                                    </table>
                                    <script>
                                        src = "../Table Sorting/searchTable.js"
                                    </script>

                                    <!-- Pagination start -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li class="page-item">
                                            <a class="page-link" href="EditShiftSchedule.php?page=<?= $Previous; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo; Previous</span>
                                            </a>
                                            </li>
                                            <?php for($i = 1; $i<= $pages; $i++) : ?>
                                                <li class="page-item"><a class="page-link" href="EditShiftSchedule.php?page=<?= $i; ?>"><?= $i; ?></a></li>
                                            <?php endfor; ?>
                                            <li class="page-item">
                                            <a class="page-link" href="EditShiftSchedule.php?page=<?= $Next; ?>" aria-label="Next">
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

<?php
    if (isset($_POST['verifyAddShiftSchedule'])) {
        $enteredPassword = $_POST['password'];
    
        $userName = $_SESSION['userID'];
    
        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE userName = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $userName);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();


        
        if ($enteredPassword === $verifyPassword) {
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#addShiftSchedule').modal('show');
            });
            </script>";
            
            // // 'di na pala kailangan to, I'll just keep it just in case ganito pagawa ni sir, I haven't added the +5 mins yet
            // $query = "UPDATE staff_List SET nurse_Name='$enc_nurse_Name', contact_No='$enc_nurse_Contact_No' WHERE nurse_ID='$nurse_ID'";
            // $query_run = mysqli_query($con, $query);
            // // Get the current date and time in SQL format
            // $currentDateTime = date('Y-m-d H:i:s');

            // $query = "UPDATE staff_List SET CRUD_auth = '$currentDateTime' WHERE userName = ?";
            // $getuserpassword = $con->prepare($query);
            // $getuserpassword->bind_param("s", $userName);
            // $getuserpassword->execute();
            // $getuserpassword->store_result();
            // $getuserpassword->bind_result($verifyPassword);
            // $getuserpassword->fetch();
            // $getuserpassword->close();
            
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyEditShiftSchedule'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $shift_Schedule_ID = $_POST['ID']; //One to edit

        $testing = 1;
        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE userName = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $userName);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();

        if ($enteredPassword === $verifyPassword) {
            // echo "<script>alert('$shift_Schedule_ID');</script>";
            
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#edit$shift_Schedule_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyDeleteShiftSchedule'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $shift_Schedule_ID = $_POST['ID']; //One to delete

        $testing = 1;
        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE userName = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $userName);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();

        if ($enteredPassword === $verifyPassword) {
            // echo "<script>alert('$nurse_ID');</script>";
            
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#delete$shift_Schedule_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }
?>