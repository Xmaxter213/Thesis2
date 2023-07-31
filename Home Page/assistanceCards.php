<?php
    require_once('../dbConnection/connection.php');
?>

<?php
require_once('AssistanceCard.php');


$sql = "SELECT patient_ID, gloves_ID, patient_Name, room_Number, age, admission_Status, nurse_Name, assistanceStatus FROM patient_Information";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
while($row = $result->fetch_assoc()) 
{
    assistanceCard($row['patient_ID'], $row['gloves_ID'], $row['patient_Name'], $row['room_Number'], $row['age'], $row['admission_Status'], $row['nurse_Name'], $row['assistanceStatus']);
}
    

} 
else 
{
    echo "0";
}
?>