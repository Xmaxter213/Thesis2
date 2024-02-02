<?php
// process_extension.php

require_once('../dbConnection/connection2.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospitalID = $_POST['hospital_id'];
    $extensionDuration = $_POST['extension_duration'];

    // Get the current expiration date from the database
    $currentExpirationQuery = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = '$hospitalID'";
    $currentExpirationResult = mysqli_query($con2, $currentExpirationQuery);

    if ($currentExpirationResult && $row = mysqli_fetch_assoc($currentExpirationResult)) {
        $currentExpiration = new DateTime($row['Expiration']);
        // Add the selected extension duration in months
        $newExpiration = $currentExpiration->add(new DateInterval("P{$extensionDuration}M"));

        // Update the 'Expiration' column in the database
        $updateQuery = "UPDATE Hospital_Table SET Expiration = '{$newExpiration->format('Y-m-d')}' WHERE hospital_ID = '$hospitalID'";
        mysqli_query($con2, $updateQuery);

        // Update status based on expiration
        $currentDate = new DateTime();
        $interval = $currentDate->diff($newExpiration);

        if ($interval->format('%R%a') < 0) {
            // Update the hospital status to 'Expired' in the database
            $updateStatusQuery = "UPDATE Hospital_Table SET hospitalStatus = 'Expired' WHERE hospital_ID = '$hospitalID'";
        } else {
            // Set the status to 'Active' if the duration is greater than or equal to 0 days
            $updateStatusQuery = "UPDATE Hospital_Table SET hospitalStatus = 'Active' WHERE hospital_ID = '$hospitalID'";
        }

        mysqli_query($con2, $updateStatusQuery);

        // Close the database connection
        mysqli_close($con2);

        // Redirect back to your page or perform any other actions
        header('Location: index.php');
        exit;
    } else {
        // Handle error if unable to fetch the current expiration date
        echo "Error fetching current expiration date.";
    }
}
?>
