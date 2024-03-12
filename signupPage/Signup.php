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

    <!-- Additional CSS for the pricing section -->
    <style>
        body {
            background: #DCDCDC;
        }

        .pricing-content {
            position: relative;
        }

        .pricing_design {
            position: relative;
            margin: 0px 15px;
        }

        /* Add any additional styles needed for the pricing section */

        .pricing_design .single-pricing {
            background: rgb(28,35,47);
            padding: 60px 40px;
            border-radius: 30px;
            box-shadow: 0 10px 40px -10px rgba(0, 64, 128, .2);
            position: relative;
            z-index: 1;
        }

        .pricing_design .single-pricing:before {
            content: "";
            background-color: #fff;
            width: 100%;
            height: 100%;
            border-radius: 18px 18px 190px 18px;
            border: 1px solid #eee;
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: -1;
        }

        .price-head {}

        .price-head h2 {
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: 600;
        }

        .price-head h1 {
            font-weight: 600;
            margin-top: 30px;
            margin-bottom: 5px;
        }

        .price-head span {}

        .single-pricing ul {
            list-style: none;
            margin-top: 30px;
        }

        .single-pricing ul li {
            line-height: 36px;
        }

        .single-pricing ul li i {
            background: #554c86;
            color: #fff;
            width: 20px;
            height: 20px;
            border-radius: 30px;
            font-size: 11px;
            text-align: center;
            line-height: 20px;
            margin-right: 6px;
        }

        .pricing-price {}

        .price_btn {
            background: rgb(28,35,47);
            padding: 10px 30px;
            color: #fff;
            display: inline-block;
            margin-top: 20px;
            border-radius: 2px;
            -webkit-transition: 0.3s;
            transition: 0.3s;
        }

        .price_btn:hover {
            background: #0aa1d6;
        }

        a {
            text-decoration: none;
        }

        .section-title {
            margin-bottom: 60px;
        }

        .text-center {
            text-align: center !important;
        }
    </style>
</head>

<body>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow" style="background-color: rgb(28,35,47);">
                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <!-- ... -->
                    </li>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item">
                        <!-- ... -->
                    </li>
                    <li class="nav-item dropdown no-arrow">
                        <!-- ... -->
                    </li>
                </ul>
            </nav>

            <!-- Subscription Cards Container -->
            <section id="pricing" class="pricing-content section-padding">
                <div class="container">
                    <div class="section-title text-center">
                        <h2>Pricing Plans</h2>
                        <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-lg-4 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.1s" data-wow-offset="0" style="visibility: visible; animation-duration: 1s; animation-delay: 0.1s; animation-name: fadeInUp;">
                            <div class="pricing_design">
                                <div class="single-pricing">
                                    <div class="price-head">
                                        <h2>Starter</h2>
                                        <h1>$0</h1>
                                        <span>/Monthly</span>
                                    </div>
                                    <ul>
                                        <li><b>15</b> website</li>
                                        <li><b>50GB</b> Disk Space</li>
                                        <li><b>50</b> Email</li>
                                        <li><b>50GB</b> Bandwidth</li>
                                        <li><b>10</b> Subdomains</li>
                                        <li><b>Unlimited</b> Support</li>
                                    </ul>
                                    <div class="pricing-price">
                                        <a class="price_btn" onclick="setSubscription(1); showAddHospitalModal();">Get Started</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.2s" data-wow-offset="0" style="visibility: visible; animation-duration: 1s; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <div class="pricing_design">
                                <div class="single-pricing">
                                    <div class="price-head">
                                        <h2>Popular</h2>
                                        <h1>$49</h1>
                                        <span>/Monthly</span>
                                    </div>
                                    <ul>
                                        <li><b>30</b> website</li>
                                        <li><b>70GB</b> Disk Space</li>
                                        <li><b>70</b> Email</li>
                                        <li><b>70GB</b> Bandwidth</li>
                                        <li><b>15</b> Subdomains</li>
                                        <li><b>Unlimited</b> Support</li>
                                    </ul>
                                    <div class="pricing-price">
                                        <a class="price_btn" onclick="setSubscription(3); showAddHospitalModal();">Get Started</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 col-xs-12 wow fadeInUp" data-wow-duration="1s" data-wow-delay="0.3s" data-wow-offset="0" style="visibility: visible; animation-duration: 1s; animation-delay: 0.3s; animation-name: fadeInUp;">
                            <div class="pricing_design">
                                <div class="single-pricing">
                                    <div class="price-head">
                                        <h2>Premium</h2>
                                        <h1>$99</h1>
                                        <span>/Monthly</span>
                                    </div>
                                    <ul>
                                        <li><b>40</b> website</li>
                                        <li><b>90GB</b> Disk Space</li>
                                        <li><b>90</b> Email</li>
                                        <li><b>90GB</b> Bandwidth</li>
                                        <li><b>20</b> Subdomains</li>
                                        <li><b>Unlimited</b> Support</li>
                                    </ul>
                                    <div class="pricing-price">
                                        <a class="price_btn" onclick="setSubscription(12); showAddHospitalModal();">Get Started</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Add Hospital Modal -->
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
>
        </div>
    </div>

    <!-- Scripts and Additional JavaScript for the pricing section -->
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
