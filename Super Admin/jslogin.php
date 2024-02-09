<?php
require_once('../dbConnection/connection2.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM superAdminAccounts WHERE email = ? AND password = ? LIMIT 1";
$stmtselect = $con2->prepare($sql);
$stmtselect->bind_param("ss", $email, $password);
$result = $stmtselect->execute();
$stmtselect->store_result();

if ($result) 
{
    if ($stmtselect->num_rows > 0) 
    {
        $sqlgetuserID = "SELECT userName FROM superAdminAccounts WHERE email = ? AND password = ? LIMIT 1";
        $getuserID = $con2->prepare($sqlgetuserID);
        $getuserID->bind_param("ss", $email, $password);
        $database = $getuserID->execute();
        $getuserID->store_result();

        if ($database && $getuserID->num_rows > 0) 
        {
            $getuserID->bind_result($userName);
            $getuserID->fetch();

            $_SESSION['userID'] = $userName;  // Assuming userName is the correct field you want to store

            date_default_timezone_set('Asia/Manila');

            $currentDateTime = date("Y-m-d H:i:s");

            // Insert into superAdminLogs
            $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$userName', 'Login', '$currentDateTime')";
            $query_run_logs = mysqli_query($con2, $sqlAddLogs);

            if ($query_run_logs) {
                echo 'Successfully';
            } else {
                echo 'Error inserting logs: ' . mysqli_error($con2);
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



mysqli_close($con2);
		

		
