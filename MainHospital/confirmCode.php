<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $getCode = $_POST["code"];
    $hospitalID = $_SESSION['selectedHospitalID'];
    $userEmail = $_SESSION['userEmail'];

    // Prepare and execute the SQL query
    $codeQuery = "SELECT code FROM userLogin WHERE email = ? AND hospital_ID = ?";
    $stmtCode = $con->prepare($codeQuery);
    $stmtCode->bind_param("si", $userEmail, $hospitalID);
    $stmtCode->execute();

    // Check if the query executed successfully
    if ($stmtCode) {
        $resultCode = $stmtCode->get_result();

        // Check if the query returned any rows
        if ($resultCode->num_rows > 0) {
            $row = $resultCode->fetch_assoc();
            $code = $row['code'];
            
            // Compare the code from POST with the code from the database
            if ($getCode === $code) {
                $response['success'] = true;
                $response['message'] = "Code Accepted.";
            } else {
                $response['success'] = false;
                $response['message'] = "Code does not match.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No matching records found.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Error executing the query: " . $stmtCode->error;
    }
} else {
    // Handle incorrect POST request
    $response['success'] = false;
    $response['message'] = "Invalid request.";
}

echo json_encode($response);
?>
