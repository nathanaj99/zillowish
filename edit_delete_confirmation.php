<?php

require "config/config.php";

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
	echo $mysqli->connect_error;
	exit();
}


if (isset($_POST['update'])) { // user wants to update
	if (!isset($_POST['data_change']) || empty($_POST['data_change']) || !is_numeric($_POST['data_change'])) {
		$error = "Please type in a valid decimal value.";
	}
	else {
		if (strcmp($_POST['type'], 'city') == 0) { // then update city table
			$sql = "UPDATE cities SET `" . $_POST['year'] . "` = " . $_POST['data_change'] . " WHERE city_id = " . $_POST['val'] . ";";
		}
		elseif (strcmp($_POST['type'], 'state') == 0) { // state
			$sql = "UPDATE states SET `" . $_POST['year'] . "` = " . $_POST['data_change'] . " WHERE state_abbr = " . $_POST['val'] . ";";
		}
		else { // zip
			$sql = "UPDATE zip SET `" . $_POST['year'] . "` = " . $_POST['data_change'] . " WHERE zip_code = " . $_POST['val'] . ";";
		}
		
		$results = $mysqli->query($sql);

		if(!$results) {
			echo $mysqli->error;
			exit();
		}
	}
}
else { // user wants to delete
	if (strcmp($_POST['type'], 'city') == 0) { // then update city table
		$sql = "UPDATE cities SET `" . $_POST['year'] . "` = null WHERE city_id = " . $_POST['val'] . ";";
	}
	elseif (strcmp($_POST['type'], 'state') == 0) { // state
		$sql = "UPDATE states SET `" . $_POST['year'] . "` = null WHERE state_abbr = " . $_POST['val'] . ";";
	}
	else { // zip
		$sql = "UPDATE zip SET `" . $_POST['year'] . "` = null WHERE zip_code = " . $_POST['val'] . ";";
	}

	$results = $mysqli->query($sql);

	if(!$results) {
		echo $mysqli->error;
		exit();
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

	<div class="container">
		<div class="row mt-4 text-center">
			<div class="col-12">
				<?php if ( isset($error) && !empty($error) ) : ?>
					<div class="text-danger"><?php echo $error; ?></div>
				<?php else : ?>
					<?php if (isset($_POST['update'])): ?>
						<div class="text-success"> Successfully updated. </div>
					<?php else: ?>
						<div class="text-success"> Successfully deleted. </div>
					<?php endif; ?>
				<?php endif; ?>
		</div>
	</div>

	<div class="row mt-4 mb-4 text-center">
		<div class="col-12">
			<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" role="button" class="btn btn-light">Back</a>
		</div> <!-- .col -->
	</div> <!-- .row -->

</div> <!-- .container -->
</div>

</body>
</html>