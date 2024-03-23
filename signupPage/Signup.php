<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include('../dbConnection/AES encryption.php');
require '../vendor/autoload.php';
require_once('../dbConnection/connection.php');

if (isset($_POST['add'])) {
    $Subscriber_FirstName = $_POST['Subscriber_first_Name'];
    $Subscriber_LastName = $_POST['Subscriber_last_Name'];
    $amount = $_POST['Price'];

    $Subscriber_Name = $Subscriber_FirstName . $Subscriber_LastName;

    $Hospital_Name = $_POST['Hospital_Name'];
    $Subscriber_Email = $_POST['Subscriber_email'];
    $Subscription = $_POST['Subscription_Duration'];
    $Status = 'Admin';

    date_default_timezone_set('Asia/Manila');
    $Creation_Date = date("Y-m-d H:i:s");

    $Expiration_Date = date("Y-m-d H:i:s", strtotime("+" . $Subscription . " months", strtotime($Creation_Date)));

    //encrypted
    $enc_Subscriber_Name = encryptthis($Subscriber_Name, $key);
    $enc_Subscriber_Email = encryptthis($Subscriber_Email, $key);
    $enc_Status = encryptthis($Status, $key);

    $sqladdHospital = "INSERT INTO Hospital_Table (hospital_Logo, Subscriber_Name, hospitalName, email, creation_Date, Expiration) 
    VALUES ('default.png', '$enc_Subscriber_Name', '$Hospital_Name', '$enc_Subscriber_Email', '$Creation_Date', '$Expiration_Date')";
    $query_run_addHospital = mysqli_query($con, $sqladdHospital);

    if ($query_run_addHospital) 
    {
        date_default_timezone_set('Asia/Manila');

        $currentDateTime = date("Y-m-d H:i:s");

        $hospital_ID = mysqli_insert_id($con);

        // Insert into superAdminLogs
        $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$Subscriber_Name', 'New Subscriber $Subscription month/s Payment of $$amount', '$currentDateTime')";
        $query_run_logs = mysqli_query($con, $sqlAddLogs);

        
        $query = "INSERT INTO userLogin ( email, password, userName, status, code, verifyPassword, hospital_ID) 
        VALUES ('$Subscriber_Email','$enc_Subscriber_Name', '$enc_Subscriber_Name', '$enc_Status', '0', '0', '$hospital_ID')";
        $query_run = mysqli_query($con, $query);

        $queryStaff = "INSERT INTO staff_List (hospital_ID, nurse_Name, assigned_Ward, contact_No, nurse_Sex, nurse_birth_Date, shift_Schedule, employment_Status, date_Employment, activated) 
        VALUES ($hospital_ID, 'HOSPITAL OWNER', 'Medicine Ward', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', 'HOSPITAL OWNER', '1')";
        $query_run = mysqli_query($con, $queryStaff);

        $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.elasticemail.com';                     
                $mail->SMTPAuth   = true;                                  
                $mail->Username   = 'j4ishere@gmail.com';                     
                $mail->Password   = 'A02F3F4222553D746B478EC9E43E48624D90'; 
                $mail->Port       = 2525;

                $mail->setFrom('j4ishere@gmail.com', 'Helping Hand');
                $mail->addAddress($Subscriber_Email, 'Recipient Name');
                $mail->isHTML(true);
                $mail->Subject = 'Hospital Subscription';
                $mail->Body    = "Hello {$Hospital_Name},<br><br>We're pleased to inform you that your subscription has been successful .<br><br>Your subscription is up until: 
                    {$Expiration_Date}.<br><br>Your Account Have been Created.<br>Email: {$Subscriber_Email} <br>Password: {$Subscriber_Name} <br><br>Thank you for choosing our Helping Hand service!<br><br>Best regards,<br>Helping Hand";

                $mail->send();
                echo 'Message has been sent';
            } 
            catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            $_SESSION['selectedHospitalID'] = $hospital_ID;
            mysqli_close($con);
            header('Location: ../MainHospital/login_new.php');
            exit;

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

    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

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

        .navbar-brand {
            color: white !important; /* Set the color to white */
            text-align: center; /* Center the text */
            width: 100%; /* Take up the full width of the navbar */
        }

        .navbar-brand h1 {
            margin: 0; /* Remove default margin */
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
                        <a href="../portal page/index.php" class="nav-link">Have&nbsp;an&nbsp;account?</a>
                        </li>
                    </ul>
                </div>
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
                                        <h2>1 Month</h2>
                                        <h1>$30</h1>
                                    </div>
                                    <ul>
                                        <li>Inclusions : </li>
                                        <li><b>1</b> Month Subscription</li>
                                        <li><b>1</b> Device (Helping Hand)</li>
                                        <li><b>100</b> Free SMS</li>
                                        <li><b>250</b> Patient Account</li>
                                        <li><b>Unlimited</b> Nurse Account</li>
                                        <li> </li>
                                    </ul>
                                    <br>
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
                                        <h2>3 Months</h2>
                                        <h1>$35</h1>
                                    </div>
                                    <ul>
                                    <li>Inclusions : </li>
                                        <li><b>2 + 1(free)</b> Months Subscription</li>
                                        <li><b>1</b> Device (Helping Hand)</li>
                                        <li><b>100</b> Free SMS</li>
                                        <li><b>250</b> Patient Account</li>
                                        <li><b>Unlimited</b> Nurse Account</li>
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
                                        <h2>1 Year</h2>
                                        <h1>$80</h1>
                                    </div>
                                    <ul>
                                    <li>Inclusions : </li>
                                        <li><b>8 + 4(free)</b> Months Subscription</li>
                                        <li><b>1</b> Device (Helping Hand)</li>
                                        <li><b>100</b> Free SMS</li>
                                        <li><b>250</b> Patient Account</li>
                                        <li><b>Unlimited</b> Nurse Account</li>
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
                        <form action="" method="POST" onsubmit="return validateForm();">
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
                                    <select id="Subscription_Duration" name="Subscription_Duration" onchange="fetchPrice(this.value)">
                                        <?php
                                        while ($row = mysqli_fetch_array($resultDuration)) {
                                            ?>
                                            <option value="<?php echo $row["Duration_Month"]; ?>" data-price="<?php echo $row["price"]; ?>">
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
                            <div>
                                <label id="priceLabel">Price: </label>
                                <input type="number" id="Price" name="Price" placeholder="Enter Price">
                            </div>
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

    <!-- Bootstrap core JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts and Additional JavaScript for the pricing section -->
    <script>
        function setSubscription(duration) {
        document.getElementById('Subscription_Duration').value = duration;
        var duration = $("#Subscription_Duration").val();
        fetchPrice(duration);

        // Proceed with showing the modal
        showAddHospitalModal();
    }

    function showAddHospitalModal() {
        // Show the modal using Bootstrap's modal method
        $('#addHospital').modal('show');
    }

    function hideAddHospitalModal() {
        // Show the modal using Bootstrap's modal method
        $('#addHospital').modal('hide');
    }

    function fetchPrice(duration) {
        // Get the selected option
        var selectedOption = $("#Subscription_Duration option:selected");
        // Get the price associated with the selected duration
        var price = selectedOption.data("price");
        // Update the price label with the fetched price
        $('#priceLabel').text("Price: $" + price);
    }

    // Function to validate form input before submission
    function validateForm() {
        var userEnteredPrice = $("#Price").val();
        var subscriptionPrice = $("#Subscription_Duration option:selected").data("price");

        // Check if the user-entered price is less than the subscription price
        if (userEnteredPrice < subscriptionPrice) {
            alert("Please enter an amount equal to or greater than $" + subscriptionPrice);
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
    </script>
</body>

</html>
