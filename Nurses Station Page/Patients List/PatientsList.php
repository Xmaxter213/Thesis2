<?php
require_once('../../dbConnection/connection.php');
//include('message.php');

//This code runs after the NursesList.php page i think
if(isset($_POST['add']))
{
    $patient_ID = $_POST['patient_ID'];
    $patient_Name = $_POST['patient_Name'];
    $room_Number = $_POST['room_Number'];
    $age = $_POST['age'];
    $reason_Admission = $_POST['reason_Admission'];
    $admission_Status = $_POST['admission_Status'];
    $nurse_Name = $_POST['nurse_Name'];
    $assistanceStatus = $_POST['assistanceStatus'];
    $deviceAssigned = $_POST['deviceAssigned'];
    //$date_Employment = sha1($_POST['date_Employment']);

    $query = "INSERT INTO patient_Information (patient_ID, patient_Name, room_Number, age, reason_Admission, admission_Status, nurse_Name, assistanceStatus, deviceAssigned) 
    VALUES (NULL,'$patient_ID', '$patient_Name','$room_Number','$age', '$reason_Admission', '$admission_Status', '$nurse_Name', '$assistanceStatus', '$deviceAssigned')";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Catagory Added Successfully";
        header('Location: NursesList.php');
        exit(0);
        showSnackbar('added');
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: NursesList.php');
        showSnackbar('error');
        exit(0);
    }
}

if(isset($_POST['edit']))
{
    $nurse_ID = $_POST['nurse_ID'];
    $nurse_Name = $_POST['nurse_Name'];
    $nurse_Age = $_POST['nurse_Age'];
    $shift_Status = $_POST['shift_Status'];
    $employment_Status = $_POST['employment_Status'];
    $date_Employment = $_POST['date_Employment'];
    //$password = sha1($_POST['password']);

        $query="UPDATE staff_List SET nurse_Name='$nurse_Name', nurse_Age ='$nurse_Age', shift_Status='$shift_Status', employment_Status='$employment_Status', date_Employment='$date_Employment' WHERE nurse_ID='$nurse_ID'";
        $query_run = mysqli_query($con, $query);

        if($query_run)
        {
            
           
            $_SESSION['message'] = "Catagory Updated Successfully";
            header('Location: NursesList.php');
            showSnackbar('edited');
            exit(0);
        }
        else
        {
            $_SESSION['message'] = "Someting Went Wrong !";
            header('Location: NursesList.php');
            showSnackbar('error');
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

    <title>F.O.O.D - Tables</title>

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
                <div class="sidebar-brand-text mx-3">F.O.O.D</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

    
            

            <!-- Nav Item - Tables -->
            <li class="nav-item active">
                <a class="nav-link" href="NursesList.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Nurses List</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a class="nav-link" href="../Nurses List/NursesList.php">
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
                            <a onclick="showSnackbar('add nurse')" href= "AddNurse.php" class="btn btn-primary float-end">Add</a>
                        </div>
                        <div class="card-body">
                           
                            <div class="table-responsive"> 

                             <?php
                                            $count =0;
                                            $sql = "SELECT * FROM staff_List";
                                            $result = mysqli_query($con, $sql);
                                            if (mysqli_num_rows($result) > 0) {
                                                echo "";
                                                
                                                ?>
                                
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nurse ID</th>
                                            <th>Nurse Name</th>
                                            <th>Nurse Age</th>
                                            <th>Shift Status</th>
                                            <th>Employment Status</th>
                                            <th>Date of Employment</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Nurse ID</th>
                                            <th>Nurse Name</th>
                                            <th>Nurse Age</th>
                                            <th>Shift Status</th>
                                            <th>Employment Status</th>
                                            <th>Date of Employment</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                            <?php
                                                    while($row = mysqli_fetch_array($result)) 
                                                    {   
                                                        $count = $count + 1;
                                            
                                            ?>
                                       
                                            <tr>
                                            <td><?php echo $row['nurse_ID'];?></td>
                                            <td><?php echo $row['nurse_Name'];?></td>
                                            <td><?php echo $row['nurse_Age'];?></td>
                                            <td><?php echo $row['shift_Status'];?></td>
                                            <td><?php echo $row['employment_Status'];?></td>
                                            <td><?php echo $row['date_Employment'];?></td>
                                            <td>
                                                
                                                    <a onclick="showSnackbar('edit nurse')" href="EditNurse.php?nurse_ID=<?= $row['nurse_ID'] ?>" class="btn btn-info">Edit</a>
                                                
                                            </td>
                                            
                                            <td>
                                                    <form action="DeleteNurse.php" method="POST">
                                                
                                                    <button onclick="showSnackbar('delete nurse')" type="submit" name="nurseDelete" value="<?= $row['nurse_ID'] ?>" class="btn btn-danger">Delete</a>
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
        }

        // Add the "show" class to DIV
        x.className = "show";

        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    </script>
</body>

</html>