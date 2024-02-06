<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
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
            <a onclick="showSnackbar('back')" href="../Patients List/PatientsList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">

    <form action ="PatientsList.php" method="POST" >
        <div>
            <label>Patient First Name</label>
            <input type="text" name="patient_first_Name" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Patient's First Name" required title="Must only contain letters">
        </div>

        <div>
            <label>Patient Last Name</label>
            <input type="text" name="patient_last_Name" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter Patient's Last Name" required title="Must only contain letters">
        </div>
        <div>
            <label>Room Number</label>
            <input type="text" class="form-control" name="room_Number" placeholder="Enter Room Number" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <br>
            <label>Patient Birth Date</label>
            <input type="date" id="patient_birth_Date" name="patient_birth_Date" min='01/01/1899' max='13/13/2000'/>
        </div>
        <script>
            //Make date today the max value
            var today = new Date().toISOString().split('T')[0];
            document.getElementById("patient_birth_Date").setAttribute("max", today);

            //Date picker filled required
            document.getElementById("patient_birth_Date").required = true;
        </script>
        <br>
        <div>
            <label>Reason for Admission</label>
            <input type="text" class="form-control" name="reason_Admission" placeholder="Enter Reason for Admission" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters & numbers"/>
        </div>
        <br>
        <div>
        <label>Admission Status</label>
        <select id="admission_Status" name="admission_Status">
            <option value="Admitted">Admitted</option>
            <option value="Discharged">Discharged</option>
        </select>
        </div>
        <br>
        <div>
            <?php 
                // retrieve selected results from database and display them on page
                $sqlNursesList = 'SELECT staff_List.nurse_ID, staff_List.nurse_Name, userLogin.status FROM staff_List JOIN userLogin WHERE staff_List.nurse_ID = userLogin.ID AND staff_List.activated = "1" AND userLogin.status = "Nurse"';
                $resultShiftSched = mysqli_query($con, $sqlNursesList);
                
                
                if (mysqli_num_rows($resultShiftSched) > 0) {
            ?>
            <label>Assigned Nurse</label>
            <select id="nurse_ID" name="nurse_ID">
                        <option value="">Not Assigned a Nurse</option>
                <?php
                    while ($row = mysqli_fetch_array($resultShiftSched)) {
                        //Decrypt name
                        $dec_nurse_Name = decryptthis($row["nurse_Name"], $key);

                        $concatenatedRow = $row["nurse_ID"] . " - " . $dec_nurse_Name;
                ?>
                        <option value="<?php echo $row["nurse_ID"];
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
            <input type="hidden" name="assistance_Status" value="NULL">
        </div>
        <div>
            <?php 
                // retrieve selected results from database and display them on page
                $sqlNursesList = 'SELECT device_ID FROM device_List';
                $resultDeviceIDs = mysqli_query($con, $sqlNursesList);
                
                
                if (mysqli_num_rows($resultDeviceIDs) > 0) {
            ?>
            <label>Device ID Assigned</label>
            <select id="device_Assigned" name="device_Assigned">
                        <option value="">Not Assigned a Device</option>
                <?php
                    while ($row = mysqli_fetch_array($resultDeviceIDs)) {
                ?>
                        <option value="<?php echo $row["device_ID"];
                        // The value we usually set is the primary key
                        ?>">
                            <?php echo $row["device_ID"];
                        ?>
                        </option>
                        <?php
                    }
                }?>
            </select>
        </div>
        <div>
            <input type="hidden" name="activated" value=1>
            </div>
        <div class = "col-md-12 mb-3">
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