<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

//This is to make sure that deactivated accounts that are due for deletion are deleted
include('patientDeleteEntriesDue.php');

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

if (isset($_POST['patientRefer'])) {
    $patient_ID = $_POST['patientRefer'];
    $new_Assigned_Ward = $_POST['assigned_Ward'];
    $getReasonForRefer = 'reasonForRefer' . $patient_ID;
    $reasonForRefer = $_POST[$getReasonForRefer];

    // Get logged in user
    $userName = $_SESSION['userID'];
    
    $query = "UPDATE patient_List SET assigned_Ward='$new_Assigned_Ward' WHERE patient_ID='$patient_ID'";
    
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Referred patient $patient_ID to ward: $new_Assigned_Ward. Reason: $reasonForRefer', '$currentDateTime', '$hospital_ID')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: PatientsListDischarged.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }

        header('Location: PatientsListDischarged.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: PatientsListDischarged.php');
        exit(0);
    }
}

if (isset($_POST['patientAdmit'])) {
    $patient_ID = $_POST['patientAdmit'];
    $getReasonForAdmit = 'reasonForAdmit' . $patient_ID;
    $reasonForAdmit = $_POST[$getReasonForAdmit];

    // Get logged in user
    $userName = $_SESSION['userID'];

    $query = "UPDATE patient_List SET admission_Status='Admitted' WHERE patient_ID='$patient_ID'";
    
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Re-admitted patient $patient_ID. Reason: $reasonForAdmit', '$currentDateTime', $hospital_ID)";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: PatientsListDischarged.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }

        header('Location: PatientsListDischarged.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: PatientsListDischarged.php');
        exit(0);
    }
}

if (isset($_POST['edit'])) {
    $patient_ID = $_POST['patient_ID'];
    $patient_first_Name = $_POST['patient_first_Name'];
    $patient_last_Name = $_POST['patient_last_Name'];
    $patient_full_Name = $patient_first_Name . ", " . $patient_last_Name;
    $room_Number = $_POST['room_Number'];
    $patient_birth_Date = $_POST['patient_birth_Date'];
    $reason_Admission = $_POST['reason_Admission'];
    $assistance_Status = $_POST['assistance_Status'];
    $device_Assigned = $_POST['device_Assigned'];
    //$password = sha1($_POST['password']);

    //Encrypt data from form
    $enc_patient_Name = encryptthis($patient_full_Name, $key);
    $enc_patient_birth_Date = encryptthis($patient_birth_Date, $key);
    $enc_reason_Admission = encryptthis($reason_Admission, $key);

    if ($device_Assigned == NULL) {
        $query = "UPDATE patient_List SET patient_Name ='$enc_patient_Name', room_Number='$room_Number', birth_Date='$enc_patient_birth_Date', reason_Admission='$enc_reason_Admission', 
        assistance_Status='$assistance_Status', gloves_ID=NULL WHERE patient_ID='$patient_ID'";
    } else if ($device_Assigned != NULL) {
        $query = "UPDATE patient_List SET patient_Name ='$enc_patient_Name', room_Number='$room_Number', birth_Date='$enc_patient_birth_Date', reason_Admission='$enc_reason_Admission', 
        assistance_Status='$assistance_Status', gloves_ID='$device_Assigned' WHERE patient_ID='$patient_ID'";
    }
    
    $query_run = mysqli_query($con, $query);

    if ($query_run) {


        $_SESSION['message'] = "Catagory Updated Successfully";
        header('Location: PatientsListDischarged.php');
        exit(0);
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: PatientsListDischarged.php');
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
    <!-- Font Awesome -->
    <script src="js/scripts.js"></script>
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

            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);" class="nav-link"
                    href="../Nurses List/NursesList.php">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Nurses List</span>
                </a>
            </li>

            <!-- Divider -->

            <li class="nav-item active">
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
                            <a class="nav-link" href="../Online_Help/patient_List_Guide.php" target="_blank">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    Need Help?
                                </span>
                                <i class="bi bi-info-circle"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php

                                                                                            ?></span>
                                <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg" style="filter: invert(1);">
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

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <a href="PatientsList.php" class="btn btn-secondary float-end">Admitted Patients List</a>
                    <a href="PatientsListDischarged.php" class="btn btn-secondary float-end active">Discharged Patients List</a>
                    <a href="RestorePatient.php" class="btn btn-secondary float-end">Restore Patient</a>
                    <a href="DeletedPatientsList.php" class="btn btn-secondary float-end">Deleted Patients List</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">Discharged Patients Table</h6>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">

                                <?php
                                $count = 0;

                                //Let's get the current website user's assigned ward
                                $staff_ID = $_SESSION['idNUM'];

                                // Prepare the SELECT query using mysqli
                                $query = "SELECT assigned_Ward FROM staff_List WHERE nurse_ID = ?";
                                $getNurseAssignedWard = $con->prepare($query);
                                $getNurseAssignedWard->bind_param("i", $staff_ID);

                                // Execute the SELECT query
                                $database = $getNurseAssignedWard->execute();

                                // Store and fetch the result
                                $getNurseAssignedWard->store_result();
                                $getNurseAssignedWard->bind_result($nurse_Assigned_Ward);
                                $getNurseAssignedWard->fetch();

                                // Close the statement
                                $getNurseAssignedWard->close();
                                                       
                                //This is for pagination
                                $limit = isset($_POST["limit-records"]) ? $_POST["limit-records"] : 10;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $result = $con->query("SELECT * FROM patient_List WHERE activated = 1 AND admission_Status = 'Discharged' AND assigned_Ward = '$nurse_Assigned_Ward' AND hospital_ID = '$hospital_ID' LIMIT $start, $limit");
                                $patients = $result->fetch_all(MYSQLI_ASSOC);

                                $result1 = $con->query("SELECT count(patient_ID) AS patient_ID FROM patient_List WHERE activated = 1 AND admission_Status = 'Discharged' AND assigned_Ward = '$nurse_Assigned_Ward' AND hospital_ID = '$hospital_ID'");
                                $custCount = $result1->fetch_all(MYSQLI_ASSOC);
                                $total = $custCount[0]['patient_ID'];
                                $pages = ceil( $total / $limit );

                                $Previous = $page - 1;
                                $Next = $page + 1;

                                if (mysqli_num_rows($result) > 0) {
                                    echo "";
                                ?>
                                    <table class="table table-bordered table-sortable" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Patient ID <input type="text" class="search-input" placeholder="Patient ID"></th>
                                                <th>Patient Name <input type="text" class="search-input" placeholder="Patient Name"></th>
                                                <th>Room Number <input type="text" class="search-input" placeholder="Room Number"></th>
                                                <th>Age <input type="text" class="search-input" placeholder="Age"></th>
                                                <th>Reason for Admission <input type="text" class="search-input" placeholder="Reason for Admission"></th>
                                                <th>Device Assigned ID <input type="text" class="search-input" placeholder="Device Assigned ID"></th>
                                                <th>Change Assigned Ward</th>
                                                <th>Admit</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach($patients as $patient) :
                                                $count = $count + 1;

                                                //Decrypt data from db
                                                $dec_patient_Name = decryptthis($patient['patient_Name'], $key);
                                                $dec_patient_birth_Date = decryptthis($patient['birth_Date'], $key);
                                                //date in mm/dd/yyyy format; or it can be in other formats as well
                                                $birthDate = $dec_patient_birth_Date;
                                                //explode the date to get month, day and year
                                                $birthDate = explode("-", $birthDate);
                                                //get age from date or birthdate
                                                $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                                                    ? ((date("Y") - $birthDate[0]) - 1)
                                                    : (date("Y") - $birthDate[0]));

                                                if ($patient_Age == -1) {
                                                    $patient_Age = 0;
                                                }

                                                $dec_reason_Admission = decryptthis($patient['reason_Admission'], $key);
                                            ?>

                                                <tr>
                                                    <td><?php echo $patient['patient_ID']; ?></td>
                                                    <td><?php echo $dec_patient_Name ?></td>
                                                    <td><?php echo $patient['room_Number']; ?></td>
                                                    <td><?php echo $patient_Age ?></td>
                                                    <td><?php echo $dec_reason_Admission ?></td>
                                                    <td><?php 
                                                    if ($patient['gloves_ID'] == NULL) {
                                                        echo "No Assigned Device";
                                                    } 
                                                    else {
                                                        echo $patient['gloves_ID'];
                                                    }?>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#referPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>">
                                                            Refer
                                                        </button>
<!-- MODAL HERE -->
                                                        <!-- Modal for refer to another ward, password verification -->
                                                        <div class="modal fade" id="referPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="referPatientPasswordVerificationModal" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="referPatientPasswordVerificationModal">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="patient_ID" name="patient_ID" value="<?=  $patient['patient_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyReferPatient">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Refer to another ward modal -->
                                                        <div class="modal fade" id="refer<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="dischargeModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to discharge this patient?</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <label>Patient will be assigned to a different ward.</label><br>

                                                                            <br>
                                                                            <label for="dischargeReason1">Reason for referring patient to another ward: </label> <br>

                                                                            <!-- Assign ward here -->
                                                                            <label for="assigned_Ward">Choose a ward to refer to:</label>
                                                                            <select id="assigned_Ward" name="assigned_Ward">
                                                                            <option value="Medicine Ward">Medicine Ward</option>
                                                                            <option value="Surgery Ward">Surgery Ward</option>
                                                                            <option value="Intensive Care Unit">Intensive Care Unit</option>
                                                                            <option value="OB Ward">OB Ward</option>
                                                                            <option value="Psych Ward">Psych Ward</option>
                                                                            <option value="Emergency Room">Emergency Room</option>
                                                                            <option value="Neonatal Intensive Care Unit">Neonatal Intensive Care Unit</option>
                                                                            <option value="Delivery Room">Delivery Room</option>
                                                                            <option value="Minor Surgery Unit">Minor Surgery Unit</option>
                                                                            <option value="Pediatric Ward">Pediatric Ward</option>
                                                                            <option value="Out-Patient Department">Out-Patient Department</option>
                                                                            </select>
                                                                            <br>

                                                                            <!-- Isa lang may required kasi same name naman sila -->
                                                                            <input type="radio" name="referReason" id="referReason1"  value="Refer order given by doctor" required >
                                                                            <label for="deleteReason1">Doctor's orders</label> <br>

                                                                            <!-- Iba name cuz input field need -->
                                                                            <input type="radio" name="referReason" id="referReason3" value="Other" onchange="getValueRefer(this, <?php echo $patient['patient_ID'] ?>)">
                                                                            <label for="referReason3">Other</label> <br>
                                                                            
                                                                            <div id="reasonForReferInputField<?= $patient['patient_ID'] ?>" style="display:none;">

                                                                            <!-- wtf bat iba yung gumagana ?= pero ?php hindi sa code sa baba :/ -->
                                                                            <textarea rows="4" cols="50" type="text" value="" name="reasonForRefer<?= $patient['patient_ID'] ?>" id="reasonForrefer<?= $patient['patient_ID'] ?>" onchange="getValueRefer(this, <?php echo $patient['patient_ID'] ?>)" pattern="\S(.*\S)?[A-Za-z0-9]+" class="form-control" placeholder="Enter reason for refer" title="Must only contain letters & numbers"></textarea>    
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="submit" name="patientRefer" value="<?= $patient['patient_ID'] ?>" class="btn btn-success">Refer</a>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#admitPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>">
                                                            Admit
                                                        </button>
<!-- MODAL HERE -->
                                                        <!-- Modal for discharge nurse, password verification -->
                                                        <div class="modal fade" id="admitPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteNursePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="dischargePatientPasswordVerificationModal">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="patient_ID" name="patient_ID" value="<?=  $patient['patient_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyAdmitPatient">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Admit Patient modal -->
                                                        <div class="modal fade" id="admit<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="admitModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to admit this patient?</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <label>Patient will be admitted and will be placed back on the Admitted Patients List page.</label><br>

                                                                            <br>
                                                                            <label for="admitReason1">Reason for discharging patient: </label> <br>

                                                                            <!-- Isa lang may required kasi same name naman sila -->
                                                                            <input type="radio" name="admitReason" id="admitReason1"  value="Accidentally discharged patient" required onchange="getValueAdmit(this, <?php echo $patient['patient_ID'] ?>)">
                                                                            <label for="admitReason1">Accidentally discharged patient</label> <br>

                                                                            <!-- Iba name cuz input field need -->
                                                                            <input type="radio" name="admitReason" id="admitReason2" value="Other" onchange="getValueAdmit(this, <?php echo $patient['patient_ID'] ?>)">
                                                                            <label for="admitReason2">Other</label> <br>
                                                                            
                                                                            <div id="reasonForAdmitInputField<?= $patient['patient_ID'] ?>" style="display:none;">

                                                                            <!-- wtf bat iba yung gumagana ?= pero ?php hindi sa code sa baba :/ -->
                                                                            <textarea rows="4" cols="50" type="text" name="reasonForAdmit<?= $patient['patient_ID'] ?>" id="reasonForAdmit<?= $patient['patient_ID'] ?>" onchange="getValueAdmit(this, <?php echo $patient['patient_ID'] ?>)" pattern="\S(.*\S)?[A-Za-z0-9]+" class="form-control" placeholder="Enter reason for admit" title="Must only contain letters & numbers">
                                                                            </textarea>    
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="submit" name="patientAdmit" value="<?= $patient['patient_ID'] ?>" class="btn btn-success">Admit Patient</a>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <a onclick="showSnackbar('edit')" class="btn btn-info" data-toggle="modal" data-target="#editPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>">Edit</a>
<!-- MODAL HERE -->
                                                        <!-- Modal for edit nurse, password verification -->
                                                        <div class="modal fade" id="editPatientPasswordVerificationModal<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="editNursePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editPatientPasswordVerificationModal">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="patient_ID" name="patient_ID" value="<?=  $patient['patient_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyEditPatient">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Edit modal -->
                                                        <div class="modal fade" id="edit<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                                        <?php
                                                                            //explode the date to get month, day and year
                                                                            $exploded_patient_Name = explode(", ", $dec_patient_Name);
                                                                            $patient_first_Name = $exploded_patient_Name[0];
                                                                            $patient_last_Name = $exploded_patient_Name[1];
                                                                            ?>
                                                                            
                                                                            <form action ="" method="POST" >
                                                                            <div>
                                                                                <input type="hidden" name="patient_ID" value="<?=  $patient['patient_ID'] ?>">
                                                                            </div>
                                                                            <div>
                                                                                <label>Patient First Name</label>
                                                                                <input type="text" name="patient_first_Name" value="<?=  $patient_first_Name ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Patient's First Name" required title="Must only contain letters">
                                                                            </div>

                                                                            <div>
                                                                                <label>Patient Last Name</label>
                                                                                <input type="text" name="patient_last_Name" value="<?=  $patient_last_Name ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Patient's Last Name" required title="Must only contain letters">
                                                                            </div>
                                                                            <div>

                                                                            <div>
                                                                                <label>Room Number</label>
                                                                                <input type="text" class="form-control" name="room_Number" value="<?=  $patient['room_Number'] ?>" placeholder="Enter Room Number" required pattern ="[0-9]+" title="Must only contain numbers"/>
                                                                            </div>

                                                                            <div>

                                                                            <div>
                                                                                <br>
                                                                                <label>Patient Birth Date</label>
                                                                                <input type="date" id="patient_birth_Date" value="<?=  $dec_patient_birth_Date ?>" name="patient_birth_Date" min='01/01/1899' max='13/13/2000'/>
                                                                            </div>
                                                                            <script>
                                                                                //Make date today the max value
                                                                                document.getElementById("patient_birth_Date").setAttribute("max", today);

                                                                                //Date picker filled required
                                                                                document.getElementById("patient_birth_Date").required = true;
                                                                            </script>
                                                                            <br>

                                                                            <div>
                                                                                <label>Reason for Admission</label>
                                                                                <input type="text" class="form-control" name="reason_Admission" value="<?=  $dec_reason_Admission ?>" placeholder="Enter Reason for Admission" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters & numbers"/>
                                                                            </div>
                                                                            <br>
                                                                            <div>
                                                                            <label>Assistance Status</label>
                                                                                <select id="assistance_Status" name="assistance_Status" value="<?=  $patient['assistance_Status'] ?>">
                                                                                    <option value="Unassigned" <?php if ($patient["assistance_Status"] == "Unassigned"){ echo "selected";}?>>Unassigned</option>
                                                                                    <option value="On The Way" <?php if ($patient["assistance_Status"] == "On The Way"){ echo "selected";}?>>On The Way</option>
                                                                                </select>
                                                                            </div>
                                                                            <br>
                                                                            <div>
                                                                                <?php
                                                                                    // Check if patient has assigned device ID
                                                                                    $device_Assigned_Variable = NULL;
                                                                                    if ($patient['gloves_ID'] != NULL)
                                                                                    {
                                                                                        $device_Assigned_Variable = $patient['gloves_ID'];
                                                                                    }
                                                                                    
                                                                                    // Retrieve selected results from database and display them on page
                                                                                    $sqlNursesList = 'SELECT device_ID FROM device_List';
                                                                                    $resultDeviceIDs = mysqli_query($con, $sqlNursesList);
                                                                                    
                                                                                    if (mysqli_num_rows($resultDeviceIDs) > 0) {
                                                                                ?>
                                                                                <label>Device ID Assigned</label>
                                                                                <select id="device_Assigned" name="device_Assigned" value=<?= $device_Assigned_Variable ?>>
                                                                                            <option value="">Not Assigned a Device</option>
                                                                                    <?php
                                                                                        while ($row2 = mysqli_fetch_array($resultDeviceIDs)) {
                                                                                    ?>
                                                                                            <option value="<?php echo $row2["device_ID"]; ?>"
                                                                                            <?php
                                                                                            // The value we usually set is the primary key
                                                                                            if ($row2["device_ID"] == $device_Assigned_Variable){
                                                                                                echo "selected";
                                                                                            }?>>
                                                                                                <?php echo $row2["device_ID"];
                                                                                            ?>
                                                                                            </option>
                                                                                            <?php
                                                                                        }
                                                                                    }?>
                                                                                </select>
                                                                            </div>
                                                                            <br>
                                                                            
                                                                            </div>

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button onclick="showSnackbar('save patient')" type = "submit" class = "btn btn-success" name = "edit" >Save</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletePatientPasswordVerificationModal<?= $patient['patient_ID'] ?>">
                                                            Delete
                                                        </button>
<!-- MODAL HERE -->
                                                        <!-- Modal for delete nurse, password verification -->
                                                        <div class="modal fade" id="deletePatientPasswordVerificationModal<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteNursePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="deletePatientPasswordVerificationModal">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="patient_ID" name="patient_ID" value="<?=  $patient['patient_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyDeletePatient">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Delete modal -->
                                                        <div class="modal fade" id="delete<?= $patient['patient_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete?</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        The deleted item would be in the recycle bin for 3 days before being permanently deleted.
                                                                        <form action="DeletePatient.php" method="POST">
                                                                            <br>
                                                                            <label for="deleteReason1">Reason for deletion: </label> <br>

                                                                            <!-- Isa lang may required kasi same name naman sila -->
                                                                            <input type="radio" name="deleteReason" id="deleteReason1"  value="Patient data will not be used" required onchange="getValue(this, <?php echo $patient['patient_ID'] ?>)">
                                                                            <label for="deleteReason1">Patient data will not be used</label> <br>

                                                                            <!-- Iba name cuz input field need -->
                                                                            <input type="radio" name="deleteReason" id="deleteReason3" value="Other" onchange="getValue(this, <?php echo $patient['patient_ID'] ?>)">
                                                                            <label for="deleteReason3">Other</label> <br>
                                                                            
                                                                            <div id="reasonForDeletionInputField<?= $patient['patient_ID'] ?>" style="display:none;">
                                                                            <!-- wtf bat iba yung gumagana ?= pero ?php hindi sa code sa baba :/ -->
                                                                            <textarea rows="4" cols="50" type="text" name="reasonForDeletion<?= $patient['patient_ID'] ?>" id="reasonForDeletion<?= $patient['patient_ID'] ?>" onchange="getValue(this, <?php echo $patient['patient_ID'] ?>)" pattern="\S(.*\S)?[A-Za-z0-9]+" class="form-control" placeholder="Enter reason for deletion" title="Must only contain letters & numbers">
                                                                            </textarea>
                                                                            </div>   
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="submit" name="dischargedPatientDelete" value="<?= $patient['patient_ID'] ?>" class="btn btn-danger">Delete</a>
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
                                    
                                    <!-- For showing and hiding input field on refer -->
                                    <script type="text/javascript">
                                        function getValueRefer(x, ID) {
                                            if(x.value == 'Other'){
                                                document.getElementById("reasonForReferInputField" + ID).style.display = 'block'; // you need a identifier for changes
                                                document.getElementById("reasonForRefer" + ID).value = ""; // you need a identifier for changes
                                            } else if(x.value == "Refer order given by doctor"){
                                                document.getElementById("reasonForReferInputField" + ID).style.display = 'none';  // you need a identifier for changes
                                                document.getElementById("reasonForRefer" + ID).value = "Doctor's order";
                                            }
                                            
                                            // Store the reason in local storage
                                            //localStorage.setItem('reasonForRefer', document.getElementById("reasonForRefer").value);      

                                            // For debugging
                                            // // alert(document.getElementById("reasonForDeletion" + ID).id); //Checks if tamang nurse ID yung radio buttons

                                            // var str,
                                            // element = document.getElementById("reasonForDischarge" + ID);
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

                                    <!-- For showing and hiding input field on admit -->
                                    <script type="text/javascript">
                                        function getValueAdmit(x, ID) {
                                            if(x.value == 'Other'){
                                                document.getElementById("reasonForAdmitInputField" + ID).style.display = 'block'; // you need a identifier for changes
                                                document.getElementById("reasonForAdmit" + ID).value = ""; // you need a identifier for changes
                                            } else if(x.value == "Accidentally discharged patient"){
                                                document.getElementById("reasonForAdmitInputField" + ID).style.display = 'none';  // you need a identifier for changes
                                                document.getElementById("reasonForAdmit" + ID).value = "Accidentally discharged patient";
                                            }
                                            
                                            // Store the reason in local storage
                                            localStorage.setItem('reasonForAdmit', document.getElementById("reasonForAdmit").value);      

                                            // For debugging
                                            // // alert(document.getElementById("reasonForDeletion" + ID).id); //Checks if tamang nurse ID yung radio buttons

                                            // var str,
                                            // element = document.getElementById("reasonForDischarge" + ID);
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

                                    <!-- For showing and hiding input field on deletion -->
                                    <script type="text/javascript">
                                        function getValue(x, ID) {
                                            if(x.value == 'Other'){
                                                document.getElementById("reasonForDeletionInputField" + ID).style.display = 'block'; // you need a identifier for changes
                                                document.getElementById("reasonForDeletion" + ID).value = ""; // you need a identifier for changes
                                            } else if(x.value == "Patient data will not be used"){
                                                document.getElementById("reasonForDeletionInputField" + ID).style.display = 'none';  // you need a identifier for changes
                                                document.getElementById("reasonForDeletion" + ID).value = "Patient data will not be used";
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
                                <!-- Pagination start -->
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item">
                                        <a class="page-link" href="PatientsListDischarged.php?page=<?= $Previous; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo; Previous</span>
                                        </a>
                                        </li>
                                        <?php for($i = 1; $i<= $pages; $i++) : ?>
                                            <li class="page-item"><a class="page-link" href="PatientsListDischarged.php?page=<?= $i; ?>"><?= $i; ?></a></li>
                                        <?php endfor; ?>
                                        <li class="page-item">
                                        <a class="page-link" href="PatientsListDischarged.php?page=<?= $Next; ?>" aria-label="Next">
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
                    (patient) => patient.querySelectorAll("td")[columnIndex]
                );

                inputField.addEventListener("input", () => {
                    const searchQuery = inputField.value.toLowerCase();

                    for (const tableCell of searchableCells) {
                        const patient = tableCell.closest("tr");
                        const value = tableCell.textContent.toLowerCase().replace(",", "");

                        patient.style.visibility = null;

                        if (value.search(searchQuery) === -1) {
                            patient.style.visibility = "collapse";
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
    if (isset($_POST['verifyReferPatient'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $patient_ID = $_POST['patient_ID']; //One to edit

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
            $('#refer$patient_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyEditPatient'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $patient_ID = $_POST['patient_ID']; //One to edit

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
            $('#edit$patient_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyDeletePatient'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $patient_ID = $_POST['patient_ID']; //One to delete

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
            $('#delete$patient_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyAdmitPatient'])) {
        $enteredPassword = $_POST['password'];
        $userName = $_SESSION['userID'];
        $patient_ID = $_POST['patient_ID']; //One to delete

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
            $('#admit$patient_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }
?>