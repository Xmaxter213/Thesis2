<?php
require_once('../dbConnection/connection.php');
include('../dbConnection/AES encryption.php');

$hospital_ID = $_SESSION['selectedHospitalID'];

$email = $_POST['email'];
$input_password = $_POST['password'];

$sql = "SELECT password, userName, ID, verifyPassword, status FROM userLogin WHERE email = ? AND hospital_ID = ? LIMIT 1";
$stmtselect = $con->prepare($sql);
$stmtselect->bind_param("ss", $email, $hospital_ID);
$result = $stmtselect->execute();
$stmtselect->store_result();

if ($result) 
{
    if ($stmtselect->num_rows > 0) 
    {
        $stmtselect->bind_result($password, $userName, $ID, $verifyPassword, $userStatus);
        $stmtselect->fetch();
        $password = decryptthis($password, $key);
        $dec_userName = decryptthis($userName, $key);
        $dec_userStatus = decryptthis($userStatus, $key);

        if($input_password === $password)
        {
            $_SESSION['userID'] = $dec_userName;  // Assuming userName is the correct field you want to store
            $_SESSION['idNUM'] = $ID;
            $_SESSION['verifyPass'] =$verifyPassword;
            $_SESSION['userStatus'] = $dec_userStatus;

            date_default_timezone_set('Asia/Manila');

            $currentDateTime = date("Y-m-d H:i:s");

            // Insert into superAdminLogs
            $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$dec_userName', 'Login', '$currentDateTime', '$hospital_ID')";
            $query_run_logs = mysqli_query($con, $sqlAddLogs);
            echo 'Successfully';

        }
        else
        {
            echo'Wrong Password';
        }

        
        
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
		

		
