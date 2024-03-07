<?php
require_once('../../dbConnection/connection.php');
include('../../dbConnection/AES encryption.php');

$nurseID = $_SESSION['idNUM'] ?? null;

if(isset($_POST['patientID'])) {
    $patientID = $_POST['patientID'];
    date_default_timezone_set('Asia/Manila');

    $updatedStatus = 'On The Way';
    $stmt_patient = $con->prepare("UPDATE patient_List SET assistance_Status = ? WHERE patient_ID = ?");
    $stmt_patient->bind_param("si", $updatedStatus, $patientID);
    $stmt_patient->execute();
    $stmt_patient->close();

    $stmt_gloves = $con->prepare("SELECT gloves_ID FROM patient_List WHERE patient_ID = ?");
    $stmt_gloves->bind_param("i", $patientID);
    $stmt_gloves->execute();
    $gloves_ID = $stmt_gloves->get_result()->fetch_assoc()['gloves_ID'];
    $stmt_gloves->close();

    $currentDateTime = date('Y-m-d H:i:s');
    $stmt_reports = $con->prepare("UPDATE arduino_Reports SET Nurse_Assigned_Status = ?, nurse_ID = ?, patient_ID = ? WHERE device_ID = ? AND Nurse_Assigned_Status IS NULL LIMIT 1");
    $stmt_reports->bind_param("siii", $currentDateTime, $nurseID, $patientID, $gloves_ID);
    $stmt_reports->execute();
    $stmt_reports->close();

    echo json_encode(array('status' => 'success', 'message' => 'Status updated successfully'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request'));
}
?>
