<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');

// Check if the AJAX request contains the patientID
if(isset($_POST['patientID'])) {
    $patientID = $_POST['patientID'];

    // Update the assistance status in the database
    $updatedStatus = 'On The Way'; // Set the new status here
    $sql = "UPDATE patient_List SET assistance_Status = ? WHERE patient_ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $updatedStatus, $patientID);

    if ($stmt->execute()) {
        // Success
        echo json_encode(array('status' => 'success', 'message' => 'Status updated successfully'));
    } else {
        // Error
        echo json_encode(array('status' => 'error', 'message' => 'Error updating status'));
    }

    $stmt->close();
} else {
    // Invalid request
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request'));
}
?>
