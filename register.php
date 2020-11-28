<?php
// We are going to process logging in here, then redirecting to the home page
require 'config/config.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	if (isset($_POST['username']) && isset($_POST['password'])) {
		$username = trim($_POST['username']);
		$password = $_POST['password'];
		if(empty($username) || empty($password)) {
			$error = "Please enter username and password";
		}

		else {
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

			if($mysqli->connect_errno) {
				echo $mysqli->connect_error;
				exit();
			}

			$password_hashed = hash("sha256", $password);

			$sql = "SELECT * FROM users WHERE username = '" . $username . "' AND password = '" . $password_hashed . "';";

			$results = $mysqli->query($sql);

			if(!$results) {
				echo $mysqli->error;
				exit();
			}

			if($results->num_rows > 0) {

				// Set session variables to remember this user
				$_SESSION["username"] = $_POST["username"];
				$_SESSION["logged_in"] = true;

				header("Location: search_form.php");
			
			}
			else {
				$error = "Invalid username or password.";
			}

		}
	}
}
else {
	header("Location: search_form.php");
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<script src="http://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/shared.css">
	<style>

		#signin_active {
			font-weight: bold;
		}


		.form-rounded {
			border-radius: 2rem;
			border: 0;
			height: 45px;
		}

		.form-control {
			margin-top: 1px;
			border-radius: 2rem;
			border: 0;
			background-color: #F2F2F2;
			padding-left: 20px;
			padding-right: 20px;
			width: 90%;
			height: 40px;
		}

		.form-group {
			width: 100%;
		}


		input {
			background-color: #F2F2F2;
			border: none;
			padding-left: 20px;
			padding-right: 20px;
			width: 90%;
		}


		.logo {
			margin-top: 100px;
			margin-bottom: 20px;
		}

		.stuff {
			margin-left: 40px;
			margin-right: 40px;
		}

		.contents {
			margin-top: 50px;
			margin-bottom: 70px;
			background-color: white;
			border-radius: 20px;
		  	box-shadow: 5px 5px 15px grey;
			position: relative;
			width: 100%;
			height: 800px;
			max-width: 500px;
			overflow: hidden;
		}

		.form-container {
			position: absolute;
			top: 0;
			height: 100%;
			width: 100%;
			transition: 1s;
			right: 0;
		}

		.form-group {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
		}

		.break {
		  flex-basis: 100%;
		  height: 0;
		}


		form {
			background-color: white;
			display: flex;
			justify-content: center;
			flex-direction: column;
			align-items: center;
			text-align: center;
			height: 100%;
		}

				p {
			margin-top: 2%;
			margin-bottom: 5%;
		}

		.btn {
			border-radius: 25px;
			font-size: 14px;
			letter-spacing: 1px;
			background-color: #3C91E6;
			color: white;
			padding-top: 11px;
			padding-bottom: 11px;
			padding-left: 40px;
			padding-right: 40px;
		}

				.alternate {
			background-color: white;
			color: #3C91E6;
		}

				.login {
			top:0;
			height: 50%;
		}

		.register {
			top: 0;
			height: 50%;
			display: none;
			transform: translateY(100%);
			z-index: 1;
		}

		h1 {
			margin-bottom: 20px;
		}


		.blue {
			background: #3C91E6;
			color: white;
			position: relative;
			top: -100%;
			width: 100%;
			height: 200%;
			transition: 1s;
		}

		.panel {
			position: absolute;
			display: flex;
			justify-content: center;
			flex-direction: column;
			align-items: center;
			text-align: center;
			padding-right: 30px;
			padding-left: 30px;
			height: 100%;
			width: 100%;
			transition: 1s;
		}

		.blue-container {
			overflow: hidden;
			position: absolute;
			width: 100%;
			transition: 1s;
			z-index: 2;
			top: 50%;
			left: 0;
			height: 50%;
		}

		.right {
				bottom: 0;
				transform: translateY(25%);
			}

			.left {
				transform: translateY(-20%);
			}

					.submit-btn {
			margin-top: 0px;
		}


		.register-active .register {
			display: block;
			}

			.register-active .login {
				transform: translateY(100%);
			}

			.register-active .blue-container{
				transform: translateY(-100%);
			}

			.register-active .blue {
			  	transform: translateY(50%);
			}

			.register-active .left {
				transform: translateY(-25%);
			}

			.register-active .right {
				display: none;
				transform: translateY(20%);
			}

		.other {
			display: none;
		}


		@media (min-width: 768px) {


			.mobile {
				display: none;
			}

			.other {
				display: block;
			}

			.contents {
				height: 500px;
				max-width: 800px;
				margin-top: 100px;
			}

		.form-container1 {
			position: absolute;
			top: 0;
			height: 100%;
			transition: 1s;
		}

		input {
			background-color: #F2F2F2;
			border: none;
			padding-left: 30px;
			padding-right: 30px;
			margin: 10px;
			width: 80%;
		}

		p {
			margin-top: 2%;
			margin-bottom: 5%;
		}


		.btn {
			border-radius: 25px;
			font-size: 14px;
			letter-spacing: 1px;
			background-color: #3C91E6;
			color: white;
			padding-top: 11px;
			padding-bottom: 11px;
			padding-left: 40px;
			padding-right: 40px;
		}


		.alternate {
			background-color: white;
			color: #3C91E6;
		}

		.login1 {
			left: 0;
			width: 50%;
		}

		.register1 {
			left: 0;
			width: 50%;
			display: none;
			z-index: 1;
		}

		.blue-container1 {
			position: absolute;
			height: 100%;
			overflow: hidden;
			transition: 1s;
			z-index: 2;
			top: 0;
			left: 50%;
			width: 50%;
		}

		.blue1 {
			background: #3C91E6;
			color: white;
			position: relative;
			left: -100%;
			height: 100%;
			width: 200%;
			transition: 1s;
		}

		.panel1 {
			position: absolute;
			display: flex;
			justify-content: center;
			flex-direction: column;
			align-items: center;
			text-align: center;
			padding-right: 30px;
			padding-left: 30px;
			height: 100%;
			width: 50%;
			transition: 1s;
		}

		.submit-btn {
			margin-top: 10px;
		}

		.register-active1 .register1 {
				transform: translateX(100%);
				display: block;
			}

			.register-active1 .login1 {
				transform: translateX(100%);
			}

			.register-active1 .blue-container1{
				transform: translateX(-100%);
			}

			.register-active1 .blue1 {
			  	transform: translateX(50%);
			}

			.right1 {
				right: 0;
				transform: translateX(0%);
			}

			.left1 {
				transform: translateX(-50%);
			}

			.register-active1 .left1 {
				transform: translateX(0%);
			}

			.register-active1 .right1 {
				transform: translateX(50%);
			}

		}

	</style>
</head>
<body>
	<div class="all">
		<?php include 'nav.php'; ?>
		<div class="stuff">

		<div class="container-fluid contents">
			<div class="mobile">
				<div class="form-container register">
					<form action="register_confirm.php" method="POST" class="register-form">
						<h1><strong>Register</strong></h1>
						<div class="form-group">
							<input type="text" class="form-control username-input-m" name="username" placeholder="Username"/>
							<div class="break"> </div>
							<small class="invalid-feedback">Username is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-input-m" name="password" placeholder="Password" />
							<div class="break"> </div>
							<small class="invalid-feedback">Password is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-check-input-m" name="password-check" placeholder="Repeat Password" />
							<div class="break"> </div>
							<small id="password-check-error" class="invalid-feedback">Repeated password is required.</small>
						</div>
						<button class="btn submit-btn"><strong>Submit</strong></button>
					</form>
				</div>
				<div class="form-container login">
					<form action="register.php" method="POST" class="login-form">
						<h1><strong>Sign in</strong></h1>
						<div class="form-group">
							<input type="text" class="form-control username-input-login-m" name="username" placeholder="Username" />
							<div class="break"> </div>
							<small class="invalid-feedback">Username is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-input-login-m" placeholder="Password" name="password" />
							<div class="break"> </div>
							<small class="invalid-feedback">Password is required.</small>
							<div class="font-italic text-danger err">
								<?php
									if (isset($error) && !empty($error)) {
										echo $error;
									}
								?>
							</div>
						</div>
						<button class="btn submit-btn"><strong>Submit</strong></button>
					</form>
				</div>
				<div class="blue-container">
					<div class="blue">
						<div class="panel left">
							<h1><strong>Hello again!</strong></h1>
							<p>If you have an account, login instead of registering.</p>
							<button class="btn alternate" id="signIn"><strong>Sign in</strong></button>
						</div>
						<div class="panel right">
							<h1><strong>Welcome!</strong></h1>
							<p>First time here? Make an account with us below.</p>
							<button class="btn alternate" id="signUp"><strong>Register</strong></button>
						</div>
					</div>
				</div>
			</div>
			<div class="other">
				<div class="form-container1 register1">
					<form action="register_confirm.php" method="POST" class="register-form-w">
						<h1><strong>Register</strong></h1>
						<div class="form-group">
							<input type="text" class="form-control username-input-w" name="username" placeholder="Username"/>
							<div class="break"> </div>
							<small class="invalid-feedback">Username is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-input-w" name="password" placeholder="Password" />
							<div class="break"> </div>
							<small class="invalid-feedback">Password is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-check-input-w" name="password-check" placeholder="Repeat Password" />
							<div class="break"> </div>
							<small id="password-check-error-w" class="invalid-feedback">Repeated password is required.</small>
						</div>
						<button class="btn submit-btn"><strong>Submit</strong></button>
					</form>
				</div>
				<div class="form-container1 login1">
					<form action="register.php" method="POST" class="login-form-w">
						<h1><strong>Sign in</strong></h1>
						<div class="form-group">
							<input type="text" class="form-control username-input-login-w" name="username" placeholder="Username" />
							<div class="break"> </div>
							<small class="invalid-feedback">Username is required.</small>
						</div>
						<div class="form-group">
							<input type="password" class="form-control password-input-login-w" placeholder="Password" name="password" />
							<div class="break"> </div>
							<small class="invalid-feedback">Password is required.</small>
						</div>
						<div class="font-italic text-danger err">
								<?php
									if (isset($error) && !empty($error)) {
										echo $error;
									}
								?>
							</div>
						<button class="btn submit-btn"><strong>Submit</strong></button>
					</form>
				</div>
				<div class="blue-container1">
					<div class="blue1">
						<div class="panel1 left1">
							<h1><strong>Hello again!</strong></h1>
							<p>If you have an account, login instead of registering.</p>
							<button class="btn alternate" id="signIn1"><strong>Sign in</strong></button>
						</div>
						<div class="panel1 right1">
							<h1><strong>Welcome!</strong></h1>
							<p>First time here? Make an account with us below.</p>
							<button class="btn alternate" id="signUp1"><strong>Register</strong></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$('#signUp').click(function() {
	$('.contents').addClass("register-active");
});

$('#signIn').click(function() {
	$('.contents').removeClass("register-active");
});

$('#signUp1').click(function() {
	$('.contents').addClass("register-active1");
});

$('#signIn1').click(function() {
	$('.contents').removeClass("register-active1");
});

$('.register-form').submit(function(event) {
	if ($.trim($('.username-input-m').val()).length == 0) {
		$('.username-input-m').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.username-input-m').removeClass('is-invalid');
	}

	if ($.trim($('.password-input-m').val()).length == 0) {
		$('.password-input-m').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.password-input-m').removeClass('is-invalid');
	}

	if ($.trim($('.password-check-input-m').val()).length == 0) {
		$('.password-check-input-m').addClass('is-invalid');
		$('#password-check-error').html('Repeated password is required');
		event.preventDefault();
	}
	else {
		$('.password-check-input-m').removeClass('is-invalid');
	}

	if (($.trim($('.password-check-input-m').val()).length > 0) && ($.trim($('.password-input-m').val()).length > 0)) {
		if (!($.trim($('.password-input-m').val()) == $.trim($('.password-check-input-m').val()))) {
			$('.password-check-input-m').addClass('is-invalid');
			$('#password-check-error').html('Passwords don\'t match'); // this doesn't work
			event.preventDefault();
		}
		else {
			$('.password-check-input-m').removeClass('is-invalid');
		}
	}

});

$('.register-form-w').submit(function(event) {
	if ($.trim($('.username-input-w').val()).length == 0) {
		$('.username-input-w').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.username-input-w').removeClass('is-invalid');
	}

	if ($.trim($('.password-input-w').val()).length == 0) {
		$('.password-input-w').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.password-input-w').removeClass('is-invalid');
	}

	if ($.trim($('.password-check-input-w').val()).length == 0) {
		$('.password-check-input-w').addClass('is-invalid');
		$('#password-check-error-w').html('Repeated password is required');
		event.preventDefault();
	}
	else {
		$('.password-check-input-w').removeClass('is-invalid');
	}

	if (($.trim($('.password-check-input-w').val()).length > 0) && ($.trim($('.password-input-w').val()).length > 0)) {
		if (!($.trim($('.password-input-w').val()) == $.trim($('.password-check-input-w').val()))) {
			$('.password-check-input-w').addClass('is-invalid');
			$('#password-check-error-w').html('Passwords don\'t match'); // this doesn't work
			event.preventDefault();
		}
		else {
			$('.password-check-input-m').removeClass('is-invalid');
		}
	}
});

$('.login-form').submit(function(event) {
	$('.err').html('');

	if ($.trim($('.username-input-login-m').val()).length == 0) {
		$('.username-input-login-m').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.username-input-login-m').removeClass('is-invalid');
	}

	if ($.trim($('.password-input-login-m').val()).length == 0) {
		$('.password-input-login-m').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.password-input-login-m').removeClass('is-invalid');
	}
});

$('.login-form-w').submit(function(event) {
	$('.err').html('');
	if ($.trim($('.username-input-login-w').val()).length == 0) {
		$('.username-input-login-w').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.username-input-login-w').removeClass('is-invalid');
	}

	if ($.trim($('.password-input-login-w').val()).length == 0) {
		$('.password-input-login-w').addClass('is-invalid');
		event.preventDefault();
	}
	else {
		$('.password-input-login-w').removeClass('is-invalid');
	}
});


</script>
</body>