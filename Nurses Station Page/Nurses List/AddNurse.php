<?php
require_once('../../dbConnection/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../css/styles.css" rel="stylesheet" />
    
</head>
<body>
    <div class="container-fluid px-4">

    <div class="row mt-4">
    <div class ="col-md-12">

    <?php //include('message.php');?>

    <div class="card">
        <div class="card-header">
            <h4> Add Nurse Staff
            <a href="NursesList.php" class="btn btn-danger float-end" >BACK</a>
            </h4>
        </div> 
        <div class="card-body">

    <form action ="NursesList.php" method="POST" >
        <div>
            <label>Nurse Name</label>
            <input type="text" name="nurse_Name"  class="form-control" placeholder="Enter Nurse Name" required>
        </div>
        <div>
            <label>Nurse Age</label>
            <input type="text" id="nurse_Age" class="form-control" name="nurse_Age" placeholder="Enter Nurse Age" required pattern ="[0-9]+" title="Must only contain numbers"/>
            <!--<input type="text" id="username" class="form-control" name="username" placeholder="Enter Nurse Age" required pattern ="\S(.*\S)?[A-Za-z0-9]+" title="Must only contain letters and numbers"/> -->
        </div>
        <br>
        <div>
            <label>Shift Status</label>
            <select id="shift_Status" name="shift_Status">
                <option value="On Shift">On Shift</option>
                <option value="Off Shift">Off Shift</option>
            </select>
        </div>
        <br>
        <div>
            <label>Employment Status</label>
            <select id="employment_Status" name="employment_Status">
                <option value="Employed">Employed</option>
                <option value="Unemployed">Unemployed</option>
            </select>
        </div>
        <br>
        <div>
            <label>Date of Employment</label>
            <input type="date" id="start" name="date_Employment" value="" min="2018-01-01" max="2030-12-31" />
        </div>

        <div class = "col-md-12 mb-3">

        <br>
        <button type = "submit" class = "btn btn-primary" name = "add" >Add</button>
        </div>
    </form>


</div>
</div>
</div>
</div>
</div>
</body>
</html>
