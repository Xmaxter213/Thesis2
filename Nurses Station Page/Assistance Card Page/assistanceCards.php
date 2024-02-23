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

$sql = "SELECT patient_List.patient_ID, patient_List.patient_Name, patient_List.room_Number, patient_List.birth_Date, 
patient_List.reason_Admission, patient_List.admission_Status, patient_List.nurse_ID, patient_List.assistance_Status, 
patient_List.gloves_ID AS patient_gloves_ID, patient_List.activated, patient_List.delete_at, arduino_Device_List.device_ID 
AS patient_device_ID, arduino_Device_List.ADL_Count, arduino_Device_List.ADL_Avg_Response, arduino_Device_List.immediate_Count, 
arduino_Device_List.immediate_Avg_Response, arduino_Device_List.assistance_Given, arduino_Device_List.nurses_In_Charge, 
arduino_Device_List.pulse_Rate, arduino_Device_List.battery_percent, arduino_Device_List.date_called 
FROM patient_List INNER JOIN arduino_Device_List ON patient_List.gloves_ID = arduino_Device_List.device_ID 
WHERE patient_List.admission_Status = 'Admitted' AND (patient_List.assistance_Status = 'On the way' OR assistance_Status = 'Unassigned')";

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
                $phoneNumber = "09771408389";

                if ($message != null && $phoneNumber != null) {
                    sendSMS($message, $phoneNumber);
                }
            } catch (Exception $ex) {
                error_log("Exception: " . $ex->getMessage());
            }
        }

        $gloves_ID = $row['gloves_ID'] ?? null;
        $device_ID = $row['device_ID'] ?? null;

        $timeFromDatabase = $row['ADL_Avg_Response'];
        $timeParts = explode(":", $timeFromDatabase);
        $totalSeconds = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];

        $timeFromDatabase2 = $row['immediate_Avg_Response'];
        $timeParts2 = explode(":", $timeFromDatabase2);
        $totalSeconds2 = ($timeParts2[0] * 3600) + ($timeParts2[1] * 60) + $timeParts2[2];

        // Check if pulse_Rate is 100 or more
        $pulseRate = $row['pulse_Rate'];
        $pulse_Rate_Status = ($pulseRate >= 100) ? "Pulse Rate Critical at $dateStr":"Normal Pulse";

        assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $admissionReason, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $gloves_ID,
        $device_ID, $row['ADL_Count'], $totalSeconds, $row['immediate_Count'], $totalSeconds2, $row['assistance_Given'], $row['nurses_In_Charge'], $row['pulse_Rate'], $row['date_called'], $pulse_Rate_Status);
    }
} else {
    echo "0";
}
?>
