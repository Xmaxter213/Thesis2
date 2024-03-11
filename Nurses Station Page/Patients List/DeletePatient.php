<?php

require_once('../../dbConnection/connection.php');
//include('message.php');
$hospital_ID = $_SESSION['selectedHospitalID'];

if(isset($_POST['patientDelete']))
{
    $patient_ID = $_POST['patientDelete'];
    $reason_For_Deletion_Variable = 'reasonForDeletion' . $patient_ID;
    $reasonForDeletion = $_POST[$reason_For_Deletion_Variable];

    //Deactivate account & put into trash
    $query1="UPDATE patient_List SET activated=0, delete_at = CURRENT_DATE + INTERVAL 30 DAY WHERE patient_ID='$patient_ID'";
    $query2 = "INSERT INTO patient_List_Trash (patient_ID, hospital_ID, deleted_at, reason_For_Deletion) VALUES ($patient_ID, $hospital_ID, NULL, '$reasonForDeletion')";
    //$query = "DELETE FROM patient_List WHERE patient_ID ='$patient_ID'";
    
    $query_run1 = mysqli_query($con, $query1);
    $query_run2 = mysqli_query($con, $query2);

    echo "Reason for deletion: $reasonForDeletion";

    if($query_run1 && $query_run2)
    {
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Deleted Patient Account ID: $patient_ID', '$currentDateTime', '$hospital_ID')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Deleted Successfully";
            header('Location: PatientsList.php');
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
        header('Location: PatientsList.php');
        exit(0);
    }

}

if(isset($_POST['dischargedPatientDelete']))
{
    $patient_ID = $_POST['dischargedPatientDelete'];
    $reason_For_Deletion_Variable = 'reasonForDeletion' . $patient_ID;
    $reasonForDeletion = $_POST[$reason_For_Deletion_Variable];

    //Deactivate account & put into trash
    $query1="UPDATE patient_List SET activated=0, delete_at = CURRENT_DATE + INTERVAL 3 DAY WHERE patient_ID='$patient_ID'";
    $query2 = "INSERT INTO patient_List_Trash (patient_ID, hospital_ID, deleted_at, reason_For_Deletion) VALUES ($patient_ID, $hospital_ID, NULL, '$reasonForDeletion')";
    //$query = "DELETE FROM patient_List WHERE patient_ID ='$patient_ID'";
    
    $query_run1 = mysqli_query($con, $query1);
    $query_run2 = mysqli_query($con, $query2);

    echo "Reason for deletion: $reasonForDeletion";

    if($query_run1 && $query_run2)
    {
        $userName = $_SESSION['userID'];

        date_default_timezone_set('Asia/Manila');
        $currentDateTime = date("Y-m-d H:i:s");

        $sqlAddLogs = "INSERT INTO NurseStationLogs (User, Action, Date_Time, hospital_ID) VALUES ('$userName', 'Deleted Patient Account ID: $patient_ID', '$currentDateTime', '$hospital_ID')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);


         if ($query_run_logs) 
        {
            $_SESSION['message'] = "Catagory Deleted Successfully";
            header('Location: PatientsListDischarged.php');
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
        header('Location: PatientsListDischarged.php');
        exit(0);
    }

}