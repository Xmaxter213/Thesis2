<?php
    require_once('../dbConnection/connection.php');
?>

<?php
require_once('AssistanceCard.php');


$sql = "SELECT patient_ID, patient_Name, room_Number, age, admission_Status, nurse_Name, assistance_Status, device_Assigned FROM patient_List WHERE assistance_Status = 'On the way' OR assistance_Status = 'Unassigned'";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    echo "";
    // output data of each row
while($row = $result->fetch_assoc()) 
{
    assistanceCard($row['patient_ID'], $row['patient_Name'], $row['room_Number'], $row['age'], $row['admission_Status'], $row['nurse_Name'], $row['assistance_Status'], $row['device_Assigned']);
}
    

} 
else 
{
    echo "0";
}
?>