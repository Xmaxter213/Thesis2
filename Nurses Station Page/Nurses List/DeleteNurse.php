<?php

require_once('../../dbConnection/connection.php');
//include('message.php');

if(isset($_POST['nurseDelete']))
{
    $nurse_ID = $_POST['nurseDelete'];

    //Deactivate account & put into trash
    $query1="UPDATE staff_List SET activated=0, delete_at = CURRENT_DATE + INTERVAL 3 DAY WHERE nurse_ID='$nurse_ID'";
    $query2 = "INSERT INTO staff_List_Trash (nurse_ID, deleted_at, reason_For_Deletion) VALUES ($nurse_ID, NULL, 'test')";
    //$query = "DELETE FROM staff_List WHERE nurse_ID ='$nurse_ID'";

    $query_run1 = mysqli_query($con, $query1);
    $query_run2 = mysqli_query($con, $query2);

    if($query_run1 && $query_run2)
    {
        $_SESSION['message'] = "Catagory Deleted Successfully";
        header('Location: NursesList.php');
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong!";
        header('Location: NursesList.php');
        exit(0);
    }

}