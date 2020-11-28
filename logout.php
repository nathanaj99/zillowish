<?php
	// to logout, remove the session variables
	// to destroy a session, must start a session
	session_start();
	session_unset();
	session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Logout</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/shared.css">
</head>
<body>
<div class="all">
		<?php include 'nav.php'; ?>
	<div class="container">
		<div class="row text-center">
			<h1 class="col-12 mt-4 mb-4">Logout</h1>
			<div class="col-12">You are now logged out.</div>
			<div class="col-12 mt-3">You can go to <a href="search_form.php">home page</a> or <a href="register.php">log in</a> again.</div>
		</div>
	</div>
</div>

</body>
</html>