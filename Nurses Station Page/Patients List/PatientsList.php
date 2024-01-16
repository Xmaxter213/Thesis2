<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

//This code runs after the NursesList.php page i think
if(isset($_POST['add']))
{
    $patient_ID = $_POST['patient_ID'];
    $patient_first_Name = $_POST['patient_first_Name'];
    $patient_last_Name = $_POST['patient_last_Name'];
    $patient_full_Name = $patient_first_Name.", ".$patient_last_Name;
    $room_Number = $_POST['room_Number'];
    $patient_birth_Date = $_POST['patient_birth_Date'];
    $reason_Admission = $_POST['reason_Admission'];
    $admission_Status = $_POST['admission_Status'];
    $nurse_ID = $_POST['nurse_ID'];
    $assistance_Status = $_POST['assistance_Status'];
    $device_Assigned = $_POST['device_Assigned'];
    //$date_Employment = sha1($_POST['date_Employment']);

    //Encrypt data from form
    $enc_patient_Name = encryptthis($patient_full_Name, $key);
    $enc_patient_birth_Date = encryptthis($patient_birth_Date, $key);
    $enc_reason_Admission = encryptthis($reason_Admission, $key);

    $query = "INSERT INTO patient_List (patient_ID, patient_Name, room_Number, birth_Date, reason_Admission, admission_Status, nurse_ID, assistance_Status, gloves_ID) 
    VALUES (NULL, '$enc_patient_Name','$room_Number','$enc_patient_birth_Date', '$enc_reason_Admission', '$admission_Status', '$nurse_ID', '$assistance_Status', $device_Assigned)";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Catagory Added Successfully";
        header('Location: PatientsList.php');
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: PatientsList.php');
        exit(0);
    }
}

if(isset($_POST['edit']))
{
    $patient_ID = $_POST['patient_ID'];
    $patient_first_Name = $_POST['patient_first_Name'];
    $patient_last_Name = $_POST['patient_last_Name'];
    $patient_full_Name = $patient_first_Name.", ".$patient_last_Name;
    $room_Number = $_POST['room_Number'];
    $patient_birth_Date = $_POST['patient_birth_Date'];
    $reason_Admission = $_POST['reason_Admission'];
    $admission_Status = $_POST['admission_Status'];
    $nurse_ID = $_POST['nurse_ID'];
    $assistance_Status = $_POST['assistance_Status'];
    $device_Assigned = $_POST['gloves_ID'];
    //$password = sha1($_POST['password']);

    //Encrypt data from form
    $enc_patient_Name = encryptthis($patient_full_Name, $key);
    $enc_patient_birth_Date = encryptthis($patient_birth_Date, $key);
    $enc_reason_Admission = encryptthis($reason_Admission, $key);

        $query="UPDATE patient_List SET patient_Name ='$enc_patient_Name', room_Number='$room_Number', birth_Date='$enc_patient_birth_Date', reason_Admission='$enc_reason_Admission', 
        admission_Status='$admission_Status', nurse_ID='$nurse_ID', assistance_Status='$assistance_Status', gloves_ID='$device_Assigned' WHERE patient_ID='$patient_ID'";
        $query_run = mysqli_query($con, $query);

        if($query_run)
        {
            
           
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: PatientsList.php');
            exit(0);
        }
        else
        {
            $_SESSION['message'] = "Someting Went Wrong !";
            header('Location: PatientsList.php');
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
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
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
            <li class="nav-item">
                <a onclick="showSnackbar('redirect to nurses list page')" class="nav-link" href="../Nurses List/NursesList.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Nurses List</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item active">
                <a onclick="showSnackbar('redirect to patients list page')" class="nav-link" href="PatientsList.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Patients List</span></a>
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php  

                            ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                
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
                    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
                            <a onclick="showSnackbar('add nurse')" href= "AddPatient.php" class="btn btn-primary float-end">Add</a>
                        </div>
                        <div class="card-body">
                           
                            <div class="table-responsive"> 

                             <?php
                                            $count =0;
                                            $sql = "SELECT * FROM patient_List";
                                            $result = mysqli_query($con, $sql);
                                            if (mysqli_num_rows($result) > 0) {
                                                echo "";
                                                
                                                ?>
                                
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Patient ID</th>
                                            <th>Patient Name</th>
                                            <th>Room Number</th>
                                            <th>Age</th>
                                            <th>Reason for Admission</th>
                                            <th>Admission Status</th>
                                            <th>Assigned Nurse ID</th>
                                            <th>Assistance Status</th>
                                            <th>Device Assigned ID</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Patient ID</th>
                                            <th>Patient Name</th>
                                            <th>Room Number</th>
                                            <th>Age</th>
                                            <th>Reason for Admission</th>
                                            <th>Admission Status</th>
                                            <th>Assigned Nurse ID</th>
                                            <th>Assistance Status</th>
                                            <th>Device Assigned ID</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                            <?php
                                                while($row = mysqli_fetch_array($result)) 
                                                {   
                                                    $count = $count + 1;
                                                
                                                //Decrypt data from db
                                                $dec_patient_Name = decryptthis($row['patient_Name'], $key);
                                                $dec_patient_birth_Date = decryptthis($row['birth_Date'], $key);
                                                echo $dec_patient_birth_Date;
                                                //date in mm/dd/yyyy format; or it can be in other formats as well
                                                $birthDate = $dec_patient_birth_Date;
                                                //explode the date to get month, day and year
                                                $birthDate = explode("-", $birthDate);
                                                //get age from date or birthdate
                                                $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
                                                    ? ((date("Y") - $birthDate[0]) - 1)
                                                    : (date("Y") - $birthDate[0]));

                                                $dec_reason_Admission = decryptthis($row['reason_Admission'], $key);
                                            ?>
                                       
                                            <tr>
                                            <td><?php echo $row['patient_ID']; ?></td>
                                            <td><?php echo $dec_patient_Name ?></td>
                                            <td><?php echo $row['room_Number']; ?></td>
                                            <td><?php echo $patient_Age ?></td>
                                            <td><?php echo $dec_reason_Admission ?></td>
                                            <td><?php echo $row['admission_Status']; ?></td>
                                            <td><?php echo $row['nurse_ID']; ?></td>
                                            <td><?php echo $row['assistance_Status']; ?></td>
                                            <td><?php echo $row['gloves_ID']; ?></td>
                                            <td>
                                                    <a onclick="showSnackbar('edit nurse')" href="EditPatient.php?patient_ID=<?= $row['patient_ID'] ?>" class="btn btn-info">Edit</a>
                                                
                                            </td>
                                            
                                            <td>
                                                    <form action="DeletePatient.php" method="POST">
                                                
                                                    <button onclick="showSnackbar('delete nurse')" type="submit" name="patientDelete" value="<?= $row['patient_ID'] ?>" class="btn btn-danger">Delete</a>
                                                    </form>
                                            </td>
                                            </tr>  
                                            <?php
                                               
                                                }
                                               
                        
                                            } 
                                            else 
                                            {
                                                echo "No Record Found";
                                            }
                                        ?>
                                       
                                        
                                    
                                    </tbody>
                                </table>
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
                    <a class="btn btn-primary" href="index.php?logout=true">Logout</a>
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
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    </script>
</body>

</html>