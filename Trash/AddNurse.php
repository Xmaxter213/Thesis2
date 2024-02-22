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
                <input type="text" name="nurse_first_Name" id="nurse_first_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Nurse's First Name" required title="Must only contain letters" oninput="updateEmailPassword()">
            </div>

            <div>
                <label>Nurse Last Name</label>
                <input type="text" name="nurse_last_Name" id="nurse_last_Name" required pattern="\S(.*\S)?[A-Za-z]+" class="form-control" placeholder="Enter Nurse's Last Name" required title="Must only contain letters" oninput="updateEmailPassword()">
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
                <input type="text" name="nurse_email" id="nurse_email" class="form-control" placeholder="Automatically generated" readonly>
                
                <label>Nurse's Password</label>
                <input type="text" name="nurse_password" id="nurse_password" class="form-control" placeholder="Automatically generated" readonly>
            </div>

           <script>
            function updateEmailPassword() {
                var firstName = document.getElementById('nurse_first_Name').value;
                var lastName = document.getElementById('nurse_last_Name').value;
                
                // Update email field
                document.getElementById('nurse_email').value = `${firstName.toLowerCase()}${lastName.toLowerCase()}@gmail.com`;

                // Update password field (you can modify this logic as needed)
                document.getElementById('nurse_password').value = `${firstName.toLowerCase()}${lastName.toLowerCase()}123`; 
            }
        </script>

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
            var today = new Date().toISOString().split('T')[0]; 
            document.getElementById("date_Employment").setAttribute("max", today);

            //Date picker filled required
            document.getElementById("date_Employment").required = true;
        </script>
        <div>
            <input type="hidden" name="activated" value=1>
            </div>
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
