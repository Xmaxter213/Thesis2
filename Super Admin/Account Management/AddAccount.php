<?php
require_once('../../dbConnection/connection.php');

//The functions for the encryption
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">
    
</head>
<body>
    <div class="container-fluid px-4">

    <div class="row mt-4">
    <div class ="col-md-12">

    <?php //include('message.php');?>

    <div class="card">
        <div class="card-header">
            <h4> Add Admin Account
            <a onclick="showSnackbar('back')" href="Account List.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">

    <form action ="Account List.php" method="POST" >
        <div>
            <label>User Name</label>
            <input type="text" name="userName" required pattern ="\S(.*\S)?[A-Za-z]+"  class="form-control" placeholder="Enter User Name" required title="Must only contain letters">
        </div>

        <div>
            <label>Email</label>
            <input type="text" name="email"  class="form-control" placeholder="Enter Email" r>
        </div>
        <div>
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Enter Password" />
        </div>
        <div>
            <label>Status</label>
            <input type="text" class="form-control" name="status" readonly value="Super Admin" />
        </div>
        <br>
        <div class = "col-md-12 mb-3">
        <br>
        <button onclick="showSnackbar('add Account')" type = "submit" class = "btn btn-primary" name = "add" >Add</button>
        </div>
    </form>

    <!-- The actual snackbar -->
    <div id="snackbar">Some text some message..</div>
    
    <script>
    function showSnackbar(msg) {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");

        //Change text
        if (msg.includes('add Account')) {
            document.getElementById("snackbar").innerHTML = "Adding new patient data to database...";
        } else if (msg.includes('back')) {
            document.getElementById("snackbar").innerHTML = "Going back to Admin Page...";
        } else if (msg.includes('error')) {
            document.getElementById("snackbar").innerHTML = "Error.. Please try again.";
        }

        // Add the "show" class to DIV
        x.className = "show";

        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    </script>

</div>
</div>
</div>
</div>
</div>
</body>
</html>