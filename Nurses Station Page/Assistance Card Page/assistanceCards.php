<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
?>

<?php
require_once('AssiscanceCardElement.php');


$sql = "SELECT patient_ID, patient_Name, room_Number, birth_Date, reason_Admission, admission_Status, nurse_ID, assistance_Status, gloves_ID FROM patient_List WHERE activated = 1 AND (assistance_Status = 'On the way' OR assistance_Status = 'Unassigned')";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        //deccrypt data from form
        $dec_patient_Name = decryptthis($row['patient_Name'], $key);
        $dec_nurse_birth_Date = decryptthis($row['birth_Date'], $key);
        $admissionReason = decryptthis($row['reason_Admission'], $key);

        //get age from date or birthdate
        $birthDate = explode("-", $dec_nurse_birth_Date);
        $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
            ? ((date("Y") - $birthDate[0]) - 1)
            : (date("Y") - $birthDate[0]));

        if ($patient_Age == -1){
            $patient_Age = 0;
        }

        if($row['assistance_Status'] == "Unassigned")
        {

            try {
                $message = "Patient: " . $dec_patient_Name . " needs help at room: " . $row['room_Number'];
                $phoneNumber = "09771408389";

                if ($message != null && $phoneNumber != null) {
                    // my local modem from my phone.
                    $url = "http://192.168.1.20:8090/SendSMS?username=Mawser&password=1234&phone=" . $phoneNumber . "&message=" . urlencode($message);
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

                    /*if ($response !== null && property_exists($response, 'status') && $response->status == 200) {
                        echo 'Message has been sent';
                    } else {
                        echo 'Technical Problem';
                    }*/
                }
            } catch (Exception $ex) {
                error_log("Exception: " . $ex->getMessage());
            }

        }
        assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $admissionReason, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $row['gloves_ID']);

    }
} else {
    echo "0";
}
?>