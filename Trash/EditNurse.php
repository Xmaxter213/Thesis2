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
            <h4> Edit Admin
            <a onclick="showSnackbar('back')" href="NursesList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">
<?php
    if(isset($_GET['nurse_ID']))
    {
        $nurse_ID = $_GET['nurse_ID'];
        $edit ="SELECT * FROM staff_List WHERE nurse_ID = '$nurse_ID' LIMIT 1";
        $run = mysqli_query($con, $edit);

        if(mysqli_num_rows($run) > 0)
        {
            $row = mysqli_fetch_array($run);

            //Decrypt data
            $dec_nurse_Name = decryptthis($row['nurse_Name'], $key);
            //explode the date to get month, day and year
            $exploded_nurse_Name = explode(", ", $dec_nurse_Name);
            $nurse_last_Name = $exploded_nurse_Name[0];
            $nurse_first_Name = $exploded_nurse_Name[1];
            $dec_nurse_Sex = decryptthis($row['nurse_Sex'], $key);
            $dec_nurse_birth_Date = decryptthis($row['nurse_birth_Date'], $key);
            $shift_Schedule = $row['shift_Schedule'];
            $dec_date_Employment = decryptthis($row['date_Employment'], $key);
            ?>

        

        <form action ="NursesList.php" method="POST" enctype="multipart/form-data">
            <div>
                <input type="hidden" name="nurse_ID" value="<?=  $row['nurse_ID'] ?>">
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
                        ?>" <?php if ($shift_Schedule == $row2["work_Shift"]) echo ' selected="selected"'; ?>>
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
                <label>Employment Status</label>
                <select id="employment_Status" name="employment_Status" value="<?=  $dec_employment_Status ?>">
                    <option value="Employed" <?php if ($row['employment_Status']  == 'Employed') echo ' selected="selected"'; ?>>Employed</option>
                    <option value="Unemployed" <?php if ($row['employment_Status'] == 'Unemployed') echo ' selected="selected"'; ?>>Unemployed</option>
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

            <div class = "col-md-12 mb-3">

            <br>
            <button onclick="showSnackbar('save nurse')" type = "submit" class = "btn btn-primary" name = "edit" >Save</button>
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
        if (msg.includes('save nurse')) {
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
                                            