<?php
require_once('dbConnection/connection.php');

if (isset($_POST['add'])) {
    $Subscriber_FirstName = $_POST['Subscriber_first_Name'];
    $Subscriber_LastName = $_POST['Subscriber_last_Name'];

    $Subscriber_Name = $Subscriber_FirstName . $Subscriber_LastName;

    $Hospital_Name = $_POST['Hospital_Name'];
    $Subscriber_Email = $_POST['Subscriber_email'];
    $Subscription = $_POST['Subscription_Duration'];

    date_default_timezone_set('Asia/Manila');
    $Creation_Date = date("Y-m-d H:i:s");

    $Expiration_Date = date("Y-m-d H:i:s", strtotime("+" . $Subscription . " months", strtotime($Creation_Date)));

    $sqladdHospital = "INSERT INTO Hospital_Table (Subscriber_Name, hospitalName, email, creation_Date, Expiration) VALUES ('$Subscriber_Name', '$Hospital_Name', '$Subscriber_Email', '$Creation_Date', '$Expiration_Date')";
    $query_run_addHospital = mysqli_query($con, $sqladdHospital);

    if ($query_run_addHospital) {
        $hospital_ID = mysqli_insert_id($con);
        $query = "INSERT INTO userLogin ( email, password, userName, status, verifyPassword, hospital_ID) 
        VALUES ('$Subscriber_Email','$Subscriber_Name', '$Subscriber_Name', 'Admin', '0', '$hospital_ID')";
        $query_run = mysqli_query($con, $query);

        $queryStaff = "INSERT INTO staff_List (hospital_ID, nurse_Name, assigned_Ward, contact_No, nurse_Sex, nurse_birth_Date, shift_Schedule, employment_Status, date_Employment, activated) 
        VALUES ($hospital_ID, 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', '1')";
        $query_run = mysqli_query($con, $queryStaff);
    } else {
        echo 'Error inserting hospital: ' . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .subscription-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px;
            text-align: center;
            background-color: #fff;
            width: 200px;
        }

        h2 {
            color: #333;
        }

        p {
            color: #777;
        }

        button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
    <title>Subscription Options</title>
</head>

<body>

    <div class="subscription-card">
        <h2>1 Month Subscription</h2>
        <p>$9.99/month</p>
        <button class="btn btn-primary" onclick="setSubscription(1); showAddHospitalModal();">Subscribe</button>
    </div>

    <div class="subscription-card">
        <h2>3 Months Subscription</h2>
        <p>$24.99/3 months</p>
        <button class="btn btn-primary" onclick="setSubscription(3); showAddHospitalModal();">Subscribe</button>
    </div>

    <div class="subscription-card">
        <h2>1 Year Subscription</h2>
        <p>$89.99/year</p>
        <button class="btn btn-primary" onclick="setSubscription(12); showAddHospitalModal();">Subscribe</button>
    </div>

    <!-- Add hospital modal -->
     <!-- Add hospital modal -->
     <div class="modal fade" id="addHospital" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Hospital</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div>
                            <label>Subscriber's First Name</label>
                            <input type="text" name="Subscriber_first_Name" id="Subscriber_first_Name" required
                                pattern="\S(.*\S)?[A-Za-z]+" class="form-control"
                                placeholder="Enter Subscriber's First Name" required title="Must only contain letters">
                        </div>
                        <br>

                        <div>
                            <label>Subscriber's Last Name</label>
                            <input type="text" name="Subscriber_last_Name" id="Subscriber_last_Name" required
                                pattern="\S(.*\S)?[A-Za-z]+" class="form-control"
                                placeholder="Enter Subscriber's Last Name" required title="Must only contain letters">
                        </div>
                        <br>

                        <div>
                            <label>Subscriber's Email</label>
                            <input type="text" name="Subscriber_email" id="Subscriber_email" class="form-control"
                                placeholder="Enter Subscriber's Email">
                        </div>
                        <br>

                        <div>
                            <label>Hospital Name</label>
                            <input type="text" name="Hospital_Name" id="Hospital_Name" class="form-control"
                                placeholder="Enter Hospital Name">
                        </div>
                        <br>

                        <div>
                            <?php
                            $sqlDuration = 'SELECT * FROM Subscription_Duration';
                            $resultDuration = mysqli_query($con, $sqlDuration);

                            if (mysqli_num_rows($resultDuration) > 0) {
                                ?>
                            <label>Subscription Duration</label>
                            <select id="Subscription_Duration" name="Subscription_Duration">
                                <?php
                                while ($row = mysqli_fetch_array($resultDuration)) {
                                    ?>
                                <option value="<?php echo $row["Duration_Month"]; ?>">
                                    <?php echo $row["Duration_Month"] . " Months"; ?>
                                </option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php
                            }
                            ?>
                        </div>

                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="add">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Hide the modal initially
            $('#addHospital').modal('hide');
        });

        function setSubscription(duration) {
            document.getElementById('Subscription_Duration').value = duration;
        }

        function showAddHospitalModal() {
            // Show the modal using Bootstrap's modal method
            $('#addHospital').modal('show');
        }

        function showSnackbar(message) {
            alert(message);
        }
    </script>

</body>

</html>