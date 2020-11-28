<nav class="container-fluid p-2">
	<div class="row">
		<div class="col col-5 col-md-4 col-lg-3 align-self-center one">
			<a href="search_form.php">
				<img class="img-fluid zillowish" src="images/zillowish_light.png" alt=""/>
			</a>
		</div>

	<?php if(!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) : ?>
		<div class="d-none d-md-block col-1 col-md-3 col-lg-5">
		</div>

		<div class="col col-3 col-md-2 col-lg-2 two align-self-center d-flex justify-content-end">
			<a class="p-2 text-right" href="about.php" id="about_active"> About Us </a>
		</div>

		<div class="col col-4 col-md-3 col-lg-2 align-self-center d-flex justify-content-center three">
			<a class="p-2 text-right" href="register.php" id="signin_active">Sign in / Register</a>
		</div>
	<?php else: ?>

		<div class="col col-1 col-md-2 col-lg-4">
		</div>

		<div class="col col-3 col-md-2 col-lg-2 two align-self-center d-flex justify-content-end">
			<a class="p-2 text-right" href="about.php" id="about_active"> About Us </a>
		</div>
		<div class="col col-3 col-md-4 col-lg-3 align-self-center d-flex justify-content-center three">
			<div class="p-2">Hello, <?php echo $_SESSION["username"]; ?>!</div>
			<a class="p-2" href="logout.php">Logout</a>
		</div>
	<?php endif; ?>
	</div>
</nav>