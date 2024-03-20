<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
?>

<?php
require_once('AssiscanceCardElement.php');
require_once('get_assigned_ward.php');

$name = isset($_SESSION['idNUM']) ? $_SESSION['idNUM'] : null;
$assignedWard = $_SESSION['assignedWard'];

//$sql = "SELECT * FROM patient_List";

$sql = "SELECT 
staff_List.nurse_ID, 
staff_List.contact_No, 
staff_List.assigned_Ward,
patient_List.patient_ID, 
patient_List.patient_Name, 
patient_List.room_Number, 
patient_List.birth_Date, 
patient_List.reason_Admission, 
patient_List.admission_Status, 
patient_List.assistance_Status, 
patient_List.gloves_ID,
arduino_Reports.assistance_Type,
arduino_Reports.date_Called,
COALESCE(call_counts.total_calls, 0) AS total_calls,
COALESCE(call_counts.ADL_calls, 0) AS ADL_calls,
COALESCE(call_counts.IMMEDIATE_calls, 0) AS IMMEDIATE_calls
FROM 
patient_List 
INNER JOIN 
staff_List ON patient_List.assigned_Ward = staff_List.assigned_Ward
LEFT JOIN
arduino_Reports ON patient_List.gloves_ID = arduino_Reports.device_ID
LEFT JOIN
(SELECT 
    `patient_ID`, 
    COUNT(*) AS `total_calls`,
    SUM(CASE WHEN `assistance_Type` = 'ADL' THEN 1 ELSE 0 END) AS `ADL_calls`,
    SUM(CASE WHEN `assistance_Type` = 'IMMEDIATE' THEN 1 ELSE 0 END) AS `IMMEDIATE_calls`
FROM 
    `arduino_Reports`
WHERE
    `assistance_Type` IN ('ADL', 'IMMEDIATE')
GROUP BY 
    `patient_ID`) AS call_counts ON patient_List.patient_ID = call_counts.patient_ID
WHERE 
patient_List.activated = 1 
AND patient_List.assigned_Ward = ?
AND staff_List.nurse_ID = ?
AND patient_List.assistance_Status = 'Unassigned'
AND patient_List.admission_Status = 'Admitted'
AND (arduino_Reports.assistance_Type = 'IMMEDIATE' AND arduino_Reports.Assitance_Finished IS NULL)
ORDER BY 
    arduino_Reports.date_Called ASC";

$stmt = $con->prepare($sql);
$stmt->bind_param("sd", $assignedWard, $name);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        //deccrypt data from form
        $dec_patient_Name = decryptthis($row['patient_Name'], $key);
        $dec_nurse_birth_Date = decryptthis($row['birth_Date'], $key);
        $admissionReason = decryptthis($row['reason_Admission'], $key);

        try{
            if (!empty($row['date_Called'])) {
                $dateCalled = new DateTime($row['date_Called']);
                $currentDateTime = new DateTime(); // Current datetime
                $interval = $currentDateTime->diff($dateCalled);
                
                if ($interval->days == 0) {
                    $diffString = $interval->format('%h:%i:%s');
                } else {
                    $diffString = $interval->format('%a days, %h:%i:%s');
                }
            } else {
                $diffString = "";
            }
        }catch (exception $e){
            echo "Invalid Format please call support";
        }

        //get age from date or birthdate
        $birthDate = explode("-", $dec_nurse_birth_Date);
        $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));

        if ($patient_Age == -1){
            $patient_Age = 0;
        }
        assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $admissionReason, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $row['assistance_Type'], $diffString, $diffString, $row['gloves_ID']);
        
    }
} else {
    echo "<h2>No Requests</h2>";
}
$stmt->close();
?>