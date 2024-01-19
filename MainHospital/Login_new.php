<?php 
	require_once('../dbConnection/connection.php');

	if(isset($_SESSION['userID']))
	{
		header("Location: ../dumHomePage/index.php");
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

    <title>F.O.O.D - Login</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/FinalLogo.png" />
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="email" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="password" placeholder="Password" required>
                                        </div>
                                        
                                        <button type="button" name="button" class="btn btn-primary btn-user btn-block" id="login">Login</button>
                                        
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="../Register Page/Register.php">Create an Account!</a>
                                        <br>
                                        <a class="small" href="../Home Page/index.php">Go to Home!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script type="text/javascript">
	$(function()
		{
			$('#login').click(function(e){
				var valid = this.form.checkValidity();

				if(valid)
				{

					var email = $('#email').val();
					var password = $('#password').val();

                    e.preventDefault();

                    $.ajax({
                    type: 'POST',
                    url: 'jslogin.php',
                    data: {email: email, password: password},
                    success: function(data){
                        if(data === "Successfully")
                        {
                            Swal.fire({
                                        'title': 'Successful',
                                        'text': data,
                                        'type': 'success'
                                        })
                            if($.trim(data) === "Successfully")
                            {
                                setTimeout('window.location.href = "../dumHomePage/index.php"', 2000);
                            }
                        }
                        else if(data === "Account Not validated")
                        {
                            Swal.fire({
                                        'title': 'Errors',
                                        'text': data,
                                        'type': 'error'
                                        }).then(function(){
                                                window.location = "../OTP/OTP.php";
                                        });
                        }
                        else
                        {
                            Swal.fire({
                                        'title': 'Errors',
                                        'text': data,
                                        'type': 'error'
                                        })

                        }
                            
                            },
                            error: function(data){
                                Swal.fire({
                                        'title': 'Errors',
                                        'text': 'There were errors while saving the data.',
                                        'type': 'error'
                                        })
                            }
                            
                })

				}
                else
                {
                    Swal.fire({
                                        'title': 'Errors',
                                        'text': 'Missing Informations',
                                        'type': 'error'
                                        })
                }

				
			})
        });
</script>

</body>

</html>