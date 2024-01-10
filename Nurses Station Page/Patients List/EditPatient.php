<?php
require_once('../../dbConnection/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

</head>
<body>
    <div class="container-fluid px-4">

    <div class="row mt-4">
    <div class ="col-md-12">

    <?php //include('message.php');?>

    <div class="card">
        <div class="card-header">
            <h4> Edit Patient
            <a onclick="showSnackbar('back')" href="PatientsList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">
<?php
    if(isset($_GET['patient_ID']))
    {
        $patient_ID = $_GET['patient_ID'];
        $edit ="SELECT * FROM patient_List WHERE patient_ID = '$patient_ID' LIMIT 1";
        $run = mysqli_query($con, $edit);

        if(mysqli_num_rows($run) > 0)
        {
            $row = mysqli_fetch_array($run);
            ?>
        
        <form action ="PatientsList.php" method="POST" >
        <div>
            <input type="hidden" name="patient_ID" value="<?=  $row['patient_ID'] ?>">
        </div>
        <div>
            <label>Patient Name</label>
            <input type="text" name="patient_Name" value="<?=  $row['patient_Name'] ?>" class="form-control" placeholder="Enter Patient Name" required>
        </div>
        <div>
            <label>Room Number</label>
            <input type="text" class="form-control" name="room_Number" value="<?=  $row['room_Number'] ?>" placeholder="Enter Room Number" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <label>Patient Age</label>
            <input type="text" class="form-control" name="age" value="<?=  $row['age'] ?>" placeholder="Enter Patient Age" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <label>Reason for Admission</label>
            <input type="text" class="form-control" name="reason_Admission" value="<?=  $row['reason_Admission'] ?>" placeholder="Enter Reason for Admission" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters & numbers"/>
        </div>
        <div>
            <label>Admission Status</label>
            <input type="text" class="form-control" name="admission_Status" value="<?=  $row['admission_Status'] ?>" placeholder="Enter Admission Status" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters"/>
        </div>
        <div>
            <label>Assigned Nurse Name</label>
            <input type="text" class="form-control" name="nurse_Name" value="<?=  $row['nurse_Name'] ?>" placeholder="Enter Assigned Nurse Name" required pattern ="\S(.*\S)?[A-Za-z]+" title="Must only contain letters"/>
        </div>
        <div>
            <label>Assistance Status</label>
            <input type="text" class="form-control" name="assistance_Status" value="<?=  $row['assistance_Status'] ?>" placeholder="Enter Assistance Status" required pattern ="\S(.*\S)?[A-Za-z]+" title="Must only contain letters"/>
        </div>
        <div>
            <label>Device ID Assigned</label>
            <?php 
            $device_Assigned_Variable = NULL;
            if ($row['device_Assigned'] != NULL)
            {
                $device_Assigned_Variable == $row['device_Assigned'];
            }
            ?>
            <input type="text" class="form-control" name="device_Assigned" value="<?=  $device_Assigned_Variable ?>" placeholder="Enter Assistance Status" required pattern ="[0-9]+" title="Must only numbers"/>
        </div>
        <br>
        <button onclick="showSnackbar('save patient')" type = "submit" class = "btn btn-primary" name = "edit" >Save</button>
        </div>
    </form>
<?php
}
        else
        {
            ?>
            <h4>No Record Found</h4>
            <?php
        }
    }
    

?>
<!-- The actual snackbar -->
<div id="snackbar">Some text some message..</div>       
    
    <script>
    function showSnackbar(msg) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        //Change text
        if (msg.includes('save patient')) {
            document.getElementById("snackbar").innerHTML = "Updating nurse data...";
        } else if (msg.includes('back')) {
            document.getElementById("snackbar").innerHTML = "Going back to Admin Page...";
        } else if (msg.includes('error')) {
            document.getElementById("snackbar").innerHTML = "Error.. Please try again.";
        }

        // Add the "show" class to DIV
        x.className = "show";

        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    </script>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
                                            