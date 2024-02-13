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

    <!-- Eto yung from previous page, gonna use it for future reference nalang -->
    <a onclick="showSnackbar('edit nurse')" href="EditPatient.php?patient_ID=<?= $patient['patient_ID'] ?>" class="btn btn-info">Edit</a>

    <div class="card">
        <div class="card-header">
            <h4> Edit Patient
            <a onclick="showSnackbar('back')" href="../Patients List/PatientsList.php" class="btn btn-danger float-end" >BACK</a>
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

            //Decrypt data
            $dec_patient_Name = decryptthis($row['patient_Name'], $key);
            //explode the date to get month, day and year
            $exploded_patient_Name = explode(", ", $dec_patient_Name);
            $patient_first_Name = $exploded_patient_Name[0];
            $patient_last_Name = $exploded_patient_Name[1];

            $dec_patient_birth_Date = decryptthis($row['birth_Date'], $key);
            $dec_reason_Admision = decryptthis($row['reason_Admission'], $key);
            ?>
        
        <form action ="PatientsList.php" method="POST" >
        <div>
            <input type="hidden" name="patient_ID" value="<?=  $row['patient_ID'] ?>">
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
            <input type="text" class="form-control" name="room_Number" value="<?=  $row['room_Number'] ?>" placeholder="Enter Room Number" required pattern ="[0-9]+" title="Must only contain numbers"/>
        </div>
        <div>
            <br>
            <label>Birth Date</label>
            <input type="date" id="nurse_birth_Date" value="<?=  $dec_patient_birth_Date ?>" name="patient_birth_Date" min='01/01/1899' max='13/13/2000'/>
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
            <label>Reason for Admission</label>
            <input type="text" class="form-control" name="reason_Admission" value="<?=  $dec_reason_Admision ?>" placeholder="Enter Reason for Admission" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters & numbers"/>
        </div>
        <br>
        <div>
        <label>Admission Status</label>
        <select id="admission_Status" name="admission_Status" value="<?=  $row['admission_Status'] ?>">
            <option value="Admitted" <?php if ($row["admission_Status"] == "Admitted"){ echo "selected";}?>>Admitted</option>
            <option value="Discharged" <?php if ($row["admission_Status"] == "Discharged"){ echo "selected";}?>>Discharged</option>
        </select>
        </div>
        <br>
        <div>
            <?php
            // Check if patient has assigned nurse ID
            $nurse_Assigned_Variable = NULL;
            if ($row['nurse_ID'] != NULL)
            {
                $nurse_Assigned_Variable = $row['nurse_ID'];
            }

            // Retrieve selected results from database and display them on page
            $sqlNursesList = 'SELECT staff_List.nurse_ID, staff_List.nurse_Name, userLogin.status FROM staff_List JOIN userLogin WHERE staff_List.nurse_ID = userLogin.ID AND staff_List.activated = "1" AND userLogin.status = "Nurse"';;
            $resultNursesList = mysqli_query($con, $sqlNursesList);
            
            if (mysqli_num_rows($resultNursesList) > 0) { 
            ?>
            <label>Assigned Nurse ID</label>
            <select id="nurse_ID" name="nurse_ID">
                        <option value="">Not Assigned a Nurse</option>
                <?php
                    while ($row2 = mysqli_fetch_array($resultNursesList)) {
                        //Decrypt name
                        $dec_nurse_Name = decryptthis($row2["nurse_Name"], $key);

                        $concatenatedRow = $row2["nurse_ID"] . " - " . $dec_nurse_Name;
                ?>
                        <option value="<?php echo $row2["nurse_ID"]; ?>"
                        <?php
                        // The value we usually set is the primary key
                        if ($row2["nurse_ID"] == $nurse_Assigned_Variable){
                            echo "selected";
                        }?>>
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
        <label>Assistance Status</label>
            <select id="assistance_Status" name="assistance_Status" value="<?=  $row['assistance_Status'] ?>">
                <option value="Unassigned" <?php if ($row["assistance_Status"] == "Unassigned"){ echo "selected";}?>>Unassigned</option>
                <option value="On The Way" <?php if ($row["assistance_Status"] == "On The Way"){ echo "selected";}?>>On The Way</option>
            </select>
        </div>
        <br>
        <div>
            <?php
                // Check if patient has assigned device ID
                $device_Assigned_Variable = NULL;
                if ($row['gloves_ID'] != NULL)
                {
                    $device_Assigned_Variable = $row['gloves_ID'];
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