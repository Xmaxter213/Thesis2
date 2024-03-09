<?php
require_once('../../dbConnection/connection.php');

// Initialize variables
$nurseID = $_SESSION['idNUM'] ?? null;
$patientID = $_POST['patientID'] ?? null;
$remarks = $_POST['remarks'] ?? null;
$currentDateTime = date('Y-m-d H:i:s');
date_default_timezone_set('Asia/Manila');

// Initialize variable to store errors
$errors = [];

// Update arduino_Reports table
$stmt_reports = $con->prepare("UPDATE arduino_Reports 
    SET Assitance_Finished = ?, assistance_Given = ? 
    WHERE patient_ID = ? AND nurse_ID = ? 
    AND Assitance_Finished IS NULL LIMIT 1");
if ($stmt_reports) {
    $stmt_reports->bind_param("ssii", $currentDateTime, $remarks, $patientID, $nurseID);
    if ($stmt_reports->execute()) {
        $stmt_reports->close();
    } else {
        $errors[] = $stmt_reports->error;
    }
} else {
    $errors[] = $con->error;
}

// Update patient_List table
$stmt_patient = $con->prepare("UPDATE patient_List SET assistance_Status = NULL WHERE patient_ID = ?");
if ($stmt_patient) {
    $stmt_patient->bind_param("i", $patientID);
    if ($stmt_patient->execute()) {
        $stmt_patient->close();
    } else {
        $errors[] = $stmt_patient->error;
    }
} else {
    $errors[] = $con->error;
}

// Check if any errors occurred
if (!empty($errors)) {
    // Output errors to the console
    echo json_encode(['errors' => $errors]);
} else {
}
?>
