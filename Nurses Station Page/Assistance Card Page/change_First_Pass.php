<?php
include('../../dbConnection/AES encryption.php');
require_once('../../dbConnection/connection.php');

// Check if password is set and not empty
if (isset($_POST['password']) && !empty($_POST['password'])) {
    $password = $_POST['password'];
    $nurseID = $_SESSION['idNUM'] ?? null;


    $enc_password = encryptthis($password, $key);
    $verPass = 1;

    try {
        $query = "UPDATE userLogin SET password = ?, verifyPassword = ? WHERE ID = ?";
        $stmt_pass = $con->prepare($query);
        $stmt_pass->bind_param("sii", $enc_password, $verPass, $nurseID);
        if ($stmt_pass->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Password updated successfully.';
            $_SESSION['verifyPass'] = 1;
        }
        $stmt_pass->close();
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to update password. Please try again.';
        echo json_encode(array("status" => "error", "message" => "Database connection failed!"));
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Password is required.';
    echo json_encode(array("status" => "error", "message" => "Password is required."));
}
?>
