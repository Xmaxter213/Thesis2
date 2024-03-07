<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
?>

<?php
require_once('AssiscanceCardElement.php');

// Function to send SMS
function sendSMS($message, $phoneNumber)
{
    $url = "http://172.16.79.30:8090/SendSMS?username=Mawser&password=1234&phone=" . $phoneNumber . "&message=" . urlencode($message);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);

    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        error_log('Error occurred: ' . var_export($info, true));
    }

    curl_close($curl);

    $response = json_decode($curl_response);
}

// Retrieve SMS setting value from cookie
$smsSetting = isset($_COOKIE['smsSetting']) ? $_COOKIE['smsSetting'] : 'Off';

$sql = "WITH PatientArduinoData AS (
    SELECT 
        patient_List.patient_ID, 
        MAX(patient_List.patient_Name) AS patient_Name, 
        MAX(patient_List.room_Number) AS room_Number, 
        MAX(patient_List.birth_Date) AS birth_Date, 
        MAX(patient_List.reason_Admission) AS reason_Admission, 
        MAX(patient_List.admission_Status) AS admission_Status, 
        MAX(patient_List.nurse_ID) AS nurse_ID, 
        MAX(patient_List.assistance_Status) AS assistance_Status, 
        MAX(patient_List.gloves_ID) AS patient_gloves_ID, 
        MAX(patient_List.activated) AS activated, 
        MAX(patient_List.delete_at) AS delete_at,  
        MAX(arduino_Reports.device_ID) AS patient_device_ID, 
        MAX(arduino_Reports.pulse_Rate) AS pulse_Rate, 
        MAX(arduino_Reports.battery_percent) AS battery_percent, 
        MAX(arduino_Reports.date_Called) AS date_called,
        staff_List.contact_No
    FROM 
        patient_List 
    INNER JOIN 
        arduino_Reports ON patient_List.gloves_ID = arduino_Reports.device_ID
    LEFT JOIN
        staff_List
    ON
        patient_List.nurse_ID = staff_List.nurse_ID 
    WHERE 
        patient_List.admission_Status = 'Admitted'
    GROUP BY
        patient_List.patient_ID
),
ArduinoReportsData AS (
    SELECT 
        `patient_ID`, 
        COUNT(*) AS `total_calls`,
        SUM(CASE WHEN `assistance_Type` = 'ADL' THEN 1 ELSE 0 END) AS `ADL_calls`,
        SUM(CASE WHEN `assistance_Type` = 'IMMEDIATE' THEN 1 ELSE 0 END) AS `IMMEDIATE_calls`
    FROM 
        `arduino_Reports`
    WHERE
        `assistance_Type` IN ('ADL', 'IMMEDIATE')
    GROUP BY 
        `patient_ID`
),
ReportsWithData AS (
    SELECT 
        `ID`, 
        `device_ID`, 
        `assistance_Type`, 
        `assistance_Given`, 
        `date_Called`, 
        TIMESTAMPDIFF(SECOND, `date_Called`, `Assitance_Finished`) AS `resolve_Time`, 
        `Nurse_Assigned_Status`, 
        TIMESTAMPDIFF(SECOND, `date_Called`, `Nurse_Assigned_Status`) AS `response_Time`, 
        `Assitance_Finished`, 
        `nurse_ID`, 
        `patient_ID` 
    FROM 
        `arduino_Reports`
)
SELECT 
    PAD.patient_ID,
    PAD.patient_Name,
    PAD.room_Number,
    PAD.birth_Date,
    PAD.reason_Admission,
    PAD.admission_Status,
    PAD.nurse_ID,
    PAD.assistance_Status,
    PAD.patient_gloves_ID,
    PAD.activated,
    PAD.delete_at,
    PAD.patient_device_ID,
    PAD.pulse_Rate,
    PAD.battery_percent,
    PAD.date_called,
    PAD.contact_No,
    RWDA.assistance_Type AS RWDA_assistance_Type,
    RWDB.assistance_Type AS RWDB_assistance_Type,
    MAX(ARD.total_calls) AS total_calls,
    MAX(ARD.ADL_calls) AS ADL_calls,
    MAX(ARD.IMMEDIATE_calls) AS IMMEDIATE_calls,
    MAX(CASE WHEN RWDA.assistance_Type = 'ADL' THEN RWDA.response_Time END) AS max_ADL_response_Time,
    MAX(CASE WHEN RWDA.assistance_Type = 'ADL' THEN RWDA.resolve_Time END) AS max_ADL_resolve_Time,
    MAX(CASE WHEN RWDB.assistance_Type = 'IMMEDIATE' THEN RWDB.response_Time END) AS max_IMMEDIATE_response_Time,
    MAX(CASE WHEN RWDB.assistance_Type = 'IMMEDIATE' THEN RWDB.resolve_Time END) AS max_IMMEDIATE_resolve_Time
FROM 
    PatientArduinoData PAD
LEFT JOIN 
    ArduinoReportsData ARD ON PAD.patient_ID = ARD.patient_ID
LEFT JOIN
    ReportsWithData RWDA ON PAD.patient_device_ID = RWDA.device_ID AND RWDA.assistance_Type = 'ADL'
LEFT JOIN
    ReportsWithData RWDB ON PAD.patient_device_ID = RWDB.device_ID AND RWDB.assistance_Type = 'IMMEDIATE'
WHERE 
    RWDB.assistance_Type = 'IMMEDIATE'
GROUP BY
    PAD.patient_ID,
    PAD.patient_Name,
    PAD.room_Number,
    PAD.birth_Date,
    PAD.reason_Admission,
    PAD.admission_Status,
    PAD.nurse_ID,
    PAD.assistance_Status,
    PAD.patient_gloves_ID,
    PAD.activated,
    PAD.delete_at,
    PAD.patient_device_ID,
    PAD.pulse_Rate,
    PAD.battery_percent,
    PAD.date_called,
    PAD.contact_No,
    RWDA.assistance_Type,
    RWDB.assistance_Type";

$result = $con->query($sql);
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        //decrypt data from form
        $dec_patient_Name = decryptthis($row['patient_Name'], $key);
        $dec_nurse_birth_Date = decryptthis($row['birth_Date'], $key);
        $admissionReason = decryptthis($row['reason_Admission'], $key);
        $dateStr = $row['date_called'];

        $nurse_contact = decryptthis($row['contact_No'], $key);

        //get age from date or birthdate
        $birthDate = explode("-", $dec_nurse_birth_Date);
        $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));

        if ($patient_Age == -1) {
            $patient_Age = 0;
        }

        if ($row['assistance_Status'] == "Unassigned" && $smsSetting == 'on') {
            try {
                $message = "Patient: " . $dec_patient_Name . " needs help at room: " . $row['room_Number'];
                $phoneNumber = $nurse_contact;

                if ($message != null && $phoneNumber != null) {
                    sendSMS($message, $phoneNumber);
                }
            } catch (Exception $ex) {
                error_log("Exception: " . $ex->getMessage());
            }
        }

        $gloves_ID = $row['gloves_ID'] ?? null;
        $device_ID = $row['device_ID'] ?? null;

        // Check if pulse_Rate is 100 or more
        $pulseRate = $row['pulse_Rate'];
        $pulse_Rate_Status = ($pulseRate >= 100) ? "Pulse Rate Critical at $dateStr" : "Normal Pulse";

        assistanceCard(
            $row['patient_ID'],
            $dec_patient_Name,
            $row['room_Number'],
            $patient_Age,
            $admissionReason,
            $row['admission_Status'],
            $row['nurse_ID'],
            $row['assistance_Status'],
            $gloves_ID,
            $device_ID,
            $row['ADL_calls'],
            $row['max_ADL_response_Time'],
            $row['IMMEDIATE_calls'],
            $row['max_IMMEDIATE_response_Time'],
            $row['pulse_Rate'],
            $row['date_called'],
            $pulse_Rate_Status,
            $nurse_contact
        );
    }
} else {
    echo "0";
}
?>