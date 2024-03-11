<?php
require_once('../dbConnection/connection.php');

if (isset($_SESSION['selectedHospitalID']))
{
    header("location: ../MainHospital/Login_new.php");
}

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

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .card {
            width: 200px;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .card img {
            max-width: 100%;
            max-height: 50px; /* Set the desired height for the logo */
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="portal-container">
    <h1>Medical Portal</h1>
    
    <div class="card-container">
        <?php 
            // retrieve selected results from the database and display them on the page
            $sqlHospital = 'SELECT * FROM Hospital_Table';
            $resultHospital = mysqli_query($con, $sqlHospital); 

            if (mysqli_num_rows($resultHospital) > 0) {
                while ($row = mysqli_fetch_array($resultHospital)) {
                    $hospitalID = $row["hospital_ID"];
        ?>
        <div class="card" onclick="selectHospital('<?php echo $hospitalID; ?>')">
            <?php 
                // You can adjust the path to the logo based on your setup
                $logoPath = '../LOGO FOLDER/' . $row['hospital_Logo'];
            ?>
            <img src="<?php echo $logoPath; ?>" alt="Hospital Logo">
            <h3><?php echo $row["hospitalName"]; ?></h3>
            <!-- You can add more details here if needed -->
        </div>
        <?php
                }
            }
        ?>
    </div>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="hospitalForm">
        <input type="hidden" id="selectedHospital" name="Hospital_Table" value="">
    </form>
</div>

<script>
    function selectHospital(hospitalID) {
        document.getElementById("selectedHospital").value = hospitalID;
        document.getElementById("hospitalForm").submit();
    }
</script>

</body>
</html>
