<?php
require_once('../../dbConnection/connection.php');

try {
    $sqlQuery1 = "SELECT nurse_ID FROM staff_List WHERE delete_at <= CURRENT_DATE";
    $result = mysqli_query($con, $sqlQuery1);

    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_array($result)){
            //Get records from staff_List where accounts are deactivated
            $nurse_ID = $row['nurse_ID'];

            $sqlQuery2 = "UPDATE staff_List_Trash SET deleted_at=CURRENT_DATE WHERE nurse_ID = $nurse_ID LIMIT 1";
            $result = mysqli_query($con, $sqlQuery2);
            
            $sqlQuery3 = "DELETE FROM staff_List WHERE nurse_ID = $nurse_ID AND activated = 0 LIMIT 1";
            $result = mysqli_query($con, $sqlQuery3);
        }
    }
}catch (PDOException $e){
    
}

?>