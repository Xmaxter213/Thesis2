<?php
require_once('../dbConnection/connection.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM superAdminAccounts WHERE email = ? AND password = ? AND status = 'Super Admin' LIMIT 1";
$stmtselect = $con->prepare($sql);
$stmtselect->bind_param("ss", $email, $password );
$result = $stmtselect->execute();
$stmtselect->store_result();

if ($result) 
{
    if ($stmtselect->num_rows > 0) 
    {
        $sqlgetuserID = "SELECT userName, status FROM superAdminAccounts WHERE email = ? AND password = ? AND status = 'Super Admin' LIMIT 1";
        $getuserID = $con->prepare($sqlgetuserID);
        $getuserID->bind_param("ss", $email, $password);
        $database = $getuserID->execute();
        $getuserID->store_result();

        if ($database && $getuserID->num_rows > 0) 
        {
            $getuserID->bind_result($userName, $status);
            $getuserID->fetch();

            $_SESSION['userID'] = $userName;  // Assuming userName is the correct field you want to store
            $_SESSION['userStatus'] = $status;

            date_default_timezone_set('Asia/Manila');

            $currentDateTime = date("Y-m-d H:i:s");

            // Insert into superAdminLogs
            $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Login', '$currentDateTime')";
            $query_run_logs = mysqli_query($con, $sqlAddLogs);

            if ($query_run_logs) {
                echo 'Successfully';
            } else {
                echo 'Error inserting logs: ' . mysqli_error($con);
            }
        } 
        else 
        {
            echo 'Error getting userID';
        }

        $getuserID->close();
    } 
    else 
    {
        echo 'Invalid Email or Password';
    }
} 
else 
{
    echo 'Error executing the query';
}

$stmtselect->close();



mysqli_close($con);
		

		
