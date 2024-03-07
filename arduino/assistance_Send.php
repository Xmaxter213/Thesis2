<?php

if (isset($_GET["devID"]) && isset($_GET["assistance"]) && isset($_GET["bpm"]) && isset($_GET["spo2"]) && isset($_GET["batteryPercent"])) {
    $ID = $_GET["devID"];
    $bpm = $_GET["bpm"];
    $spo2 = $_GET["spo2"];
    $assistance = $_GET["assistance"];
    $batteryPercent = $_GET["batteryPercent"];
    $sensor_type = NULL;  

    $servername = "db4free.net";
    $username = "userthesis2";
    $password = "dbThesis123";
    $dbname = "dbthesis2";

    date_default_timezone_set('Asia/Manila');

    if($assistance == 1) {
        $assist_Type = 'ADL';
    } else if($assistance == 2) {
        $assist_Type = 'IMMEDIATE';
    } else if($assistance == 3){
        $assist_Type = 'IMMEDIATE';
        $sensor_type = 'BPM';
    } else if($assistance == 4){
        $assist_Type = 'IMMEDIATE';
        $sensor_type = 'SPO2';
    } else if($assistance == 5){
        $assist_Type = 'ADL';
        $sensor_type = 'BATTERY';
    }

    $assiststatus = 'Unassigned';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $currentDateTime = date("Y-m-d H:i:s");

    // Use prepared statements to prevent SQL injection
    $checkExistenceSql = "SELECT COUNT(*) FROM arduino_Reports WHERE device_ID = ? AND Assitance_Finished IS NULL";
    
    // Prepare and bind the statement for checking existence
    $checkExistenceStmt = $conn->prepare($checkExistenceSql);
    $checkExistenceStmt->bind_param("s", $ID);
    $checkExistenceStmt->execute();
    $checkExistenceStmt->bind_result($count);
    $checkExistenceStmt->fetch();
    $checkExistenceStmt->close();

    if ($count > 0) {
        // If the record already exists, update it
        $updateSql = "UPDATE arduino_Reports SET assistance_Type=?, critical_sensors=?, pulse_rate=?, oxygen_levels=?, battery_percent=? WHERE device_ID=? AND Assitance_Finished IS NULL LIMIT 1";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssdddd", $assist_Type, $sensor_type, $bpm, $spo2, $batteryPercent, $ID);
        

        if ($updateStmt->execute()) {
            echo "Record updated successfully";
        } else {
            // Log the error instead of echoing directly
            error_log("Error updating record: " . $conn->error);
            echo "Error updating record";
        }

        $updateStmt->close();
    } else {
        // If the record does not exist, insert a new one
        $insertSql = "INSERT INTO arduino_Reports (device_ID, assistance_Type, critical_sensors, date_Called, pulse_rate, oxygen_levels, battery_percent) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("dsssddd", $ID, $assist_Type, $sensor_type, $currentDateTime, $bpm, $spo2, $batteryPercent);

        if ($insertStmt->execute()) {
            echo "New record created successfully";

            // Check if record exists in patient_List before updating
            $patientcheckExistenceSql = "SELECT COUNT(*) FROM patient_List WHERE gloves_ID = ? AND assistance_Status IS NULL";
            $patientcheckExistenceStmt = $conn->prepare($patientcheckExistenceSql);
            $patientcheckExistenceStmt->bind_param("s", $ID);
            $patientcheckExistenceStmt->execute();
            $patientcheckExistenceStmt->bind_result($countpatient);
            $patientcheckExistenceStmt->fetch();
            $patientcheckExistenceStmt->close();

            if ($countpatient > 0) {
                $patientupdateSql = "UPDATE patient_List SET assistance_Status=? WHERE gloves_ID=? AND activated = 1 LIMIT 1";
                $patientupdateStmt = $conn->prepare($patientupdateSql);
                $patientupdateStmt->bind_param("sd", $assiststatus, $ID);
                if ($patientupdateStmt->execute()) {
                    echo "Patient record updated successfully";
                } else {
                    // Log the error instead of echoing directly
                    error_log("Error updating patient record: " . $conn->error);
                    echo "Error updating patient record";
                }
                $patientupdateStmt->close();
            }
        } else {
            // Log the error instead of echoing directly
            error_log("Error creating new record: " . $insertStmt->error);
            echo "Error creating new record";
        }

        $insertStmt->close();
    }

    $conn->close();
} else {
    echo "devID or assist type";
}

?>
