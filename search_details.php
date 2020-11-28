<?php

require "config/config.php";

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
	echo $mysqli->connect_error;
	exit();
}

if (!isset($_GET['location']) || empty($_GET['location'])) {
	echo "Location doesn't seem right. Please try again.";
	exit();
}

$city = false;
$state = false;
$zip = false;

if (!isset($_GET['which'])) {
	$array_loc = explode(',', $_GET['location']);
	$count = count($array_loc);

	// In the client-side, we already checked if there is no ,. So we are guaranteed to get at least a state-level query (count >= 2)
	if ($count == 2) {
		$state = true;
	}
	elseif ($count >= 3) {
		$city = true;
	}
}
else {
	$zip = true;
	// Test again if all are numbers
	$zip_result = $_GET['location'];
}


if ($city) {
	// IF CITY, there is some ambiguity because we might have two cities with the same name
	$state_result = trim($array_loc[$count-2]);
	if (strlen($state_result) > 2) {
		$state_result = substr($state_result, 0, 2);
	} 
	$city_result = trim($array_loc[$count-3]);

	// This is an exception handler when New York City can be interpreted as New York or City
	if ($city_result == 'New York City') {
		$city_result = 'New York';
	}

	$sql = "SELECT * FROM cities WHERE city_name LIKE '%" . $city_result . "%' AND state_abbr = '". $state_result . "';";

	$results = $mysqli->query($sql);

	if(!$results) {
		echo $mysqli->error;
		exit();
	}

	// If there are many results, then take the result with the highest value
	$highest = 0;
	$highest_id = 0;
	$no_result = false;
	if ($results->num_rows == 0) {
		$no_result = true;
	}
	else {
		while ($row = $results->fetch_assoc()) {
			$value = floatval($row['2021']);
			if ($value > $highest) {
				$highest_id = $row['city_id'];
				$highest = $value;
			}
		}
	}

	if (!$no_result) {
		$sql = "SELECT * FROM cities WHERE city_id = " . $highest_id . ";";

		$results = $mysqli->query($sql);

		if(!$results) {
			echo $mysqli->error;
			exit();
		}

		$timeseries = $results->fetch_assoc();
		$name = $timeseries['city_name'];

		$type = 'city';
		$val = $highest_id;
	}
}

elseif ($state) {
	$state_result = trim($array_loc[$count-2]);
	if (strlen($state_result) > 2) {
		$state_result = trim(str_replace("State", "", $state_result));
		$sql = "SELECT * FROM states WHERE state_name LIKE '%". $state_result . "%';";
	}
	else {
		$sql = "SELECT * FROM states WHERE state_abbr = '". $state_result . "';";
	}
	$results = $mysqli->query($sql);

	if(!$results) {
		echo $mysqli->error;
		exit();
	}

	$no_result = false;
	if ($results->num_rows == 0) {
		$no_result = true;
	}
	else {
		$timeseries = $results->fetch_assoc();
		$name = $timeseries['state_name'];

		$type = 'state';
		$val = $state_result;
	}
}

elseif ($zip) {
	$sql = "SELECT * FROM zip WHERE zip_code = ". $zip_result . ";";
	$results = $mysqli->query($sql);

	if(!$results) {
		echo $mysqli->error;
		exit();
	}
	$no_result = false;
	if ($results->num_rows == 0) {
		$no_result = true;
	}
	else {
		$timeseries = $results->fetch_assoc();
		$name = $timeseries['zip_code'];

		$type = 'zip';
		$val = $zip_result;
	}
}

if (!$no_result) {
	foreach($timeseries as $key => $value) {
		if (is_null($value)) {
			$timeseries[$key] = 0;
		}
	}

	if (($timeseries['2019'] == 0) || ($timeseries['2020'] == 0)) {
		$pct_increase_past = 'N/A';
		$status_past = 'na';
	}
	else {
		$pct_increase_past = round(($timeseries['2020'] - $timeseries['2019']) / $timeseries['2019'] * 100, 2);
		if ($pct_increase_past >= 0) {
			$status_past = 'pos';
		}
		else {
			$status_past = 'neg';
		}
		$pct_increase_past = strval(abs($pct_increase_past)) . '%';
	}

	if (($timeseries['2020'] == 0) || ($timeseries['2021'] == 0)) {
		$pct_increase_future = 'N/A';
		$status_future = 'na';
	}
	else {
		$pct_increase_future = round(($timeseries['2021'] - $timeseries['2020']) / $timeseries['2020'] * 100, 2);
		if ($pct_increase_future >= 0) {
			$status_future = 'pos';
		}
		else {
			$status_future = 'neg';
		}
		$pct_increase_future = strval(abs($pct_increase_future)) . '%';
	}
}


?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Search Details</title>
	<script src="http://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" href="css/shared.css">
	<script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDrbI4UnjWSUAw-iXLRrbhRlNDDe87rPao&callback=initMap&libraries=places&v=weekly" defer
    ></script>
	<style>


		.logo {
			margin-top: 100px;
			margin-bottom: 20px;
		}


		.form-rounded {
			margin-top: 10px;
			border-radius: 2rem;
			box-shadow: 5px 5px 10px grey;
			border: 0;
			height: 40px;
		}


		.fa{
		    box-sizing: border-box;
		    padding: 9px;
		    width: 45px;
		    height: 45px;
		    position: absolute;
		    top: 0;
		    right: 0;
		    color: #07051a;
		    text-align: center;
		    transition: all 1s;
		}

		.zillowish {
			width: 100%;
		}


		.stats {
			margin-top: 50px;
		}

		.overview-title {
			margin-top: 10px;
			margin-bottom: 20px;
		}


		.map-cont {
			height: 500px;
		}

		#map {
        	height: 400px;
        	width: 95%;
      	}

      	.map {
      		margin-top: 40px;
      		margin-bottom: 50px;
      	}

      	.fa-arrow-up {
      		color: green;
      	}

      	.fa-arrow-down {
      		color: red;
      	}

      	.pct {
      		font-size: 26px;
      	}

      	.modal {
      		z-index: 1;
      		display: none;
      		padding: 100px;
      		width: 100%;
      		height: 100%;
      		background-color: rgba(0,0,0,0.4);
		}

		.change {
			/*border: 2px solid grey;*/
			border-radius: 2rem;
			box-shadow: 3px 3px 7px grey;
		}

		.change:hover {
			box-shadow: 4px 4px 8px 2px grey;
		}


		.modal-box {
		  background-color: white;
		  margin: auto;
		  padding: 30px;
		  border: 1px solid #888;
		  width: 100%;
		  border-radius: 2rem;
		}

		.close {
		  color: lightgrey;
		  float: right;
		  font-size: 28px;
		  font-weight: bold;
		}

		.close:hover,
		.close:focus {
		  color: grey;
		  text-decoration: none;
		  cursor: pointer;
		}

		.form-group {
			width: 100%;
		}

		#data_change {
			width: 60%;
			border-radius: 2rem;
			height: 40px;
			border-width: 2px;
			/*box-shadow: 1px 1px 8px grey;*/
		}

		.input {
			margin-bottom: 20px;
			margin-top: 40px;
		}

		.btn {
			margin-left: 10px;
			margin-right: 10px;
		}

		@media (min-width: 768px) {

			.modal-box {
				width: 60%;
				max-width: 700px;
			}

			#map {
        	width: 70%;
      	}
		}




	</style>
</head>
<body>
	<div class="all">
		<?php include 'nav.php'; ?>
		<div class="stuff">


		<div class="container-fluid stats">
			<div class="row">
				<div class="col col-12 col-lg-8">
					<canvas id="projection"></canvas>
				</div>

				<div class="col col-12 col-lg-4 text-center">
					<?php if ($no_result): ?>
						<h2 class="overview-title"> Sorry! We don't have any data for <strong> <?php echo $_GET['location']; ?> </strong> </h2>

					<?php else: ?>
					<h2 class="overview-title"> Market Overview for <strong>
						<?php echo $name; ?> </strong>
					</h2>
					<p class="overview-description"> The typical home value of homes in <?php echo $name;?> is $<?php echo number_format($timeseries['2020'], 2);?>. This value is seasonally adjusted and only includes the middle price tier of homes. </p>

					<div class="row justify-content-center">
						<div class="col-5 col-sm-4 col-md-3 col-lg-5 change mx-2">
							<div class="row justify-content-center pt-2 pb-0">
								<p> <strong> 2019-2020 </strong> </p>
							</div>

							<?php if (strcmp($status_past, 'na') == 0):?>
								<div class row justify-content-center>
									<p class="pct"> <?php echo $pct_increase_past; ?> </p>
								</div>

							<?php elseif (strcmp($status_past, 'pos') == 0): ?>
								<div class="row d-flex justify-content-center">
									<div class="col-5 col-md-5 col-lg-4 text-right pr-md-1">
										<i class="fas fa-arrow-up fa-2x"></i>
									</div>
									<div class="col-7 col-md-7 col-lg-8 text-left pl-md-2">
										<p class="pct">  <?php echo $pct_increase_past;?> </p>
									</div>
								</div>

							<?php else: ?>
								<div class="row d-flex justify-content-center">
									<div class="col-5 col-md-5 col-lg-4 text-right pr-md-1">
										<i class="fas fa-arrow-down fa-2x"></i>
									</div>
									<div class="col-7 col-md-7 col-lg-8 text-left pl-md-2">
										<p class="pct">  <?php echo $pct_increase_past;?> </p>
									</div>
								</div>
							<?php endif; ?>
						</div>


						<div class="col-5 col-sm-4 col-md-3 col-lg-5 change mx-2">
							<div class="row justify-content-center pt-2 pb-0">
								<p> <strong> 2020-2021 </strong> </p>
							</div>
							<?php if (strcmp($status_future, 'na') == 0):?>
								<div class row justify-content-center>
									<p class="pct"> <?php echo $pct_increase_future; ?> </p>
								</div>

							<?php elseif (strcmp($status_future, 'pos') == 0): ?>
								<div class="row d-flex justify-content-center">
									<div class="col-5 col-md-5 col-lg-4 text-right pr-md-1">
										<i class="fas fa-arrow-up fa-2x"></i>
									</div>
									<div class="col-7 col-md-7 col-lg-8 text-left pl-md-2">
										<p class="pct">  <?php echo $pct_increase_future;?> </p>
									</div>
								</div>

							<?php else: ?>
								<div class="row d-flex justify-content-center">
									<div class="col-5 col-md-5 col-lg-4 text-right pr-md-1">
										<i class="fas fa-arrow-down fa-2x"></i>
									</div>
									<div class="col-7 col-md-7 col-lg-8 text-left pl-md-2">
										<p class="pct">  <?php echo $pct_increase_future;?> </p>
									</div>
								</div>
							<?php endif; ?>
						</div>

				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="container-fluid map">
			<div class="row">
				<div class="col col-12 d-flex justify-content-center">
					<div id="map"></div>
				</div>
			</div>
		</div>


        <div id="modal_admin" class="modal">
        	<form action="edit_delete_confirmation.php" method="POST" class="modal-box">
        		<span class="close"> &times; </span>

        		<h4 id="modal_desc"> Edit or Delete *year* data for *place* </h4>

	        		<div class="form-group row justify-content-center input">
	        			<input type="text" class="form-control" id="data_change" name="data_change"> 
	        		</div>

        		<input type="hidden" name="location" value="<?php echo $_GET['location']; ?>">
        		<input type="hidden" name="which" value="<?php echo $_GET['which']; ?>">
        		<input type="hidden" name="year" id="year_input">
        		<input type="hidden" name="type" value="<?php echo $type;?>">
        		<input type="hidden" name="val" value="<?php echo $val;?>">

        		<div class="row justify-content-center">
	        		<button type="submit" class="btn btn-warning" name="update" value="update">Edit</button>
	        		<button type="submit" class="btn btn-danger" name="delete" value="delete">Delete </button>
	        	</div>
	        </form>
        </div>
    </div>
</div>
</div>

  <script src="js/chart.js/dist/Chart.bundle.min.js"></script>

<script>

var years = ["2010", "2011", "2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021"];
let labels = [];
// let data = [];
for (let j = 0; j < years.length; j++) {
	let year = years[j]
	let date = new Date(year);
	labels.push(date);
}

<?php if (!$no_result):?>
	let data = [<?php echo $timeseries['2010']; ?>, <?php echo $timeseries['2011']; ?>, <?php echo $timeseries['2012']; ?>, <?php echo $timeseries['2013']; ?>, <?php echo $timeseries['2014']; ?>, <?php echo $timeseries['2015']; ?>, <?php echo $timeseries['2016']; ?>, <?php echo $timeseries['2017']; ?>, <?php echo $timeseries['2018']; ?>, <?php echo $timeseries['2019']; ?>, <?php echo $timeseries['2020']; ?>, <?php echo $timeseries['2021']; ?>];

	for (let i = 0; i < data.length; i++) {
		if (data[i] == 0) {
			data[i] = null;
		}
	}
<?php else: ?>
	let data = [null, null, null, null, null, null, null, null, null, null, null, null];
<?php endif; ?>


let data1 = {
	labels: labels,
	datasets: [{
		fill: false,
		label: 'Average Home Value',
		data: data,
		borderColor: '#fe8b36',
		backgroundColor: '#fe8b36'
	}]
}

let options = {
	spanGaps: true,
	type: 'line',
	data: data1,
	options: {
		fill: false,
		responsive: true,
		scales: {
				xAxes: [{
					type: 'time',
					time: {
						unit: 'year'
					},
					scaleLabel: {
						display: true,
						labelString: "Date"
					}
				}],
				yAxes: [{
					ticks: {
						beginAtZero: true
					},
					display: true,
					scaleLabel: {
						display: true,
						labelString: "Average Home Value"
					}
				}]
		}
	}
}

let ctxL = document.getElementById("projection").getContext('2d');
let chart = new Chart(ctxL, options);

let modal = document.getElementById('modal_admin');
let close = document.getElementsByClassName('close')[0];


<?php if ((isset($_SESSION['username'])) && (strcmp($_SESSION['username'], 'admin') == 0)): ?>
	document.getElementById("projection").onclick = function(evt){
	    var active = chart.lastActive[0];

	    if (active != undefined) {
	    	var year = years[active._index];
	    	var location = '<?php echo $_GET['location']; ?>';
	    	modal.style.display = "block";

	    	$('#modal_desc').html('Edit or Delete '.concat(year, " data for ", location));
	    	$('#data_change').attr('value', data[active._index]);
	    	$('#year_input').attr('value', year);
	    }
	};

	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	};

	close.onclick = function() {
		modal.style.display = "none";
	};

<?php endif; ?>



function initMap() {
	map = new google.maps.Map(document.getElementById("map"), {
	  	center: { lat: -34.397, lng: 150.644 },
	 	zoom: 12,
	 	mapTypeId: "roadmap",
	});

	infowindow = new google.maps.InfoWindow();


    let request = {
      query: "<?php echo $_GET['location']; ?>",
      fields: ["name", "geometry", "place_id"],
    };

    var id = 0;


    service = new google.maps.places.PlacesService(map);
    service.findPlaceFromQuery(request, (results, status) => {
      if (status === google.maps.places.PlacesServiceStatus.OK) {
      	createMarker(results[0]);

        map.setCenter(results[0].geometry.location);

        id = results[0].place_id;
      }
    });

}
  

function createMarker(place) {
    let marker = new google.maps.Marker({
      map,
      position: place.geometry.location,
    });

    google.maps.event.addListener(marker, "click", () => {
      infowindow.setContent(place.name);
      infowindow.open(map);
    });
}
  


</script>

</body>
</html>