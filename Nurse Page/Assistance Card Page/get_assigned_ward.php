<?php
$nurseID = $_SESSION['idNUM'] ?? null;

// Include database connection file
require_once('../../dbConnection/connection.php');

if ($nurseID !== null) {

    $sql = "SELECT assigned_Ward FROM staff_List WHERE nurse_ID = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $nurseID);
    $stmt->execute();
    
    $stmt->bind_result($assignedWard);
    
    $stmt->fetch();

    $stmt->close();

    $_SESSION['assignedWard'] = $assignedWard;
} else {
}
?>
