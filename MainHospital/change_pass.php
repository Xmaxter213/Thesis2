<?php
include('../dbConnection/AES encryption.php');
require_once('../dbConnection/connection.php');

$response = array();


if (isset($_POST['password']) && !empty($_POST['password'])) {
    $password = $_POST['password'];
    $userEmail = $_SESSION['userEmail'];


    $enc_password = encryptthis($password, $key);

    
    $query = "UPDATE userLogin SET password = ? WHERE email = ?";
    $stmt_pass = $con->prepare($query);
    $stmt_pass->bind_param("ss", $enc_password, $userEmail);
    
    if ($stmt_pass->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Password updated successfully.';
    } else {
        
        $response['status'] = 'error';
        $response['message'] = 'Failed to update password. Please try again.';
    }
    $stmt_pass->close();
} else {
    
    $response['status'] = 'error';
    $response['message'] = 'Password is required.';
}


header('Content-Type: application/json');
echo json_encode($response);
?>
