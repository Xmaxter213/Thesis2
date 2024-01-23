<?php
    require_once('../dbConnection/connection.php');

    //The functions for the encryption
    include('../dbConnection/AES encryption.php');
?>

<?php
require_once('AssistanceCard.php');


$sql = "SELECT patient_ID, patient_Name, room_Number, birth_Date, admission_Status, nurse_ID, assistance_Status, gloves_ID FROM patient_List WHERE assistance_Status = 'On the way' OR assistance_Status = 'Unassigned'";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
while($row = $result->fetch_assoc()) 
{
    //deccrypt data from form
    $dec_patient_Name = decryptthis($row['patient_Name'], $key);
    $dec_nurse_birth_Date = decryptthis($row['birth_Date'], $key);

    //get age from date or birthdate
    $birthDate = explode("-", $dec_nurse_birth_Date);
    $patient_Age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
        ? ((date("Y") - $birthDate[0]) - 1)
        : (date("Y") - $birthDate[0]));

    

    assistanceCard($row['patient_ID'], $dec_patient_Name, $row['room_Number'], $patient_Age, $row['admission_Status'], $row['nurse_ID'], $row['assistance_Status'], $row['gloves_ID']);
}
    

} 
else 
{
    echo "0";
}
?>