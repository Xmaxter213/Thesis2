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
            <h4> Add Nurse Staff
            <a onclick="showSnackbar('back')" href="PatientsList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">

    <form action ="PatientsList.php" method="POST" >
        <div>
            <label>Patient Name</label>
            <input type="text" name="patient_Name"  class="form-control" placeholder="Enter Patient Name" required>
        </div>
        <div>
            <label>Room Number</label>
            <input type="text" class="form-control" name="room_Number" placeholder="Enter Room Number" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <label>Patient Age</label>
            <input type="text" class="form-control" name="age" placeholder="Enter Patient Age" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <label>Reason for Admission</label>
            <input type="text" class="form-control" name="reason_Admission" placeholder="Enter Reason for Admission" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters & numbers"/>
        </div>
        <div>
            <label>Admission Status</label>
            <input type="text" class="form-control" name="admission_Status" placeholder="Enter Admission Status" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters"/>
        </div>
        <div>
            <label>Assigned Nurse Name</label>
            <input type="text" class="form-control" name="nurse_Name" placeholder="Enter Assigned Nurse Name" required pattern ="\S(.*\S)?[A-Za-z]+" title="Must only contain letters"/>
        </div>
        <br>
        <div>
            <label>Assistance Status</label>
            <select id="assistance_Status" name="assistance_Status">
                <option value="Unassigned">Unassigned</option>
            </select>
        </div>
        <br>
        <div>
            <label>Device ID Assigned</label>
            <input type="text" class="form-control" name="device_Assigned" placeholder="Enter Assistance Status" required pattern ="[0-9]+" title="Must only numbers"/>
        </div>
        <br>
        <button onclick="showSnackbar('add patient')" type = "submit" class = "btn btn-primary" name = "add" >Add</button>
        </div>
    </form>

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>
    
    <script>
    function showSnackbar(msg) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        //Change text
        if (msg.includes('add patient')) {
            document.getElementById("snackbar").innerHTML = "Adding new patient data to database...";
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
