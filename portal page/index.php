<?php
require_once('../dbConnection/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if Hospital ID is selected
    if(isset($_POST['Hospital_Table']) && !empty($_POST['Hospital_Table'])) {
        $selectedHospitalID = $_POST['Hospital_Table'];

        // Store the selected hospital ID in the session
        $_SESSION['selectedHospitalID'] = $selectedHospitalID;
        header("location: ../MainHospital/login_new.php");
    } else {
        echo "Please select a hospital.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .portal-container {
            text-align: center;
        }

        .dropdown {
            padding: 10px;
            width: 100%;
            max-width: 300px; /* Set your desired max-width */
        }
    </style>
</head>
<body>

<div class="portal-container">
    <h1>Medical Portal</h1>
    
    <div class="dropdown">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="hospitalForm">
            <?php 
                // retrieve selected results from the database and display them on the page
                $sqlHospital = 'SELECT * FROM Hospital_Table';
                $resultHospital = mysqli_query($con, $sqlHospital); 

                if (mysqli_num_rows($resultHospital) > 0) {
            ?>
            <label>Hospital Portal</label>
            <select id="Hospital_Table" name="Hospital_Table" onchange="submitForm()">
                <option value="" disabled selected>Select a Hospital</option>
                <?php
                    while ($row = mysqli_fetch_array($resultHospital)) {
                        $hospitalID = $row["hospital_ID"];
                ?>
                <option value="<?php echo $hospitalID; ?>">
                    <?php echo $row["hospitalName"]; ?>
                </option>
                <?php
                    }
                ?>
            </select>
            <?php
                }
            ?>
        </form>
    </div>
</div>

<script>
    function submitForm() {
        document.getElementById("hospitalForm").submit();
    }
</script>

</body>
</html>
