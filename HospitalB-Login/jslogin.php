<?php
require_once('../dbConnection/connection.php');

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM userLogin WHERE email = ? AND password = ? LIMIT 1";
$stmtselect = $con->prepare($sql);
$stmtselect->bind_param("ss", $email, $password);
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

$sqlgetuserID = "SELECT userName FROM userLogin WHERE email = ? AND password = ? LIMIT 1";
$getuserID = $con->prepare($sqlgetuserID);
$getuserID->bind_param("ss", $email, $password);
$database = $getuserID->execute();
$getuserID->store_result();

if ($database && $getuserID->num_rows > 0) {
    $getuserID->bind_result($userName);
    $getuserID->fetch();

    $_SESSION['userID'] = $userName;  // Assuming userName is the correct field you want to store
} else {
    echo 'Error getting userID';
}

$getuserID->close();
mysqli_close($con);
		

		
