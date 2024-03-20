<?php
include('../dbConnection/AES encryption.php');
require_once('../dbConnection/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = $_POST["email"];
    $hospitalID = $_SESSION['selectedHospitalID'];
    $_SESSION['userEmail'] = $email;

    // Get the current expiration date from the database using prepared statement
    $checkemailQuery = "SELECT ID FROM userLogin WHERE email = ?";
    $stmtEmail = $con->prepare($checkemailQuery);
    $stmtEmail->bind_param("s", $email);
    $stmtEmail->execute();
    $result = $stmtEmail->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $randomCode = generateRandomCode();
        $response['success'] = true;
        $id = $row['ID'];

        $phoneQeuery = "SELECT contact_No FROM staff_List WHERE nurse_ID = ?";
        $stmtPhone = $con->prepare($phoneQeuery);
        $stmtPhone->bind_param("i", $id);
        $stmtPhone->execute();
        $resultphone = $stmtPhone->get_result();
        if ($resultphone->num_rows > 0) {
            $codeloginQuery = "UPDATE userLogin SET code = ? WHERE ID = ? and email = ?";
            $stmtCode = $con->prepare($codeloginQuery);
            $stmtCode->bind_param("sis", $randomCode, $id, $email);
            $stmtCode->execute();
            $stmtCode->close();

            $row2 = $resultphone->fetch_assoc();
            $dec_contact = decryptthis($row2['contact_No'], $key);
            $message = "your confirmation code is:  " . $randomCode;
            $phoneNumber = $dec_contact;
            if ($message != null && $phoneNumber != null) {
                sendSMS($message, $dec_contact);
            }
        }

        $stmtPhone->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Email does not exist.";
    }

    // Close the statement
    $stmtEmail->close();
} else {
    $response['success'] = false;
    $response['message'] = "Email not provided or invalid request.";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

function generateRandomCode($length = 8)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $max)];
    }
    return $randomString;
}

function sendSMS($message, $phoneNumber)
{   
    try{
            
        $url = "http://192.168.1.20:8090/SendSMS?username=Mawser&password=1234&phone=" . $phoneNumber . "&message=" . urlencode($message);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            error_log('Error occurred: ' . var_export($info, true));
        }

        curl_close($curl);

        $response = json_decode($curl_response);
    }catch (Exception $e) {
        echo "Phone Modem does not have a load or cant send due to different sims. " . $e->getMessage();
    }
}
?>
