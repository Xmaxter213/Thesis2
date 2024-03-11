<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
?>

<?php
require_once('./AssiscanceCardElement.php');
require_once('./get_Assigned_Ward_Admin.php');

$name = isset($_SESSION['idNUM']) ? $_SESSION['idNUM'] : null;
$assignedWard = $_SESSION['assignedWard'];

// Function to send SMS
function sendSMS2($message2, $phoneNumber2)
{
    $url2 = "http://172.16.79.30:8090/SendSMS?username=Mawser&password=1234&phone=" . $phoneNumber2 . "&message=" . urlencode($message2);
    $curl2 = curl_init($url2);
    curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
    $curl_response2 = curl_exec($curl2);

    if ($curl_response2 === false) {
        $info2 = curl_getinfo($curl2);
        curl_close($curl2);
        error_log('Error occurred: ' . var_export($info2, true));
    }

    curl_close($curl2);

    $response = json_decode($curl_response2);
}

// Retrieve SMS setting value from cookie
$smsSetting = isset($_COOKIE['smsSetting']) ? $_COOKIE['smsSetting'] : 'Off';

$sqlAdl = "SELECT 
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
patient_List.gloves_ID 
FROM 
patient_List 
INNER JOIN 
staff_List ON patient_List.assigned_Ward = staff_List.assigned_Ward
LEFT JOIN
arduino_Reports ON patient_List.gloves_ID = arduino_Reports.device_ID
WHERE 
patient_List.activated = 1 
AND patient_List.assigned_Ward = ?
AND staff_List.nurse_ID = ?
AND (patient_List.assistance_Status = 'On the way' OR patient_List.assistance_Status = 'Unassigned')
AND patient_List.admission_Status = 'Admitted'
AND (arduino_Reports.assistance_Type = 'ADL' AND arduino_Reports.Assitance_Finished IS NULL)";

$stmt = $con->prepare($sqlAdl);
$stmt->bind_param("sd", $assignedWard, $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "";
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        //decrypt data from form
        $dec_patient_Name = decryptthis($row['patient_Name'], $key);
        $dec_nurse_birth_Date = decryptthis($row['birth_Date'], $key);
        $admissionReason = decryptthis($row['reason_Admission'], $key);
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
                $message2 = "Patient: " . $dec_patient_Name . " needs help at room: " . $row['room_Number'];
                $phoneNumber2 = $nurse_contact;

                if ($message2 != null && $phoneNumber2 != null) {
                    sendSMS($message2, $phoneNumber2);
                }
            } catch (Exception $ex) {
                error_log("Exception: " . $ex->getMessage());
            }
        }

        assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $admissionReason, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $row['gloves_ID'], $nurse_contact, $row['assigned_Ward']);
    }
} else {
    echo "<h2>No Requests</h2>";
}
?>