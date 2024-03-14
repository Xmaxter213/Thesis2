<?php
require_once('../dbConnection/connection.php');

if(isset($_SESSION['selectedHospitalID']))
{
    $hospital_ID = $_SESSION['selectedHospitalID'];

    $query = "SELECT Expiration FROM Hospital_Table WHERE hospital_ID = $hospital_ID";
    $query_run = mysqli_query($con, $query);

    if($query_run)
    {
        $row = mysqli_fetch_assoc($query_run);
        $expirationDate = new DateTime($row['Expiration']);
        $currentDate = new DateTime();

        if($expirationDate < $currentDate)
        {
            header("location: ../expiredPage/expired.php");
        }
    }
    else
    {
        echo "Error executing the query: " . mysqli_error($con);
    }

    
}
if (isset($_SESSION['userID'])) {
    header("Location: ../Nurses Station Page/Assistance Card Page/assistanceCard.php");
}
if (!isset($_SESSION['selectedHospitalID']))
{
    header("location: ../portal page/index.php");
}
else
{
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
}


if(isset($_GET['Change_Hospital']))
{
    session_destroy();
    unset($_SESSION);
    header("location: ../portal page/index.php");
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

    <title>Hospital Login</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="<?php echo $logo_path; ?>" />
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Font Awesome -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
    href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
    rel="stylesheet"
    />
    <!-- MDB -->
    <link
    href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.2.0/mdb.min.css"
    rel="stylesheet"
    />
    <!-- Button styles -->
    <link rel="stylesheet" href="./css/login_button_style.css">
    
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .vertical-line {
            border-left: 1px solid #000; /* Adjust thickness and color as needed */
            height: 2px; /* Adjust height as needed */
            margin: 0 10px; /* Adjust spacing as needed */
        }
        .form-outline {
            position: relative;
            margin-bottom: 1rem; /* Adjust as needed */
        }

    .form-outline input.form-control {
        padding: 1.25rem 1rem;
        border: 1px solid #ced4da; /* Adjust the border color as needed */
        border-radius: 0.375rem; /* Adjust the border radius as needed */
        font-size: 1rem; /* Adjust the font size as needed */
    }

    .form-outline input.form-control:focus {
        border-color: #80bdff; /* Adjust the border color for focus state as needed */
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25); /* Adjust the box shadow for focus state as needed */
    }

    .form-outline label {
        position: absolute;
        top: 1rem; /* Adjust the label position as needed */
        left: 1rem; /* Adjust the label position as needed */
        color: #6c757d; /* Adjust the label color as needed */
        transition: transform 0.15s ease-in-out, opacity 0.15s ease-in-out;
        opacity: 0.5;
        cursor: text;
    }

    .form-outline input.form-control:focus ~ label,
    .form-outline input.form-control:not(:placeholder-shown) ~ label {
        top: 0.25rem; /* Adjust the label position for focus and active states as needed */
        left: 0.75rem; /* Adjust the label position for focus and active states as needed */
        font-size: 0.75rem; /* Adjust the font size for focus and active states as needed */
        opacity: 1;
    }
    
    .bg-login-image {
            background-image: url('<?php echo $logo_path; ?>');
            border-radius: 50%;
            /* Other background properties like size, position, repeat, etc. can be added here */
        }
        .bg-login-image-container {
            padding: 20px; /* Adjust the padding value as needed */
        }
    </style>

</head>

<body style="background-color: rgb(28,35,47);">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row bg-login-image-container">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome</h1>
                                        <h6 class="card-subtitle mb-2">Let's get you Logged in</h6>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" aria-describedby="emailHelp" placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" placeholder="Password" required>
                                        </div>

                                        <button type="button" name="button" class="buttonStyle" id="login">Login</button>
                                        <!-- btn btn-secondary btn-user btn-block -->
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                    </div>
                                    <div class="text-center">
                                        <a class="small menu__link" href="?Change_Hospital=1">Change Hospital</a>
                                        <span class="vertical-line"></span> <!-- Vertical line -->
                                        <a class="small menu__link" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="modal top fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop='static' aria-hidden="true" data-mdb-keyboard="true">
        <div class="modal-dialog" style="width: 500px;">
            <div class="modal-content text-center">
                <div class="modal-header h5 text-white" style="background-color: rgb(28, 35, 47); justify-content: center;">
                    Password Reset
                </div>
                <div class="modal-body px-5">
                    <p class="py-2">
                        Enter your email address and we'll send you an SMS verification code.
                    </p>
                    <div class="form-outline">
                        <input type="email" id="typeEmail" class="form-control my-3" />
                        <label class="form-label" for="typeEmail">Email input</label>
                    </div>
                    <button type="button" class="btn btn-secondary bg-primary btn-user btn-block" id="resetPassButton">Reset password</button>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary btn-user " data-bs-dismiss="modal" id="forgotclose">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal top fade" id="confirmCodeModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop='static' aria-hidden="true" data-mdb-keyboard="true">
        <div class="modal-dialog" style="width: 500px;">
            <div class="modal-content text-center">
                <div class="modal-header h5 text-white" style="background-color: rgb(28, 35, 47); justify-content: center;">
                    Confirm Code
                </div>
                <div class="modal-body px-5">
                    <p class="py-2">
                        Enter your code
                    </p>
                    <div class="form-outline">
                        <input type="text" id="typeCode" class="form-control my-3" />
                        <label class="form-label" for="typeCode">Code</label>
                    </div>
                    <button type="button" class="btn btn-secondary bg-primary btn-user btn-block" id="comfirmCode">Submit</button>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="confirmclose">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal top fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop='static' aria-hidden="true" data-mdb-keyboard="true">
        <div class="modal-dialog" style="width: 500px;">
            <div class="modal-content text-center">
                <div class="modal-header h5 text-white" style="background-color: rgb(28, 35, 47); justify-content: center;">
                    Change Password
                </div>
                <div class="modal-body px-5">
                    <div class="form-outline">
                        <input type="password" id="type1Password" class="form-control my-3" />
                        <label class="form-label" for="type1Password">Password</label>
                    </div>
                    <div class="form-outline">
                        <input type="password" id="type2Password" class="form-control my-3" />
                        <label class="form-label" for="type2Password">Confirm Password</label>
                    </div>
                    <button type="button" class="btn btn-secondary btn-user bg-primary btn-block" id="submitChangePassword">Submit</button>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="ChangePasswordclose">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script type="text/javascript">
        $(function() {
            $('#login').click(function(e) {
                var valid = this.form.checkValidity();

                if (valid) {

                    var email = $('#email').val();
                    var password = $('#password').val();

                    e.preventDefault();

                    $.ajax({
                        type: 'POST',
                        url: 'jslogin.php',
                        data: {
                            email: email,
                            password: password
                        },
                        success: function(data) {
                            if (data === "Successfully") {
                                Swal.fire({
                                    'title': 'Successful',
                                    'text': data,
                                    'type': 'success'
                                })
                                if ($.trim(data) === "Successfully") {
                                    setTimeout('window.location.href = "../Nurses Station Page/Assistance Card Page/assistanceCard.php"', 2000);
                                }
                            } else if (data === "Account Not validated") {
                                Swal.fire({
                                    'title': 'Errors',
                                    'text': data,
                                    'type': 'error'
                                }).then(function() {
                                    window.location = "../OTP/OTP.php";
                                });
                            } else {
                                Swal.fire({
                                    'title': 'Errors',
                                    'text': data,
                                    'type': 'error'
                                })

                            }

                        },
                        error: function(data) {
                            Swal.fire({
                                'title': 'Errors',
                                'text': 'There were errors while saving the data.',
                                'type': 'error'
                            })
                        }

                    })

                } else {
                    Swal.fire({
                        'title': 'Errors',
                        'text': 'Missing Informations',
                        'type': 'error'
                    })
                }


            })
        });

        // Handling "Reset password" button click event
        $('#forgotPasswordModal #resetPassButton').click(function(e) {
            var email = $('#typeEmail').val();

            // Email validation using regular expression
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    'title': 'Error',
                    'text': 'Please enter a valid email address.',
                    'type': 'error'
                });
                return;
            }
            
            document.getElementById("resetPassButton").disabled = true; 
            document.getElementById("forgotclose").disabled = true;
            e.preventDefault();

            $.ajax({
            type: 'POST',
            url: 'sendCode.php', // Replace with your PHP file handling password reset
            data: {
                email: email
            },
            success: function(response) {
                // Handle success response
                if (response.success) {
                    Swal.fire({
                        'title': 'Success',
                        'text': response.message,
                        'type': 'success'
                    }).then((result) => {
                        
                        $('#forgotPasswordModal').modal('hide');
                        $('.modal-backdrop').remove(); 
                        $('body').removeClass('modal-open');

                        $('#confirmCodeModal').modal('show');
                    });
                } else {
                    Swal.fire({
                        'title': 'Error',
                        'text': response.message,
                        'type': 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
            
            console.error(xhr.responseText);
            Swal.fire({
                'title': 'Error',
                'text': 'An error occurred while processing your request.',
                'type': 'error'
            });
        },
        complete: function() {
            
            document.getElementById("resetPassButton").disabled = false; 
            document.getElementById("forgotclose").disabled = false;
        }
    });
});
</script>

<script>
    $('#confirmCodeModal #comfirmCode').click(function(e) {
                var code = $('#typeCode').val(); 

                document.getElementById("comfirmCode").disabled = true;
                document.getElementById("confirmclose").disabled = true;
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'confirmCode.php', 
                    data: {
                        code: code 
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                'title': 'Success',
                                'text': response.message,
                                'type': 'success'
                            }).then((result) => {
                                
                                $('#confirmCodeModal').modal('hide');
                                $('.modal-backdrop').remove(); 
                                $('body').removeClass('modal-open');

                                $('#changePasswordModal').modal('show');
                            });
                        } else {
                            Swal.fire({
                                'title': 'Error',
                                'text': 'this codes',
                                'type': 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                    
                    console.error(xhr.responseText);
                    Swal.fire({
                        'title': 'Error',
                        'text': 'An error occurred while processing your request.',
                        'type': 'error'
                    });
                },
                complete: function() {
                    
                    document.getElementById("comfirmCode").disabled = false;
                    document.getElementById("confirmclose").disabled = false;
                }
                });
            });
</script>

<script>
    $(document).ready(function() {
        $('#submitChangePassword').click(function(e) {
            var password1 = $('#type1Password').val().trim();
            var password2 = $('#type2Password').val().trim();

            // Check if fields are empty
            if (password1 === '' || password2 === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Please fill in all fields.',
                    type: 'error'
                });
                return;
            }

            // Check if passwords match
            if (password1 !== password2) {
                Swal.fire({
                    title: 'Error',
                    text: 'Passwords do not match.',
                    type: 'error'
                });
                return;
            }

            document.getElementById("submitChangePassword").disabled = true;
            document.getElementById("ChangePasswordclose").disabled = true;
            $.ajax({
                type: 'POST',
                url: 'change_pass.php', 
                data: {
                    password: password1
                },
                success: function(response) {
                    
                    Swal.fire({
                        title: 'Success',
                        text: 'Password changed successfully.',
                        type: 'success'
                    }).then((result) => {
                        $('#changePasswordModal').modal('hide');
                        $('.modal-backdrop').remove(); 
                        $('body').removeClass('modal-open');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
                        type: 'error'
                    });
                },
                complete: function() {
                    
                    document.getElementById("submitChangePassword").disabled = false;
                    document.getElementById("ChangePasswordclose").disabled = false;
                }
                });
            });
    });
</script>

</body>

</html>