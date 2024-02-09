<?php

    require_once('dbConnection/connection.php');

    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION);
        header("location: MainHospital/login_new.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expired</title>
    <style>
        body {
            display: flex;
            flex-direction: column; /* Updated to column layout */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h1 {
            font-size: 2em;
            text-align: center;
            color: red; /* You can change the color to your preference */
            margin-bottom: 20px; /* Added margin below h1 */
        }

        button {
            padding: 10px 20px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div>
        <h1>SUBSCRIPTION EXPIRED</h1>
        <a class="btn btn-primary" href="?logout=true">Logout</a>
    </div>

</body>
</html>
