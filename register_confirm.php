<?php 

require 'config/config.php';

// check if all of them are filled
if ( !isset($_POST['username']) || empty($_POST['username'])
	|| !isset($_POST['password']) || empty($_POST['password']) ) {
	$error = "Please fill out all required fields.";
}

elseif (strcmp($_POST['password'], $_POST['password-check']) != 0) {
	$error = "Passwords don't match";
}

else {
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($mysqli->connect_errno) {
		echo $mysqli->connect_error;
		exit();
	}

	// check if username has been taken

	$exists = "SELECT * FROM users WHERE username = '" . trim($_POST['username']) . "';";
	$exists_result = $mysqli->query($exists);
	if (!$exists_result) {
		echo $mysqli->error;
		exit();
	}

	if ($exists_result->num_rows > 0) {
		$error = "Username has already been taken. Please choose another one.";
	}
	else {
		// hash password
		$pass = hash("sha256", $_POST['password']);

		// then insert
		$sql = "INSERT INTO users(username, password) VALUES('" . $_POST['username'] . "', '" . $pass . "');";
		$register = $mysqli->query($sql);

		if(!$register) {
			echo $mysqli->error;
			exit();
		}

		$mysqli->close();
	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registration Confirmation</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/shared.css">
	<style>
		.container {
			margin-top: 100px;
		}

	</style>
</head>
<body>

<div class="all">
	<?php include 'nav.php'; ?>

	<!--<div class="container">
		<div class="row d-flex justify-content-center text-center">
			<h1 class="col-12 mt-4">User Registration</h1>
		</div>
	</div> -->

	<div class="container">
		<div class="row mt-4 text-center">
			<div class="col-12">
				<?php if ( isset($error) && !empty($error) ) : ?>
					<div class="text-danger"><?php echo $error; ?></div>
				<?php else : ?>
					<div class="text-success"><?php echo $_POST['username']; ?> was successfully registered.</div>
				<?php endif; ?>
		</div>
	</div>

	<div class="row mt-4 mb-4 text-center">
		<div class="col-12">
			<a href="register.php" role="button" class="btn btn-primary">Login</a>
			<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" role="button" class="btn btn-light">Back</a>
		</div> <!-- .col -->
	</div> <!-- .row -->

</div> <!-- .container -->
</div>

</body>
</html>