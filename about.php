<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>About</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" href="css/shared.css">
	<style>

		#about_active {
			font-weight: bold;
		}

		h1 {
			margin-top: 50px;
			font-size: 30px;
		}

		h3 {
			font-size: 24px;
		}

		h5 {
			font-size: 18px;
		}

		.card {
			overflow: hidden;
			display: flex;
			flex-direction: row;
			justify-content: center;
			box-shadow: 5px 5px 10px grey;
			width: 80%;
			height: 300px;
			padding: 0;
			border-radius: 20px;
			margin-bottom: 30px;
		}

		.title {
			background-color: #3C91E6;
			flex: 1;
			padding: 1rem;
			overflow: hidden;
			color: white;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			text-align: center;
		}

		.first {
			background-color: #3C91E6;
		}

		.second {
			background-color: #3C91E6;
		}

		.content {
			background-color: white;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			text-align: center;
			flex: 1;
			padding: 1rem;
		}

		p {
			font-size: 12px;
			line-height: 20px;
			margin: 10px;
		}

		.page-header {
			text-align: center;
			margin-top: 20px;
			margin-bottom: 50px;
		}

		.prof {
			margin-left: auto;
			margin-right: auto;
			width: 90%;
		}

		.profile-picture {
			width: 200px;
			height: 200px;
			margin-right: auto;
			margin-left: auto;
			overflow: hidden;
			margin-bottom: 30px;
		}

		.profile-picture img {
			height: 100%;
			transform: translateX(-50%);
			margin-left: 50%;
		}

		.desc {
			box-shadow: 5px 5px 10px grey;
			border-radius: 2rem;
		}

		@media (min-width: 768px) {
			p {
				font-size: 15px;
			}

			.card {
				max-width: 700px;
			}


			.prof {
			width: 80%;
			max-width: 700px;
		}

			h1 {
				font-size: 40px;
			}

			h3 {
				font-size: 28px;
			}

			h5 {
			font-size: 21px;
		}
		}

		@media (min-width: 992px) {
			.card {
				max-width: 700px;
			}

			.profile-picture {
				width: 250px;
				height: 250px;
				overflow: hidden;
				margin-bottom: 50px;
			}
		}
	</style>
</head>

<body>
<div class="all">

	<?php include 'nav.php'; ?>

	<div class="page-header">
		<h1> <strong> About Us </strong> </h1>
	</div>


	<div class="row prof text-center">
		<div class="col col-12 ">
		<div class="rounded-circle profile-picture">
				<img src="images/nathan.jpg" alt="Nathan Jo" />
				</div>
		
			<h3> <strong> Nathan Jo </strong> </h3>
			<h5> Founder </h5>
			<p> Nathan is the visionary mastermind behind Zillow-ish. It started with an unhealthy amount of time searching through Zillow, fantasizing about million-dollar homes. But now, he has built a platform that makes it easier for other people to see aggregated home values in their desired areas! </p>
		</div>
	</div>



	<div class="page-header">
		<h1> <strong> Information </strong> </h1>
	</div>

	<div class="container-fluid card disclaimer">
		<div class="title first">
			<h2> Disclaimer </h2>
		</div>
		<div class="content">
			<p> The information displayed on this website are not intended to replace professional real estate advice. We are merely projecting prices based off of a handful of indicators and cannot guarantee how the housing market will fluctuate for your place of interest. Only use this tool as a starting point to your buying needs! </p>
		</div>
	</div>

	<div class="container-fluid card disclaimer">
		<div class="content">
			<p> We trained a machine learning model which took into account the area's change in wealth, home sales, and other factors. (We can't say anymore... it's proprietary!) </p>
		</div>
		<div class="title second">
			<h2> How we made the projections </h2>
		</div>
	</div>

</div>

<script>
// Get card element

</script>
</body>