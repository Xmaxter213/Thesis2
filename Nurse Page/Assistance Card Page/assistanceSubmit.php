<?php
require_once('../../dbConnection/connection.php');

// Initialize variables
$nurseID = $_SESSION['idNUM'] ?? null;
$patientID = $_POST['patientID'] ?? null;
$remarks = $_POST['remarks'] ?? null;
$currentDateTime = date('Y-m-d H:i:s');
$assist_status = NULL;

// Update arduino_Reports table
$stmt_reports = $con->prepare("UPDATE arduino_Reports 
    SET Assitance_Finished = ?, assistance_Given = ? 
    WHERE patient_ID = ? AND nurse_ID = ? 
    AND Assitance_Finished IS NULL LIMIT 1");
if ($stmt_reports) {
    $stmt_reports->bind_param("ssii", $currentDateTime, $remarks, $patientID, $nurseID);
    $stmt_reports->execute();
    $stmt_reports->close();
}

// Update patient_List table
$stmt_patient = $con->prepare("UPDATE patient_List SET assistance_Status = ? WHERE patient_ID = ?");
if ($stmt_patient) {
    $stmt_patient->bind_param("si", $assist_status, $patientID);
    $stmt_patient->execute();
    $stmt_patient->close();
}

// Check if any rows were affected
if ($con->affected_rows > 0) {
    echo "Update successful!";
} else {
    echo "No rows were updated.";
}
?>
