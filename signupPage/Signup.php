<?php
require_once('../dbConnection/connection.php');

if (isset($_POST['add'])) {
    $Subscriber_FirstName = $_POST['Subscriber_first_Name'];
    $Subscriber_LastName = $_POST['Subscriber_last_Name'];

    $Subscriber_Name = $Subscriber_FirstName . $Subscriber_LastName;

    $Hospital_Name = $_POST['Hospital_Name'];
    $Subscriber_Email = $_POST['Subscriber_email'];
    $Subscription = $_POST['Subscription_Duration'];

    date_default_timezone_set('Asia/Manila');
    $Creation_Date = date("Y-m-d H:i:s");

    $Expiration_Date = date("Y-m-d H:i:s", strtotime("+" . $Subscription . " months", strtotime($Creation_Date)));

    $sqladdHospital = "INSERT INTO Hospital_Table (Subscriber_Name, hospitalName, email, creation_Date, Expiration) VALUES ('$Subscriber_Name', '$Hospital_Name', '$Subscriber_Email', '$Creation_Date', '$Expiration_Date')";
    $query_run_addHospital = mysqli_query($con, $sqladdHospital);

    if ($query_run_addHospital) {
        $hospital_ID = mysqli_insert_id($con);
        $query = "INSERT INTO userLogin ( email, password, userName, status, code, verifyPassword, hospital_ID) 
        VALUES ('$Subscriber_Email','$Subscriber_Name', '$Subscriber_Name', 'Admin', '0', '0', '$hospital_ID')";
        $query_run = mysqli_query($con, $query);

        $queryStaff = "INSERT INTO staff_List (hospital_ID, nurse_Name, assigned_Ward, contact_No, nurse_Sex, nurse_birth_Date, shift_Schedule, employment_Status, date_Employment, activated) 
        VALUES ($hospital_ID, 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', '1')";
        $query_run = mysqli_query($con, $queryStaff);
    } else {
        echo 'Error inserting hospital: ' . mysqli_error($con);
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

    <title>Helping Hand - Tables</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- For table sorting -->
    <link rel="stylesheet" href="tablesort.css">

    <style>
        .subscription-cards-container {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        margin: 20px;
        }

        .subscription-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa; /* Light gray background color */
            width: 250px;
            margin: 10px;
            transition: transform 0.3s ease-in-out;
        }

        .subscription-card:hover {
            transform: scale(1.05); /* Hover effect to slightly increase size */
        }

        .subscription-card h2 {
            color: #007bff; /* Blue color for headings */
        }

        .subscription-card p {
            color: #6c757d; /* Gray color for additional information */
        }

        .subscription-card button {
            background-color: #28a745; /* Green color for the subscribe button */
            border: none;
            color: #fff; /* White text color */
            padding: 8px 15px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .subscription-card button:hover {
            background-color: #218838; /* Darker green color on hover */
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: white;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        @keyframes bubbleAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .bubble {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            animation: bubbleAnimation 1s ease-out;
        }
    </style>
    <!-- Bubble animation -->
    <script>
        function showBubbleAnimation(event) {
            const navLink = event.currentTarget;
            const rect = navLink.getBoundingClientRect();
            const bubble = document.createElement('span');
            bubble.classList.add('bubble');
            bubble.style.top = `${event.clientY - rect.top}px`;
            bubble.style.left = `${event.clientX - rect.left}px`;
            navLink.appendChild(bubble);
            setTimeout(() => {
                bubble.remove();
            }, 1000);
        }
    </script>

</head>


<body>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
                style="background-color: rgb(28,35,47);">
                        <!-- Topbar Navbar -->
                            <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                            aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small"
                                        placeholder="Search for..." aria-label="Search"
                                        aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item">
                        <a class="nav-link" href="../Online_Help/patient_List_Guide.php" target="_blank">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                Need Help?
                            </span>
                            <i class="bi bi-info-circle"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <?php

                                                                                        ?></span>
                            <img class="img-profile" src="../Assistance Card Page/./Images/logout.svg" style="filter: invert(1);">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="index.php?logout=true" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
            </nav>

            <!-- Subscription Cards Container -->
            <div class="subscription-cards-container">
                <div class="subscription-card">
                    <h2>1 Month Subscription</h2>
                    <p>Unlock premium features for a month.</p>
                    <p>$9.99/month</p>
                    <button class="btn btn-success" onclick="setSubscription(1); showAddHospitalModal();">Subscribe</button>
                </div>
            
                <div class="subscription-card">
                    <h2>3 Months Subscription</h2>
                    <p>Save more with a quarterly plan.</p>
                    <p>$24.99/3 months</p>
                    <button class="btn btn-success" onclick="setSubscription(3); showAddHospitalModal();">Subscribe</button>
                </div>
            
                <div class="subscription-card">
                    <h2>1 Year Subscription</h2>
                    <p>Best value for a year of access.</p>
                    <p>$89.99/year</p>
                    <button class="btn btn-success" onclick="setSubscription(12); showAddHospitalModal();">Subscribe</button>
                </div>
            </div>

            <!-- Add Hospital Modal -->
             <!-- Add hospital modal -->
             <div class="modal fade" id="addHospital" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Hospital</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="hideAddHospitalModal();">x</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div>
                                <label>Subscriber's First Name</label>
                                <input type="text" name="Subscriber_first_Name" id="Subscriber_first_Name" required
                                    pattern="\S(.*\S)?[A-Za-z]+" class="form-control"
                                    placeholder="Enter Subscriber's First Name" required title="Must only contain letters">
                            </div>
                            <br>

                            <div>
                                <label>Subscriber's Last Name</label>
                                <input type="text" name="Subscriber_last_Name" id="Subscriber_last_Name" required
                                    pattern="\S(.*\S)?[A-Za-z]+" class="form-control"
                                    placeholder="Enter Subscriber's Last Name" required title="Must only contain letters">
                            </div>
                            <br>

                            <div>
                                <label>Subscriber's Email</label>
                                <input type="text" name="Subscriber_email" id="Subscriber_email" class="form-control"
                                    placeholder="Enter Subscriber's Email">
                            </div>
                            <br>

                            <div>
                                <label>Hospital Name</label>
                                <input type="text" name="Hospital_Name" id="Hospital_Name" class="form-control"
                                    placeholder="Enter Hospital Name">
                            </div>
                            <br>

                            <div>
                                <?php
                                $sqlDuration = 'SELECT * FROM Subscription_Duration';
                                $resultDuration = mysqli_query($con, $sqlDuration);

                                if (mysqli_num_rows($resultDuration) > 0) {
                                    ?>
                                <label>Subscription Duration</label>
                                <select id="Subscription_Duration" name="Subscription_Duration">
                                    <?php
                                    while ($row = mysqli_fetch_array($resultDuration)) {
                                        ?>
                                    <option value="<?php echo $row["Duration_Month"]; ?>">
                                        <?php echo $row["Duration_Month"] . " Months"; ?>
                                    </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <?php
                                }
                                ?>
                            </div>

                            <br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="hideAddHospitalModal();">Close</button>
                            <button type="submit" class="btn btn-primary" name="add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Hide the modal initially
            $('#addHospital').modal('hide');
        });

        function setSubscription(duration) {
            document.getElementById('Subscription_Duration').value = duration;
        }

        function showAddHospitalModal() {
            // Show the modal using Bootstrap's modal method
            $('#addHospital').modal('show');
        }

        function hideAddHospitalModal() {
            // Show the modal using Bootstrap's modal method
            $('#addHospital').modal('hide');
        }

        function showSnackbar(message) {
            alert(message);
        }
    </script>

</body>

</html>