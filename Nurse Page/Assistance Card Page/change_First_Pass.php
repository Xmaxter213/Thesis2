<?php
include('../../dbConnection/AES encryption.php');
require_once('../../dbConnection/connection.php');

// Check if password is set and not empty
if (isset($_POST['password']) && !empty($_POST['password'])) {
    $password = $_POST['password'];
    $nurseID = $_SESSION['idNUM'] ?? null;

    $verPass = 1;

    //$enc_password = encryptthis($password, $key);

    try {
        $query = "UPDATE userLogin SET password = ?, verifyPassword = ? WHERE ID = ?";
        $stmt_pass = $con->prepare($query);
        $stmt_pass->bind_param("sii", $password, $verPass, $nurseID);
        $stmt_pass->execute();
        $stmt_pass->close();

        $_SESSION['verifyPass'] = 1;
    } catch (Exception $e) {
        echo json_encode(array("status" => "error", "message" => "Database connection failed!"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Password is required."));
}
?>
