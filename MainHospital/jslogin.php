<?php
require_once('../dbConnection/connection.php');

$hospital_ID = $_SESSION['selectedHospitalID'];

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM userLogin WHERE email = ? AND password = ? AND hospital_ID = ? LIMIT 1";
$stmtselect = $con->prepare($sql);
$stmtselect->bind_param("sss", $email, $password, $hospital_ID);
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
$getuserID->bind_param("sss", $email, $password, $hospital_ID);
$database = $getuserID->execute();
$getuserID->store_result();

if ($database && $getuserID->num_rows > 0) {
    $getuserID->bind_result($userName, $ID, $verifyPassword, $userStatus);
    $getuserID->fetch();

    $_SESSION['userID'] = $userName;  // Assuming userName is the correct field you want to store
    $_SESSION['idNUM'] = $ID;
    $_SESSION['verifyPass'] =$verifyPassword;
    $_SESSION['userStatus'] = $userStatus;
} else {
    echo 'Error getting userID';
}

$getuserID->close();
mysqli_close($con);
		

		
