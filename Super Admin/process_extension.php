<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once('../dbConnection/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospitalID = $_POST['hospital_ID'];
    $extensionDuration = $_POST['extension_duration'];

    // Get the current expiration date from the database using prepared statement
    $currentExpirationQuery = "SELECT Expiration, hospitalName FROM Hospital_Table WHERE hospital_ID = ?";
    $stmt = mysqli_prepare($con, $currentExpirationQuery);
    mysqli_stmt_bind_param($stmt, 's', $hospitalID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $currentExpiration, $hospitalName);
        mysqli_stmt_fetch($stmt);
        
        $currentExpirationDateTime = new DateTime($currentExpiration);
        $newExpirationDateTime = clone $currentExpirationDateTime;
        $newExpirationDateTime->add(new DateInterval("P{$extensionDuration}M"));

        $updateQuery = "UPDATE Hospital_Table SET Expiration = ?, hospitalStatus = ? WHERE hospital_ID = ?";
        $status = $newExpirationDateTime < new DateTime() ? 'Expired' : 'Active';
        $stmt = mysqli_prepare($con, $updateQuery);
        mysqli_stmt_bind_param($stmt, 'sss', $newExpirationDateTime->format('Y-m-d'), $status, $hospitalID);
        mysqli_stmt_execute($stmt);

        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();                                            
            $mail->Host       = 'smtp.elasticemail.com';                     
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'j4ishere@gmail.com';                     
            $mail->Password   = 'A02F3F4222553D746B478EC9E43E48624D90'; 
            $mail->Port       = 2525;

            $mail->setFrom('j4ishere@gmail.com', 'Helping Hand');
            $mail->addAddress('boholbryan25@gmail.com', 'Recipient Name');
            $mail->isHTML(true);
            $mail->Subject = 'Subscription Update';
            $mail->Body    = "Hello {$hospitalName},<br><br>We're pleased to inform you that your subscription has been extended.<br><br>Your subscription is now extended up until: {$newExpirationDateTime->format('Y-m-d')}.<br><br>Thank you for choosing our Helping Hand service!<br><br>Best regards,<br>Helping Hand";

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        mysqli_close($con);
        header('Location: index.php');
        exit;
    } else {
        echo "Error fetching current expiration date.";
    }
}
?>
