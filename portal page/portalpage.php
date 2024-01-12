<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .portal-container {
            text-align: center;
        }

        .dropdown {
            padding: 10px;
            width: 100%;
            max-width: 300px; /* Set your desired max-width */
        }
    </style>
</head>
<body>

<div class="portal-container">
    <h1>Medical Portal</h1>
    
    <div class="dropdown">
        <select id="MedicalPortal" name="MedicalPortal" onchange="redirect()">
            <option value="" disabled selected>Select Hospital</option>
            <option value="MainHospital">MainHospital</option>
            <option value="HospitalB-Login">HospitalB-Login</option>
            <option value="HospitalA-Login">HospitalA-Login</option>
            <!-- Add more options as needed -->
        </select>
    </div>
</div>

<script>
    function redirect() {
        var selectedDepartment = document.getElementById("MedicalPortal").value;
        if (selectedDepartment) {
            // Replace the placeholder URLs with actual paths or URLs
            window.location.href = "../" + selectedDepartment + "/Login_new.php";
        }
    }
</script>

</body>
</html>
