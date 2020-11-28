<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Main Search</title>
	<script
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDrbI4UnjWSUAw-iXLRrbhRlNDDe87rPao&libraries=places"> </script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

	<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

	<link rel="stylesheet" href="css/shared.css">

		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>


	<style>
		.logo {
			margin-top: 100px;
		}

		.desc {
			margin-bottom: 50px;
			text-align: center;
		}

		.error {
			text-align: center;
			font-size: 12px;
			visibility: hidden;
			color: red;
		}

		.zip {
			display: none;
		}

		.state {
			display: inline-block;
		}

		form {
			 position: relative;
			 width: 60%;
			 height: 150px;
		}

		.choice {
			margin-right: 50px;
			margin-bottom: 30px;
			/*left: 15%;*/
		}

		.search{
			margin-top: 20px;
			position: absolute;
		    left: 52%;
		    transform: translate(-50%,-50%);
		    transition: all 1s;
		    width: 50px;
		    height: 50px;
		    background: white;
		    box-sizing: border-box;
		    border-radius: 25px;
		    border: 2px solid white;
		    box-shadow: 5px 5px 10px grey;
		    padding: 5px;
		}


		input{
		    width: 100%;
		    height: 35px;
		    outline: 0;
		    border: 0;
		    display: none;
		}

		.fa{
		    box-sizing: border-box;
		    padding: 10px;
		    padding-left: 22px;
		    width: 45px;
		    height: 45px;
		    position: absolute;
		    top: 0;
		    right: 0;
		    color: #07051a;
		    text-align: center;
		    transition: all 1s;
		}

		.one {
			visibility: hidden;
		}


		.more {
			margin-top: 70px;
		}



			.desc {
				padding-right: 40px;
				padding-left: 40px;
			}


		@media (min-width: 768px) {

			.choice {
				margin-right: 40px;
			}

		}

	</style>
</head>
<body>

<div class="all">
	<?php include 'nav.php'; ?>

	<div class="container justify-content-center logo">
		<div class="row">
			<div class="col-12 d-flex justify-content-center">
				<img class="img-fluid" src="images/zillowish_light.png" alt="" />
			</div>
		</div>
	</div>

	<div class="container justify-content-center desc">
		<div class="row">
			<div class="col-12 d-flex justify-content-center">
				<a> Type in a state, city, or ZIP code to see projected home values in your area!</a>
			</div>
		</div>
	</div>




	 
	<div class="container d-flex justify-content-center f">
		<form action="search_details.php" method="GET" class="state">
			<div class="form-group custom-control custom-switch d-flex justify-content-center choice">
	            <input type="checkbox" id="toggle" checked data-toggle="toggle" data-on="Zip Code" data-off="City/State" data-onstyle="secondary" data-offstyle="success" name="which">

	    	</div>
			<div class="form-group row justify-content-center search">
				<div class="col-9">
					<input type="text" id="searchLocation" name="location">
						<i class="fa fa-search"> </i>
				</div>
			</div> 
		</form>


	</div> 

	<div class="row">
			<div class="col-12">
			<div class="error">  </div>
		</div>
	</div>

</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.3/highlight.min.js"></script>

	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>



	<script>
		let input = document.getElementById('searchLocation');
		let tog = $('#toggle');

		if (!$('#toggle').is(':checked')) {
			const autocomplete = new google.maps.places.Autocomplete(input);
			autocomplete.setComponentRestrictions({
				country: ["us"],
			});
		}

		$('#toggle').on('change', function () {
			if ($(this).is(':checked')) {
				//google.maps.event.clearInstanceListeners(autocomplete);
				google.maps.event.clearInstanceListeners($("#searchLocation")[0]);
			}
			else {
				let autocomplete = new google.maps.places.Autocomplete(input);
				autocomplete.setComponentRestrictions({
					country: ["us"],
				});
			}
		});

		// Interaction with the search form
		$('.search').mouseenter(function () {
		    $('.search').css({"width": "100%", "cursor": "pointer"});
		    $('input').delay(800).css({"display": "block"});
		});

		$('form').click(function (e) {
		    e.stopPropagation();
		});

		$('html').click(function () {
		    $('.search').css({"width": "50px"});
		    $('input').css({"display": "none"});
		});


		// Client-side validation of the search form
		$('form').submit(function(event) {
			let input = $('#searchLocation').val();
			if (input.length == 0) {
				event.preventDefault();
				$('.error').html('Please type in a value before submitting.');
				$('.error').css('visibility', 'visible');
			}

			if (!$('#toggle').is(':checked')) { // if not checked, then state/city
				if (!input.includes(",")) {
					event.preventDefault();
					$('.error').html('Invalid input. Please type a valid city/state.');
					$('.error').css('visibility', 'visible');
				}
			}
			else { // if zip, only allow one entry and it has to be all numbers
				if (input.includes(",") || !/^\d+$/.test(input)) {
					event.preventDefault();
					$('.error').html("Please enter only a ZIP code value");
					$('.error').css('visibility', 'visible');
				}

			}


		});

		window.onload = function() {
			$('#toggle').prop('checked', true).change();
		}

	

		 // $('#toggle').click(;

</script>
</body>
</html>