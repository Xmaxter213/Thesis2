<?php
require_once('../dbConnection/connection.php');
include('../dbConnection/AES encryption.php');

$hospital_ID = $_SESSION['selectedHospitalID'];

$email = $_POST['email'];
$password = $_POST['password'];

$enc_email = encryptthis($email, $key);
$enc_password = encryptthis($password, $key);

$sql = "SELECT * FROM userLogin WHERE email = ? AND password = ? AND hospital_ID = ? LIMIT 1";
$stmtselect = $con->prepare($sql);
$stmtselect->bind_param("sss", $enc_email, $enc_password, $hospital_ID);
$result = $stmtselect->execute();
$stmtselect->store_result();

if ($result) 
{
    if ($stmtselect->num_rows > 0) 
    {
        echo 'Successfully';
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

$sqlgetuserID = "SELECT userName, ID, verifyPassword, status FROM userLogin WHERE email = ? AND password = ? AND hospital_ID = ? LIMIT 1";
$getuserID = $con->prepare($sqlgetuserID);
$getuserID->bind_param("sss", $enc_email, $enc_password, $hospital_ID);
$database = $getuserID->execute();
$getuserID->store_result();

if ($database && $getuserID->num_rows > 0) {
    $getuserID->bind_result($userName, $ID, $verifyPassword, $userStatus);
    $getuserID->fetch();

    $dec_userName = decryptthis($userName, $key);
    $dec_userStatus = decryptthis($userStatus, $key);

    $_SESSION['userID'] = $dec_userName;  // Assuming userName is the correct field you want to store
    $_SESSION['idNUM'] = $ID;
    $_SESSION['verifyPass'] =$verifyPassword;
    $_SESSION['userStatus'] = $dec_userStatus;

    date_default_timezone_set('Asia/Manila');

    $currentDateTime = date("Y-m-d H:i:s");

    // Insert into superAdminLogs
    $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Login', '$currentDateTime', '$hospital_ID')";
    $query_run_logs = mysqli_query($con, $sqlAddLogs);


} 
else {
    echo 'Error getting userID';
}

$getuserID->close();
mysqli_close($con);
		

		
