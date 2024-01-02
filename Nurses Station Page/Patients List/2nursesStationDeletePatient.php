<?php

require_once('../dbConnection/connection.php');
//include('message.php');

if(isset($_POST['nurseDelete']))
{
    $nurse_ID = $_POST['nurseDelete'];

    $query = "DELETE FROM staff_List WHERE nurse_ID ='$nurse_ID'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Catagory Deleted  Successfully";
        header('Location: add_admin.php');
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong !";
        header('Location: add_admin.php');
        exit(0);
    }

}