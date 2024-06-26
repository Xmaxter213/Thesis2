<?php
require_once('../dbConnection/connection.php');

if (isset($_SESSION['selectedHospitalID'])) {
    header("location: ../MainHospital/login_new.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if Hospital ID is selected
    if (isset($_POST['Hospital_Table']) && !empty($_POST['Hospital_Table'])) {
        $selectedHospitalID = $_POST['Hospital_Table'];

        // Store the selected hospital ID in the session
        $_SESSION['selectedHospitalID'] = $selectedHospitalID;
        header("location: ../MainHospital/login_new.php");
    } else {
        echo "Please select a hospital.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Medical Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- For card styles -->
    <link rel="stylesheet" href="./css/cardStyle.css">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">


    <!-- Your custom styles -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f4f4;
        }

        #content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #content {
            flex: 1;
        }

        .topbar {
            background-color: rgb(28, 35, 47);
        }

        .navbar-brand {
            color: white !important;
            /* Set the color to white */
            text-align: center;
            /* Center the text */
            width: 100%;
            /* Take up the full width of the navbar */
        }

        .navbar-brand h1 {
            margin: 0;
            /* Remove default margin */
        }

        .navbar-nav {
            margin-left: auto;
            /* Push the Admin word to the right */
        }

        .nav-link.align-middle {
            display: flex;
            align-items: center;
        }

        .navbar-nav .admin {
            color: white;
            font-size: 14px;
            margin-right: 15px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .card {
            width: 200px;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: pointer;
            text-align: center;
        }

        .card img {
            max-width: 100%;
            max-height: 50px;
            margin-bottom: 10px;
        }

        h1 {
            text-align: center;
        }
    </style>

</head>

<body>

    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
                <!-- Need Help Link -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../Nurses Station Page/Online_Help/online_Help.php" target="_blank">
                            <span class="nav-link">
                                Need&nbsp;Help?
                            </span>
                            <i class="bi bi-info-circle"></i>
                        </a>
                    </li>
                </ul>

                <!-- Medical Portal Brand -->
                <a class="navbar-brand" href="#">
                    <h1 class="m-0">Medical Portal</h1>
                </a>

                <!-- Navbar Toggler -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Menu Items -->
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow">
                            <!-- ... Your search dropdown code ... -->
                        </li>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item">
                            <!-- ... Your user information code ... -->
                        </li>
                        <!-- Admin Word -->
                        <li class="nav-item">
                            <a href="../signupPage/Signup.php" class="nav-link">Sign<span>-</span>Up</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link align-middle">|</span>
                        </li>
                        <li class="nav-item">
                            <a href="../Super Admin/Login_new.php" class="nav-link">Admin</a>
                        </li>
                    </ul>
                </div>
            </nav>


            <div class="card-container">

                <!-- <div class="ag-courses_item">
                    <div class="ag-courses-item_link">
                        <div class="ag-courses-item_bg"></div>
                        <div class="ag-courses-item_content">
                            <div class="ag-courses-item_title">Hello World</div>
                            <div class="ag-courses-item_image">
                                <img src="../LOGO FOLDER/ospital_Muntinlupa.jpg" alt="Image">
                            </div>
                        </div>
                    </div>
                </div> -->

                <?php
                // retrieve selected results from the database and display them on the page
                $sqlHospital = 'SELECT * FROM Hospital_Table';
                $resultHospital = mysqli_query($con, $sqlHospital);

                if (mysqli_num_rows($resultHospital) > 0) {
                    while ($row = mysqli_fetch_array($resultHospital)) {
                        $hospitalID = $row["hospital_ID"];
                        ?>
                        <div class="ag-courses_item" onclick="selectHospital('<?php echo $hospitalID; ?>')">
                            <div class="ag-courses-item_link">
                                <div class="ag-courses-item_bg"></div>
                                <div class="ag-courses-item_content">
                                    <div class="ag-courses-item_title">
                                        <?php echo $row["hospitalName"]; ?>
                                    </div>
                                    <div class="ag-courses-item_image">
                                        <?php
                                        // You can adjust the path to the logo based on your setup
                                        $logoPath = '../LOGO FOLDER/' . $row['hospital_Logo'];
                                        ?>
                                        <img src="<?php echo $logoPath; ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- You can add more details here if needed -->
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="hospitalForm">
                <input type="hidden" id="selectedHospital" name="Hospital_Table" value="">
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function selectHospital(hospitalID) {
            document.getElementById("selectedHospital").value = hospitalID;
            document.getElementById("hospitalForm").submit();
        }
    </script>

</body>

</html>