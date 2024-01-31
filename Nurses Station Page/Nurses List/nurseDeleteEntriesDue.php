<?php
require_once('../../dbConnection/connection.php');

try {
    $sqlQuery1 = "SELECT nurse_ID FROM staff_List WHERE delete_at <= CURRENT_DATE";
    $result = mysqli_query($con, $sqlQuery1);
    $nurseIDArray = array();
    
    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_array($result)){
            //Get records from staff_List where accounts are deactivated
            $nurse_ID = $row['nurse_ID'];
            array_push($nurseIDArray, $nurse_ID);
        }

        //Idk why pero I need to do this instead of rekta sa while loop
        //For each ID in the array, use them to update trash table & delete from main table
        foreach ($nurseIDArray as $ID) {
            $sqlQuery2 = "UPDATE staff_List_Trash SET deleted_at=CURRENT_DATE WHERE nurse_ID = $ID LIMIT 1";
            $result = mysqli_query($con, $sqlQuery2);
            
            $sqlQuery3 = "DELETE FROM staff_List WHERE nurse_ID = $ID AND activated = 0 LIMIT 1";
            $result = mysqli_query($con, $sqlQuery3);

            $sqlQuery4 = "DELETE FROM userLogin WHERE ID = $ID LIMIT 1";
            $result = mysqli_query($con, $sqlQuery3);
        }
    }
}catch (PDOException $e){
    
}
?>