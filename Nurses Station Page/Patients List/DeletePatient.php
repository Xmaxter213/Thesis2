<?php

require_once('../../dbConnection/connection.php');
//include('message.php');

if(isset($_POST['patientDelete']))
{
    $patient_ID = $_POST['patientDelete'];

    $query = "DELETE FROM patient_List WHERE patient_ID ='$patient_ID'";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $_SESSION['message'] = "Catagory Deleted Successfully";
        header('Location: PatientsList.php');
        exit(0);
    }
    else
    {
        $_SESSION['message'] = "Someting Went Wrong!";
        header('Location: PatientsList.php');
        exit(0);
    }

}