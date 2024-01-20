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

    <?php 
    //include('message.php');
    ?>

    <div class="card">
        <div class="card-header">
            <h4> Add Nurse Staff
            <a onclick="showSnackbar('back')" href="NursesList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">

    <form action ="NursesList.php" method="POST" >
        <div>
            <label>Nurse First Name</label>
            <input type="text" name="nurse_first_Name" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's First Name" required title="Must only contain letters">
        </div>

        <div>
            <label>Nurse Last Name</label>
            <input type="text" name="nurse_last_Name" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters">
        </div>

        <div>
            <br>
            <label>Birth Date</label>
            <input type="date" id="nurse_birth_Date" name="nurse_birth_Date" min='01/01/1899' max='13/13/2000'/>
        </div>

        <script>
            //Make date today the max value
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("nurse_birth_Date").setAttribute("max", today);

            //Date picker filled required
            document.getElementById("nurse_birth_Date").required = true;
        </script>
        
        <div>
        <br>
        <div>
            <label>Shift Status</label>
            <select id="shift_Schedule" name="shift_Schedule">
                <option value="Morning Shift">Morning Shift, 6AM - 2PM</option>
                <option value="Afternoon Shift">Night Shift, 2PM - 10PM</option>
                <option value="Graveyard Shift">Graveyard Shift, 10PM - 6AM</option>
            </select>
        </div>
        <br>
        <div>
            <label>Employment Status</label>
            <select id="employment_Status" name="employment_Status">
                <option value="Employed">Employed</option>
                <option value="Unemployed">Unemployed</option>
            </select>
        </div>
        <br>
        <div>
            <label>Date of Employment</label>
            <input type="date" id="date_Employment" name="date_Employment" value="" min="2018-01-01" max="2030-12-31" />
        </div>
        <script>
            //Make date today the max value
            document.getElementById("date_Employment").setAttribute("max", today);

            //Date picker filled required
            document.getElementById("date_Employment").required = true;
        </script>

        <div class = "col-md-12 mb-3">

        <br>
        <button onclick="showSnackbar('add nurse')" type = "submit" class = "btn btn-primary" name = "add" >Add</button>
        </div>
    </form>

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>
    
    <script>
    function showSnackbar(msg) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        //Change text
        if (msg.includes('add nurse')) {
            document.getElementById("snackbar").innerHTML = "Adding new nurse data to database...";
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
