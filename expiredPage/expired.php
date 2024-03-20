<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once('../dbConnection/connection.php');
include('../dbConnection/AES encryption.php');

if (!isset($_SESSION['selectedHospitalID']))
{
    header("location: ../portal page/index.php");
}
else
{
    try {
        $stmt = $con->prepare("SELECT hospital_Logo FROM Hospital_Table WHERE hospital_ID = ?");
        $stmt->bind_param("i", $_SESSION['selectedHospitalID']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $logo_path = "../LOGO FOLDER/" . $row['hospital_Logo'];
        } else {
            $logo_path = "../LOGO FOLDER/default.png";
        }
    } catch (Exception $e) {
        echo "Server is slow please wait for a few minutes: " . $e->getMessage();
    }
}
if(isset($_SESSION['selectedHospitalID']))
{
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "SELECT Expiration, Subscriber_Name, hospitalName FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $row = mysqli_fetch_assoc($query_run);
        $hospitalName = $row['hospitalName'];
        $SubscriberName = $row['Subscriber_Name'];
        $expirationDate = new DateTime($row['Expiration']);
        $currentDate = new DateTime();

        $owner = decryptthis($SubscriberName, $key);

        
        if($expirationDate > $currentDate)
        {
          if (isset($_SESSION['userID'])) {
            header("Location: ../Nurses Station Page/Assistance Card Page/assistanceCard.php");
          }
        }
    }
    else
    {
        echo "Error executing the query: " . mysqli_error($con);
    }

    
}

if(isset($_GET['Change_Hospital']))
{
    session_destroy();
    unset($_SESSION);
    header("location: ../portal page/index.php");
}


if(isset($_POST['extend']))
{
    $extension = $_POST['Subscription_Duration'];
    $amount = $_POST['userAmount'];

    $sql = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $sql);
    if ($query_run) {
        if (mysqli_num_rows($query_run) > 0) {
            $query_result = mysqli_fetch_assoc($query_run);
            $currentExpiration = $query_result['Expiration'];

            // Convert current expiration to DateTime object
            $currentExpirationDateTime = new DateTime($currentExpiration);

            // Calculate new expiration by adding extension duration
            $newExpirationDateTime = clone $currentExpirationDateTime;
            $newExpirationDateTime->add(new DateInterval("P{$extension}M"));

            // Now $newExpirationDateTime contains the new expiration date
            $new_expiration = $newExpirationDateTime->format('Y-m-d');

            $sqlupdate = "UPDATE Hospital_Table SET Expiration = '$new_expiration' WHERE hospital_ID = $hospital_ID";

            $query_update = mysqli_query($con, $sqlupdate);

            if ($query_update) {

                    $sql = "SELECT hospitalName, Expiration, email FROM Hospital_Table WHERE hospital_ID = ?";
                    $stmtselect = $con->prepare($sql);
                    $stmtselect->bind_param("s", $hospital_ID);
                    $result = $stmtselect->execute();
                    $stmtselect->store_result();

                    if ($result) 
                    {
                        if ($stmtselect->num_rows > 0) 
                        {
                                $stmtselect->bind_result($hospitalName, $Expiration, $email);
                                $stmtselect->fetch();

                                $email = decryptthis($email, $key);

                                $mail = new PHPMailer(true);
                            try {
                                $mail->isSMTP();                                            
                                $mail->Host       = 'smtp.elasticemail.com';                     
                                $mail->SMTPAuth   = true;                                  
                                $mail->Username   = 'j4ishere@gmail.com';                     
                                $mail->Password   = 'A02F3F4222553D746B478EC9E43E48624D90'; 
                                $mail->Port       = 2525;

                                $mail->setFrom('j4ishere@gmail.com', 'Helping Hand');
                                $mail->addAddress($email, 'Recipient Name');
                                $mail->isHTML(true);
                                $mail->Subject = 'Hospital Subscription';
                                $mail->Body    = "Hello {$hospitalName},<br><br>We're pleased to inform you that your Subscription has been extended .<br><br>Your subscription is up until: 
                                    {$Expiration}.<br><br>Thank you for choosing our Helping Hand service!<br><br>Best regards,<br>Helping Hand";

                                $mail->send();
                            } 
                            catch (Exception $e) {
                                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                            }


                            date_default_timezone_set('Asia/Manila');
                            $currentDateTime = date("Y-m-d H:i:s");
                            // Insert into superAdminLogs
                            
                            $sqlAddLogs = "INSERT INTO superAdminLogs (User, Action, Date_Time) VALUES ('$hospitalName', 'Extended Hospital : $hospitalName Payment : $$amount', '$currentDateTime')";
                            $query_run_logs = mysqli_query($con, $sqlAddLogs);
                            header("location: ../portal page/index.php");

                            if(!$query_run_logs)
                            {
                                echo 'Error inserting logs: ' . mysqli_error($con);
                            }
                        }
                    }
              
            } else {
                echo "Update failed: " . mysqli_error($con);
            }

        } else {
            echo "No results found";
        }
    } else {
        echo "Query failed: " . mysqli_error($con);
    }
}





?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Subscription</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        /* Apply background color to the body */
        body {
            background: #DCDCDC;
        }

        .expired-container {
            
            margin: 5%; /* Set 10% margin on all sides */
        }

        .expired-card {
            background: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            width: 200%; /* Set width to 200% */
            margin: 0 -50%; /* Adjust margin to center the card */
        }

        .bg-login-image {
            background-image: url('<?php echo $logo_path; ?>');
            background-size: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
                style="background-color: rgb(28,35,47);">
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
                    <li class="nav-item">
                        <a href="?Change_Hospital=1" class="nav-link" style="color: white;">Have&nbsp;an&nbsp;account?</a>
                    </li>
                </ul>
            </nav>

            <div class="expired-container">
                <div class="container">
                    <div class="row justify-content-center">

                        <!-- Expired Subscription with Login Section -->
                        <div class="col-md-6">
                            <div class="expired-card">
                                <div class="card-body">
                                    <h3 class="text-center text-danger">Expired Subscription</h3>
                                    <p class="text-center">Your subscription has expired. Please renew it to continue
                                        enjoying our services.</p><br>


                                    <!-- Login Section -->
                                    <div class="row mt-4">
                                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                                        <div class="col-lg-6">
                                            <div class="p-5">
                                                <br>
                                                <div class="text-center">
                                                    <h2 class="text-left">Hospital Details : </h2>
                                                </div>
                                                <br>
                                                <div class="text-left">
                                                    <label>Hospital Name : </label>
                                                    <h5><?php echo $hospitalName; ?></h5>
                                                </div>
                                                <br>
                                                <div class="text-left">
                                                    <label>Subscriber Name : </label>
                                                    <h5><?php echo $owner; ?></h5>
                                                </div>
                                                <br><br>
                                                <hr>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End of Login Section -->

                                    <br><br><br>
                                    <div class="text-center">
                                        <a href="#extendModal" id="renewButton" class="btn btn-primary">Renew Subscription</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End of Expired Subscription with Login Section -->

                    </div>
                </div>
            </div>

        </div>
    </div>

<!-- Extension Modal -->
<div class="modal fade" id="extendModal" tabindex="-1" role="dialog" aria-labelledby="extendModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendModalLabel">Extend Subscription</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="hideExtendModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="" onsubmit="return validateAmount();">
                    <!-- Pass the hospital ID to the modal form -->
                    <input type="hidden" name="hospital_ID">
                    <div class="form-group">
                        <?php 
                            // retrieve selected results from the database and display them on the page
                            $sqlDuration = 'SELECT * FROM Subscription_Duration';
                            $resultDuration = mysqli_query($con, $sqlDuration);

                            if (mysqli_num_rows($resultDuration) > 0) {
                        ?>
                        <label for="Subscription_Duration">Subscription Duration</label>
                        <select id="Subscription_Duration" name="Subscription_Duration" class="form-control" onchange="fetchPrice(this.value);">
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
                    <div class="form-group">
                        <label>Price</label>
                        <span id="priceLabel"></span>
                    </div>
                    <div class="form-group">
                        <label for="userAmount">Enter Amount</label>
                        <input type="text" class="form-control" id="userAmount" name="userAmount" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="hideExtendModal();">Close</button>
                    <button type="submit" class="btn btn-primary" name="extend">Extend</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Manually trigger the modal when the button is clicked
        $('#renewButton').click(function () {
            // Set the default value of months to 1
            $('#Subscription_Duration').val('1');
            // Show the modal
            $('#extendModal').modal('show');
            // Fetch and display the price for the default duration
            fetchPrice('1');
        });
    });

    function hideExtendModal() {
        // Hide the modal using Bootstrap's modal method
        $('#extendModal').modal('hide');
    }

    function fetchPrice(duration) {
        // Get the selected option
        var selectedOption = $("#Subscription_Duration option:selected");
        // Get the price associated with the selected duration
        var price = selectedOption.data("price");
        // Update the price label with the fetched price
        $('#priceLabel').text("Price: $" + price);
    }

    function validateAmount() {
        var userAmount = parseFloat($('#userAmount').val());
        var selectedOption = $("#Subscription_Duration option:selected");
        var price = parseFloat(selectedOption.data("price"));
        
        if (userAmount !== price) {
            alert("Please enter an amount equal to the price.");
            return false;
        }
        return true;
    }
</script>



</body>

</html>
