<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';
require_once('../../dbConnection/connection.php');
//include('message.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

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

//This is to make sure that deactivated accounts that are due for deletion are deleted
include('nurseDeleteEntriesDue.php');

//This code runs after the NursesList.php page i think
if (isset($_POST['add'])) {
    $nurse_first_Name = $_POST['nurse_first_Name'];
    $nurse_last_Name = $_POST['nurse_last_Name'];
    $nurse_full_Name = $nurse_last_Name . ", " . $nurse_first_Name;
    $nurse_Contact_No = $_POST['nurse_Contact_No'];
    $nurse_Sex = $_POST['nurse_Sex'];
    $nurse_birth_Date = $_POST['nurse_birth_Date'];
    $shift_Schedule = $_POST['shift_Schedule'];
    $employment_Status = $_POST['employment_Status'];
    $date_Employment = $_POST['date_Employment'];
    $activated = $_POST['activated'];

    #Login
    $nurse_email = $_POST['nurse_email'];
    $account_status = $_POST['Account_Status'];
    $userName = $nurse_first_Name . $nurse_last_Name;
    $nurse_password = $userName;

    //encrypt Login
    $enc_nurse_email = encryptthis($nurse_email, $key);
    $enc_account_status = encryptthis($account_status, $key);
    $enc_userName = encryptthis($userName, $key);
    $enc_password = encryptthis($nurse_password, $key);

    //$date_Employment = sha1($_POST['date_Employment']);

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

    //Encrypt data from form
    $enc_nurse_Name = encryptthis($nurse_full_Name, $key);
    $enc_nurse_Contract_No = encryptthis($nurse_Contact_No, $key);
    $enc_nurse_Sex = encryptthis($nurse_Sex, $key);
    $enc_nurse_birth_Date = encryptthis($nurse_birth_Date, $key);
    $enc_date_Employment = encryptthis($date_Employment, $key);

    $query = "INSERT INTO staff_List (nurse_ID, hospital_ID, nurse_Name, assigned_Ward, contact_No, nurse_Sex, nurse_birth_Date, shift_Schedule, employment_Status, date_Employment, activated) VALUES (NULL, '$hospital_ID', '$enc_nurse_Name', '$nurse_Assigned_Ward', '$enc_nurse_Contract_No', '$enc_nurse_Sex', '$enc_nurse_birth_Date','$shift_Schedule','$employment_Status', '$enc_date_Employment', '$activated')";
    $query_run = mysqli_query($con, $query);

    $query_Login = "INSERT INTO userLogin (ID, email, password, userName, status, code, verifyPassword, hospital_ID) VALUES (NULL, '$nurse_email','$enc_password', '$enc_userName', '$enc_account_status', '0', '0','$hospital_ID')";
    $query_Login_run = mysqli_query($con, $query_Login);

    if ($query_run) {

        $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.elasticemail.com';                     
                $mail->SMTPAuth   = true;                                  
                $mail->Username   = 'j4ishere@gmail.com';                     
                $mail->Password   = 'A02F3F4222553D746B478EC9E43E48624D90'; 
                $mail->Port       = 2525;

                $mail->setFrom('j4ishere@gmail.com', 'Helping Hand');
                $mail->addAddress($nurse_email, 'Recipient Name');
                $mail->isHTML(true);
                $mail->Subject = 'Account Creation';
                $mail->Body    = "Hello {$nurse_full_Name},<br><br>Your Account Have been Created.<br>Email: {$nurse_email} <br>Password: {$nurse_password} <br><br>Thank you for choosing our Helping Hand service!<br><br>Best regards,<br>Helping Hand";

                $mail->send();
                echo 'Message has been sent';
            } 
            catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }


        

        // Prepare the SELECT query using mysqli
        $query = "SELECT nurse_ID FROM staff_List WHERE nurse_Name = ?";
        $getnurseID = $con->prepare($query);
        $getnurseID->bind_param("s", $enc_nurse_Name);

        // Execute the SELECT query
        $database = $getnurseID->execute();

        // Store and fetch the result
        $getnurseID->store_result();
        $getnurseID->bind_result($ID);
        $getnurseID->fetch();

        // Close the statement
        $getnurseID->close();

        // Rest of your code
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Created $account_status Account ID: $ID status : $account_status ', '$currentDateTime', '$hospital_ID')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Added Successfully";
            header('Location: NursesList.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }

        
    } else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: NursesList.php');
        exit(0);
    }

}

if (isset($_POST['edit'])) {
    $nurse_ID = $_POST['nurse_ID'];
    $nurse_first_Name = $_POST['nurse_first_Name'];
    $nurse_last_Name = $_POST['nurse_last_Name'];
    $nurse_full_Name = $nurse_last_Name . ", " . $nurse_first_Name;
    $assigned_Ward = $_POST['assigned_Ward'];
    $nurse_Contact_No = $_POST['nurse_Contact_No'];
    $nurse_Sex = $_POST['nurse_Sex'];
    $nurse_birth_Date = $_POST['nurse_birth_Date'];
    $shift_Schedule = $_POST['shift_Schedule'];
    $employment_Status = $_POST['employment_Status'];
    $date_Employment = $_POST['date_Employment'];
    //$password = sha1($_POST['password']);

    //Encrypt data from form
    $enc_nurse_Name = encryptthis($nurse_full_Name, $key);
    $enc_nurse_Name = encryptthis($nurse_full_Name, $key);
    $enc_nurse_Contact_No = encryptthis($nurse_Contact_No, $key);
    $enc_nurse_Sex = encryptthis($nurse_Sex, $key);
    $enc_nurse_birth_Date = encryptthis($nurse_birth_Date, $key);
    $enc_date_Employment = encryptthis($date_Employment, $key);

    $query = "UPDATE staff_List SET nurse_Name='$enc_nurse_Name', assigned_Ward='$assigned_Ward', contact_No='$enc_nurse_Contact_No', nurse_Sex='$enc_nurse_Sex', nurse_birth_Date ='$enc_nurse_birth_Date', shift_Schedule='$shift_Schedule', employment_Status='$employment_Status', date_Employment='$enc_date_Employment' WHERE nurse_ID='$nurse_ID'";
    $query_run = mysqli_query($con, $query);


    if ($query_run) {
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Updated Nurse/Admin Account ID: $nurse_ID', '$currentDateTime', '$hospital_ID')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: NursesList.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con);
        }
    }
     else {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: NursesList.php');
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
                <a onclick="showSnackbar('redirect to assistance page'); showBubbleAnimation(event);"
                    class="nav-link menu__link buttonStyle" href="../Assistance Card Page/assistanceCard.php">
                    <i class="bi bi-wallet2"></i>
                    <span>Assistance Page</span>
                </a>
            </li>

            <li class="nav-item active">
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

            <li class="nav-item">
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
                    <a href="NursesList.php" class="btn btn-secondary float-end active">Active Nurses List</a>
                    <a href="NursesListInactive.php" class="btn btn-secondary float-end">Inactive Nurses List</a>
                    <a href="EditShiftSchedule.php" class="btn btn-secondary float-end">Shift Schedules List</a>
                    <a href="RestoreNurse.php" class="btn btn-secondary float-end">Restore Nurse</a>
                    <a href="DeletedNursesList.php" class="btn btn-secondary float-end">Deleted Nurses List</a>
                    <br><br>

                    <!-- DataTables Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-secondary">Active Nurses Accounts List</h6>
                            <!-- <a class="btn btn-primary" data-toggle="modal" data-target="#addNurse">Add</a> -->
                            <a class="btn btn-secondary" data-toggle="modal" data-target="#addNursePasswordVerificationModal">Add</a>
<!-- MODAL HERE -->
                            <!-- Modal for add nurse, password verification -->
                            <div class="modal fade" id="addNursePasswordVerificationModal" tabindex="-1" role="dialog" aria-labelledby="addNursePasswordVerificationModalLabel" aria-hidden="true">
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
                                                <button type="submit" class="btn btn-primary" name="verifyAddNurse">Verify Password</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
<!-- MODAL HERE -->
                            <!-- Add nurse modal -->
                            <div class="modal fade" id="addNurse" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add nurse</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="" method="POST">
                                                <div>
                                                    <label>Nurse First Name</label>
                                                    <input type="text" name="nurse_first_Name" id="nurse_first_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Nurse's First Name" required title="Must only contain letters" oninput="updateEmailPassword()">
                                                </div>

                                                <div>
                                                    <label>Nurse Last Name</label>
                                                    <input type="text" name="nurse_last_Name" id="nurse_last_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters" oninput="updateEmailPassword()">
                                                </div>
                                                <br>
                                                
                                                <div>
                                                    <label>Nurse Contact No.</label>
                                                    <input type="text" name="nurse_Contact_No" id="nurse_Contact_No" required pattern="\S(.*\S)?[0-9]+" class="form-control" placeholder="Enter Nurse's Contact No." required title="Must only contain numbers">
                                                </div>
                                                <br>

                                                <div>
                                                    <label>Nurse Sex</label>
                                                    <select id="nurse_Sex" name="nurse_Sex">
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                                <br>
                                                
                                                <div>
                                                    <label>Nurse's Email</label>
                                                    <input type="email" name="nurse_email" id="nurse_email" class="form-control" placeholder="Nurse Email" >
                                                    
                                                </div>

                                            <div>
                                                <br>
                                                <label>Account Status</label>
                                                <select id="Account_Status" name="Account_Status">
                                                    <option value="Nurse">Nurse</option>
                                                    <option value="Admin">Admin</option>
                                                </select>
                                            </div>
                                            <br>
                                            <?php
                                                // Calculate the date 19 years ago in the format Year-Month-Day
                                                $nineteenYearsAgo = date('Y-m-d', strtotime('-19 years'));
                                            ?>
                                                <!-- HTML input field -->
                                                <label>Birth Date</label>
                                                <input type="date" name="nurse_birth_Date" name="nurse_birth_Date" min='01/01/1899' max="<?php echo $nineteenYearsAgo; ?>">
                                            
                                            <div>
                                            <br>


                                            <div>
                                                <?php 
                                                    // retrieve selected results from database and display them on page
                                                    $sqlShiftSched = 'SELECT * FROM shift_Schedule';
                                                    $resultShiftSched = mysqli_query($con, $sqlShiftSched);
                                                    

                                                    if (mysqli_num_rows($resultShiftSched) > 0) {
                                                ?>
                                                <label>Shift Status</label>
                                                <select id="shift_Schedule" name="shift_Schedule">
                                                    <?php
                                                        while ($row = mysqli_fetch_array($resultShiftSched)) {
                                                            $concatenatedRow = $row["work_Shift"] . ": " . $row["time_Range"];
                                                    ?>
                                                            <option value="<?php echo $row["work_Shift"];
                                                            // The value we usually set is the primary key
                                                            ?>">
                                                                <?php echo $concatenatedRow;
                                                            ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    }?>
                                                </select>
                                            </div>
                                            <br>
                                            <div>
                                                <label>Nurse Status</label>
                                                <select id="employment_Status" name="employment_Status">
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                </select>
                                            </div>
                                            <br>
                                            <div>
                                                <label>Date of Employment</label>
                                                <input type="date" id="date_Employment" name="date_Employment" value="" min="2018-01-01" max="2030-12-31" />
                                            </div>
                                            <script>
                                                //Make date today the max value
                                                var today = new Date().toISOString().split('T')[0]; 
                                                document.getElementById("date_Employment").setAttribute("max", today);

                                                //Date picker filled required
                                                document.getElementById("date_Employment").required = true;
                                            </script>
                                            <div>
                                                <input type="hidden" name="activated" value=1>
                                            </div>
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

                                //Let's get the current website user's assigned ward
                                    $staff_ID = $_SESSION['idNUM'];

                                    // Prepare the SELECT query using mysqli
                                    $query = "SELECT assigned_Ward FROM staff_List WHERE nurse_ID = ?";
                                    $getNurseAssignedWard = $con->prepare($query);
                                    $getNurseAssignedWard->bind_param("s", $staff_ID);
                                    
                                    // Execute the SELECT query
                                    $database = $getNurseAssignedWard->execute();

                                    // Store and fetch the result
                                    $getNurseAssignedWard->store_result();
                                    $getNurseAssignedWard->bind_result($nurse_Assigned_Ward);
                                    $getNurseAssignedWard->fetch();

                                    // Close the statement
                                    $getNurseAssignedWard->close();

                                $sql = "SELECT * FROM staff_List WHERE activated = 1 AND assigned_Ward = '$nurse_Assigned_Ward' AND employment_Status = 'Active'";
                                $result = mysqli_query($con, $sql);

                                //This is for pagination
                                $limit = isset($_POST["limit-records"]) ? $_POST["limit-records"] : 10;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $result = $con->query("SELECT * FROM staff_List  WHERE activated = 1 AND assigned_Ward = '$nurse_Assigned_Ward' AND hospital_ID = '$hospital_ID' AND employment_Status = 'Active' LIMIT $start, $limit");
                                $nurses = $result->fetch_all(MYSQLI_ASSOC);

                                $result1 = $con->query("SELECT count(nurse_ID) AS nurse_ID FROM staff_List WHERE activated = 1 AND assigned_Ward = '$nurse_Assigned_Ward' AND hospital_ID = '$hospital_ID' AND employment_Status = 'Active'");
                                $custCount = $result1->fetch_all(MYSQLI_ASSOC);
                                $total = $custCount[0]['nurse_ID'];
                                $pages = ceil( $total / $limit );

                                $Previous = $page - 1;
                                $Next = $page + 1;

                                if (mysqli_num_rows($result) > 0) {
                                    echo "";
                                ?>
                                    <table class="table table-bordered table-sortable" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Nurse ID <input type="text" class="search-input" placeholder="Nurse ID"></th>
                                                <th>Nurse Name <input type="text" class="search-input" placeholder="Nurse Name"></th>
                                                <th>Nurse Contract No. <input type="text" class="search-input" placeholder="Nurse Contract No."></th>
                                                <th>Shift Schedule <input type="text" class="search-input" placeholder="Shift Schedule"></th>
                                                <th>View Details</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach($nurses as $nurse) :
                                                $count = $count + 1;

                                                if ($nurse['nurse_Name'] === 'HOSPITAL OWNER') {
                                                    // $dec_nurse_Name = 'HOSPITAL OWNER';
                                                    // $dec_nurse_Contact_No = 'HOSPITAL OWNER';
                                                    // $dec_nurse_Sex = 'HOSPITAL OWNER';
                                                    // $dec_nurse_birth_Date = 'HOSPITAL OWNER';
                                                    // $birthDate = 'HOSPITAL OWNER';
                                                    // $dec_nurse_Age = 'HOSPITAL OWNER';
                                                    // $dec_date_Employment = 'HOSPITAL OWNER';
                                                    continue;
                                                } else {
                                                    //Decrypt data from db
                                                    $dec_nurse_Name = decryptthis($nurse['nurse_Name'], $key);
                                                    $dec_nurse_Contact_No = decryptthis($nurse['contact_No'], $key);
                                                    $dec_nurse_Sex = decryptthis($nurse['nurse_Sex'], $key);
                                                    $dec_nurse_birth_Date = decryptthis($nurse['nurse_birth_Date'], $key);
                                                    //date in mm/dd/yyyy format; or it can be in other formats as well
                                                    $birthDate = $dec_nurse_birth_Date;
                                                    //explode the date to get month, day and year
                                                    $birthDate = explode("-", $birthDate);
                                                    //get age from date or birthdate
                                                    $dec_nurse_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                                                        ? ((date("Y") - $birthDate[0]) - 1)
                                                        : (date("Y") - $birthDate[0]));

                                                    if ($dec_nurse_Age == -1) {
                                                        $dec_nurse_Age = 0;
                                                    }

                                                    $dec_date_Employment = decryptthis($nurse['date_Employment'], $key);
                                                }
                                                
                                            ?>

                                                <tr>
                                                    <td><?php echo $nurse['nurse_ID'] ?></td>
                                                    <td><?php echo $dec_nurse_Name ?></td>
                                                    <td><?php echo $dec_nurse_Contact_No ?></td>
                                                    <td><?php echo $nurse['shift_Schedule']; ?></td>
                                                    <td>
                                                        <a class="btn btn-info" data-toggle="modal" data-target="#ViewNurseDetails<?= $nurse['nurse_ID'] ?>">View Details</a>

                                                        <div class="modal fade" id="ViewNurseDetails<?= $nurse['nurse_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Nurse Details</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <label>Name: <?php echo $dec_nurse_Name ?></label><br>
                                                                        <label>Contact Number: <?php echo $dec_nurse_Contact_No ?></label><br>
                                                                        <label>Sex: <?php echo $dec_nurse_Sex ?></label><br>
                                                                        <label>birhtday: <?php echo $dec_nurse_birth_Date ?></label><br>
                                                                        <label>Age: <?php echo $dec_nurse_Age ?></label><br>
                                                                        <label>Shift Schedule: <?php echo $nurse['shift_Schedule']; ?></label><br>
                                                                        <label>Date of Emploment: <?php echo $dec_date_Employment ?></label><br>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a onclick="showSnackbar('edit nurse')" class="btn btn-info" data-toggle="modal" data-target="#editNursePasswordVerificationModal<?= $nurse['nurse_ID'] ?>">Edit</a>
<!-- MODAL HERE -->
                                                        <!-- Modal for edit nurse, password verification -->
                                                        <div class="modal fade" id="editNursePasswordVerificationModal<?= $nurse['nurse_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="editNursePasswordVerificationModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editNursePasswordVerificationModalLabel">Password Verification</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="" method="POST">
                                                                            <div class="form-group">
                                                                                <input type="hidden" id="nurse_ID" name="nurse_ID" value="<?=  $nurse['nurse_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyEditNurse">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Edit modal -->
                                                        <div class="modal fade" id="edit<?= $nurse['nurse_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Edit nurse</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <?php
                                                                        //explode the date to get month, day and year
                                                                        $exploded_nurse_Name = explode(", ", $dec_nurse_Name);
                                                                        $nurse_last_Name = $exploded_nurse_Name[0];
                                                                        $nurse_first_Name = $exploded_nurse_Name[1];
                                                                        ?>

                                                                        <form action="" method="POST">
                                                                        <div>
                                                                            <input type="hidden" name="nurse_ID" value="<?=  $nurse['nurse_ID'] ?>">
                                                                        </div>
                                                                        <div>
                                                                            <label>Nurse First Name</label>
                                                                            <input type="text" name="nurse_first_Name" value="<?=  $nurse_first_Name ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's First Name" required title="Must only contain letters">
                                                                        </div>

                                                                        <div>
                                                                            <label>Nurse Last Name</label>
                                                                            <input type="text" name="nurse_last_Name" value="<?=  $nurse_last_Name ?>" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">
                                                                        </div>
                                                                        <br>

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
                                                                        <div>
                                                                            <label>Nurse Contact No.</label>
                                                                            <input type="text" name="nurse_Contact_No" value="<?=  $dec_nurse_Contact_No ?>" id="nurse_Contact_No" required pattern="\S(.*\S)?[0-9]+" class="form-control" placeholder="Enter Nurse's Contact No." required title="Must only contain numbers">
                                                                        </div>
                                                                        <br>

                                                                        <div>
                                                                            <label>Nurse Sex</label>
                                                                            <select id="nurse_Sex" name="nurse_Sex">
                                                                                <option value="Male"<?php if ($dec_nurse_Sex == 'Male') echo ' selected="selected"'; ?>>Male</option>
                                                                                <option value="Female"<?php if ($dec_nurse_Sex == 'Female') echo ' selected="selected"'; ?>>Female</option>
                                                                            </select>
                                                                        </div>
                                                                        
                                                                        <div>
                                                                            <br>
                                                                            <label>Birth Date</label>
                                                                            <input type="date" id="nurse_birth_Date" value="<?=  $dec_nurse_birth_Date ?>" name="nurse_birth_Date" min='01/01/1899' max='13/13/2000'/>
                                                                        </div>
                                                                        <script>
                                                                            //Make date today the max value
                                                                            var today = new Date().toISOString().split('T')[0];
                                                                            document.getElementById("nurse_birth_Date").setAttribute("max", today);

                                                                            //Date picker filled required
                                                                            document.getElementById("nurse_birth_Date").required = true;
                                                                        </script>
                                                                        <br>
                                                                        <div>
                                                                        <?php 
                                                                            // retrieve selected results from database and display them on page
                                                                            $sqlShiftSched = 'SELECT * FROM shift_Schedule';
                                                                            $resultShiftSched = mysqli_query($con, $sqlShiftSched);
                                                                            

                                                                            if (mysqli_num_rows($resultShiftSched) > 0) {
                                                                        ?>
                                                                        <label>Shift Status</label>
                                                                        <select id="shift_Schedule" name="shift_Schedule">
                                                                            <?php
                                                                                while ($row2 = mysqli_fetch_array($resultShiftSched)) {
                                                                                    $concatenatedRow = $row2["work_Shift"] . ": " . $row2["time_Range"];
                                                                            ?>
                                                                                    <option  value="<?php echo $row2["work_Shift"];
                                                                                    // The value we usually set is the primary key
                                                                                    ?>" <?php if ($nurse['shift_Schedule'] == $row2["work_Shift"]) echo ' selected="selected"'; ?>>
                                                                                        <?php echo $concatenatedRow;
                                                                                    ?>
                                                                                    </option>
                                                                                    <?php
                                                                                }
                                                                            }?>
                                                                        </select>
                                                                        </div>
                                                                        <br>
                                                                        <div>
                                                                            <label>Nurse Status</label>
                                                                            <select id="employment_Status" name="employment_Status" value="<?=  $dec_employment_Status ?>">
                                                                                <option value="Active" <?php if ($nurse['employment_Status']  == 'Active') echo ' selected="selected"'; ?>>Employed</option>
                                                                                <option value="Inactive" <?php if ($nurse['employment_Status'] == 'Inactive') echo ' selected="selected"'; ?>>Unemployed</option>
                                                                            </select>
                                                                        </div>
                                                                        <br>
                                                                        <div>
                                                                            <label>Date of Employment</label>
                                                                            <input type="date" id="date_Employment" name="date_Employment" value="<?=  $dec_date_Employment ?>" min="2018-01-01" max="2030-12-31" />
                                                                        </div>
                                                                        <script>
                                                                            //Make date today the max value
                                                                            document.getElementById("date_Employment").setAttribute("max", today);

                                                                            //Date picker filled required
                                                                            document.getElementById("date_Employment").required = true;
                                                                        </script>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button onclick="showSnackbar('edit save')" type = "submit" class = "btn btn-success" name = "edit" >Save</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteNursePasswordVerificationModal<?= $nurse['nurse_ID'] ?>">
                                                            Delete
                                                        </button>
<!-- MODAL HERE -->
                                                        <!-- Modal for delete nurse, password verification -->
                                                        <div class="modal fade" id="deleteNursePasswordVerificationModal<?= $nurse['nurse_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteNursePasswordVerificationModalLabel" aria-hidden="true">
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
                                                                                <input type="hidden" id="nurse_ID" name="nurse_ID" value="<?=  $nurse['nurse_ID'] ?>">
                                                                                <label for="password">Enter Your Password:</label>
                                                                                <input type="password" class="form-control" id="password" name="password" required>
                                                                            </div>
                                                                            <button type="submit" class="btn btn-primary" name="verifyDeleteNurse">Verify Password</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
<!-- MODAL HERE -->
                                                        <!-- Delete modal -->
                                                        <div class="modal fade" id="delete<?= $nurse['nurse_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Are you sure you want to delete?</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        The deleted item would be in the recycle bin for 30 days before being permanently deleted.
                                                                        <form action="DeleteNurse.php" method="POST">
                                                                            <br>
                                                                            <label for="deleteReason1">Reason for deletion: </label> <br>

                                                                            <!-- Isa lang may required kasi same name naman sila -->
                                                                            <input type="radio" name="deleteReason" id="deleteReason1"  value="Account will not be used" required onchange="getValue(this, <?php echo $nurse['nurse_ID'] ?>)">
                                                                            <label for="deleteReason1">Account will not be used</label> <br>

                                                                            <input type="radio" name="deleteReason" id="deleteReason2" value="Worker does not work in the hospital anymore" onchange="getValue(this, <?php echo $nurse['nurse_ID'] ?>)">
                                                                            <label for="deleteReason2">Worker does not work in the hospital anymore</label> <br>

                                                                            <!-- Iba name cuz input field need -->
                                                                            <input type="radio" name="deleteReason" id="deleteReason3" value="Other" onchange="getValue(this, <?php echo $nurse['nurse_ID'] ?>)">
                                                                            <label for="deleteReason3">Other</label> <br>
                                                                            
                                                                            <div id="reasonForDeletionInputField<?= $nurse['nurse_ID'] ?>" style="display:none;">
                                                                            <!-- wtf bat iba yung gumagana ?= pero ?php hindi sa code sa baba :/ -->
                                                                            <textarea rows="4" cols="50" type="text" name="reasonForDeletion<?= $nurse['nurse_ID'] ?>" id="reasonForDeletion<?= $nurse['nurse_ID'] ?>" onchange="getValue(this, <?php echo $nurse['nurse_ID'] ?>)" pattern="\S(.*\S)?[A-Za-z0-9]+" class="form-control" placeholder="Enter reason for deletion" title="Must only contain letters & numbers">
                                                                            </textarea>   
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            <button type="submit" name="nurseDelete" value="<?= $nurse['nurse_ID'] ?>" class="btn btn-danger">Delete</a>
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
                                    <script>
                                        src = "../Table Sorting/searchTable.js"
                                    </script>
                                    
                                    <!-- Pagination start -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li class="page-item">
                                            <a class="page-link" href="NursesList.php?page=<?= $Previous; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo; Previous</span>
                                            </a>
                                            </li>
                                            <?php for($i = 1; $i<= $pages; $i++) : ?>
                                                <li class="page-item"><a class="page-link" href="NursesList.php?page=<?= $i; ?>"><?= $i; ?></a></li>
                                            <?php endfor; ?>
                                            <li class="page-item">
                                            <a class="page-link" href="NursesList.php?page=<?= $Next; ?>" aria-label="Next">
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
    <!-- <div id="snackbar">Some text some message..</div> -->

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
                document.getElementById("snackbar").innerHTML = "Refreshing nurses list page...";
            } else if (msg.includes('redirect to nurses list page')) {
                document.getElementById("snackbar").innerHTML = "Opening nurses list page...";
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
                const tablenurses = inputField
                    .closest("table")
                    .querySelectorAll("tbody > tr");
                const headerCell = inputField.closest("th");
                const otherHeaderCells = headerCell.closest("tr").children;
                const columnIndex = Array.from(otherHeaderCells).indexOf(headerCell);
                const searchableCells = Array.from(tablenurses).map(
                    (nurse) => nurse.querySelectorAll("td")[columnIndex]
                );

                inputField.addEventListener("input", () => {
                    const searchQuery = inputField.value.toLowerCase();

                    for (const tableCell of searchableCells) {
                        const nurse = tableCell.closest("tr");
                        const value = tableCell.textContent.toLowerCase().replace(",", "");

                        nurse.style.visibility = null;

                        if (value.search(searchQuery) === -1) {
                            nurse.style.visibility = "collapse";
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
    if (isset($_POST['verifyAddNurse'])) {
        $enteredPassword = $_POST['password'];
        $ID = $_SESSION['idNUM'];
        $userName = $_SESSION['userID'];
    
        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE ID = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $ID);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();

        $verifyPassword = decryptthis($verifyPassword, $key);


        
        if ($enteredPassword === $verifyPassword) {
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#addNurse').modal('show');
            });
            </script>";
            
            // // 'di na pala kailangan to, I'll just keep it just in case ganito pagawa ni sir, I haven't added the +5 mins yet
            // $query = "UPDATE staff_List SET nurse_Name='$enc_nurse_Name', contact_No='$enc_nurse_Contact_No' WHERE nurse_ID='$nurse_ID'";
            // $query_run = mysqli_query($con, $query);
            // // Get the current date and time in SQL format
            // $currentDateTime = date('Y-m-d H:i:s');

            // $query = "UPDATE staff_List SET CRUD_auth = '$currentDateTime' WHERE ID = ?";
            // $getuserpassword = $con->prepare($query);
            // $getuserpassword->bind_param("s", $ID);
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

    if (isset($_POST['verifyEditNurse'])) {
        $enteredPassword = $_POST['password'];
        $ID = $_SESSION['idNUM'];
        $nurse_ID = $_POST['nurse_ID']; //One to edit

        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE ID = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $ID);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();

        $verifyPassword = decryptthis($verifyPassword, $key);

        if ($enteredPassword === $verifyPassword) {
            // echo "<script>alert('$nurse_ID');</script>";
            
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#edit$nurse_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }

    if (isset($_POST['verifyDeleteNurse'])) {
        $enteredPassword = $_POST['password'];
        $ID = $_SESSION['idNUM'];
        $nurse_ID = $_POST['nurse_ID']; //One to delete

        //This is for checking if pw is correct
        $query = "SELECT password FROM userLogin WHERE ID = ?";
        $getuserpassword = $con->prepare($query);
        $getuserpassword->bind_param("s", $ID);
        $getuserpassword->execute();
        $getuserpassword->store_result();
        $getuserpassword->bind_result($verifyPassword);
        $getuserpassword->fetch();
        $getuserpassword->close();

        $verifyPassword = decryptthis($verifyPassword, $key);

        if ($enteredPassword === $verifyPassword) {
            // echo "<script>alert('$nurse_ID');</script>";
            
            echo "<script type='text/javascript'>
            $(document).ready(function(){
            $('#delete$nurse_ID').modal('show');
            });
            </script>";   
        } else {
            // Password is incorrect, display an error message
            echo '<script>alert("Incorrect password. Please try again.");</script>';
        }
    }
?>