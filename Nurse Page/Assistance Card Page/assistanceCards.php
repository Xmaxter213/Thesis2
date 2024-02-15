<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
include('../../dbConnection/AES encryption.php');
?>

<?php
require_once('AssiscanceCardElement.php');

$name = isset($_SESSION['idNUM']) ? $_SESSION['idNUM'] : null;


$sql = "SELECT 
            staff_List.nurse_ID, 
            staff_List.contact_no, 
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
            staff_List ON patient_List.nurse_ID = staff_List.nurse_ID 
        WHERE 
            patient_List.activated = 1 
            AND patient_List.nurse_ID = '$name'
            AND (patient_List.assistance_Status = 'On the way' OR patient_List.assistance_Status = 'Unassigned')
            AND staff_List.nurse_ID = '$name';";
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
        assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $admissionReason, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $row['gloves_ID']);

    }
} else {
    echo "0";
}
?>