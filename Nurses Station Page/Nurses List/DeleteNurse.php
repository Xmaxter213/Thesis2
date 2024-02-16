<?php

require_once('../../dbConnection/connection.php');
//include('message.php');

if(isset($_POST['nurseDelete']))
{
    $nurse_ID = $_POST['nurseDelete'];
    $reason_For_Deletion_Variable = 'reasonForDeletion' . $nurse_ID;
    $reasonForDeletion = $_POST[$reason_For_Deletion_Variable];
    
    //Deactivate account & put into trash
    $query1="UPDATE staff_List SET activated=0, delete_at = CURRENT_DATE + INTERVAL 3 DAY WHERE nurse_ID='$nurse_ID'";
    $query2 = "INSERT INTO staff_List_Trash (nurse_ID, deleted_at, reason_For_Deletion) VALUES ($nurse_ID, NULL, '$reasonForDeletion')";
    //$query = "DELETE FROM staff_List WHERE nurse_ID ='$nurse_ID'";//

    $query_run1 = mysqli_query($con, $query1);
    $query_run2 = mysqli_query($con, $query2);
    
    echo "Reason for deletion: $reasonForDeletion";
    
    if($query_run1 && $query_run2)
    {
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time) VALUES ('$userName', 'Deleted Nurse/Admin Account ID: $nurse_ID', '$currentDateTime')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Deleted Successfully";
            header('Location: NursesList.php');
            exit(0);
        } 
        else 
        {
            echo 'Error inserting logs: ' . mysqli_error($con2);
        }
        
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong!";
        header('Location: NursesList.php');
        exit(0);
    }

}