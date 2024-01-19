<?php 
	require_once('../dbConnection/connection.php');

	if(isset($_GET['logout']))
    {
        session_destroy();
        unset($_SESSION);
        header("location: ../MainHospital/login_new.php");
    }

	if(!isset($_SESSION['userID']))
    {
    	header("location: ../MainHospital/login_new.php");
    }
    else
    {
    	$status = $_SESSION['userStatus'];

        $name = $_SESSION['userID'];

        if($status === 'Admin')
        {
        	header("location: ../Home Page/index.php");
        }
	}

?>
<!<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
		<?php
			echo "Welcome, " . htmlspecialchars($name) . "!";

			echo '<br><a href="?logout=1">Logout</a>';
		?>
</body>
</html>