<?php
require_once('../dbConnection/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["code"])) {
    $getcode = $_POST["code"];
    $userEmail = $_SESSION['userEmail'];


    // Get the current expiration date from the database using prepared statement
    $checkemailQuery = "SELECT code FROM userLogin WHERE email = ?";
    $stmtEmail = $con->prepare($checkemailQuery);
    $stmtEmail->bind_param("s", $userEmail);
    $stmtEmail->execute();
    $result = $stmtEmail->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $code = $row['code'];
        if($getcode === $code)
        {
            $response['success'] = true;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "code does not match.";
    }

    // Close the statement
    $stmtEmail->close();
} else {
    $response['success'] = false;
    $response['message'] = "code not provided or invalid request.";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>
