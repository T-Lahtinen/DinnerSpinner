<?php
// Database information.
$db_name = "dinnerspinner";
$servername = "localhost";
$username = "root";
$password = "";

// Base URL variable.
$base_url = "http://localhost/dinnerspinner/";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $db_name);
mysqli_set_charset($conn, "utf8mb4");

// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

// Get restaurants
$sql_restaurants = "SELECT * FROM restaurants WHERE Status = 1 AND IsRestaurant = 1";
$db_restaurants = mysqli_query($conn, $sql_restaurants);

// Get foods
$sql_foods = "SELECT * FROM restaurants WHERE Status = 1 AND IsRestaurant = 0";
$db_foods = mysqli_query($conn, $sql_foods);

// Initiate variables as empty.
$style_check = $selection_check = $current_style = $current_selection = $stylesheet = $css_link = '';

// Check cookie values.
if (!empty($_COOKIE)) {
	foreach ($_COOKIE as $cookie_value) {
		$current_style = $_COOKIE['style_css'];
		$current_selection = $_COOKIE['selection_box'];
	}
}

// Insert correct style.
if ($current_style == "dinnerspinner") {
	$stylesheet = 'dinnerspinner-style.css';
	$style_check = 'checked';
} else {
	$stylesheet = 'dinnerspinner-dark-style.css';
}

// Have the spinner selection and lang toggled right.
if ($current_selection == "foods") {
	$selection_lang = 'vaihtoehtoa';
	$selection_check = 'checked';
} else {
	$selection_lang = 'ravintolaa';
}

// Data sorting function.
function array_orderby() {
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row)
				$tmp[$key] = $row[$field];
			$args[$n] = $tmp;
			}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

?>

<html xml:lang="fi" lang="fi" id="html">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">

		<title>Dinner Spinner</title>
		<meta name="author" content="Tommi Lahtinen">

		<!-- Facebook OpenGraph tags -->
		<meta property="og:title" content="Dinner Spinner">
		<meta property="og:type" content="website">
		<meta property="og:description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">
		<meta property="og:image" content="/dinnerspinner/images/dinnerspinner-some-image.png">
		<meta property='og:image:type' content='image/png' />
		<meta property='og:image:width' content='1200' />
		<meta property='og:image:height' content='673' />

		<!-- Twitter cards tags -->
		<meta name='twitter:card' content='summary_large_image' />
		<meta name="twitter:title" content="Dinner Spinner">
		<meta name="twitter:description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">

		<link href="css/normalize.css" rel="stylesheet" type="text/css">
		<link href="css/<?php echo $stylesheet; ?>" rel="stylesheet" type="text/css" id="style">
		<link href="css/responsive.css" rel="stylesheet" type="text/css">

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Urbanist">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Balsamiq+Sans">

		<link rel="icon" type="image/png" href="/dinnerspinner/images/favicon.png"/>

	</head>

	<body id="main" class="main">

		<div id="wrapper">
			<div id="title-bar">
				<h1 class="page-title">DINNER SPINNER</h1>
			</div>

			<div class="content-container">
				<div id="info-box-wrapper">
					<div id="info-box">
						<div class="about">Mikä ihmeen Dinner Spinner?<img src="/dinnerspinner/images/arrow-right.svg" class="show-more"><img src="/dinnerspinner/images/arrow-down.svg" class="show-less"></div>
						<div class="about-text">
							<p>Oletko koskaan pohtinut mitä söisit tänään? Saat rajattua valinnan muutamaan vaihtoehtoon, mutta päättäminen osoittautuu mahdottomaksi. Mikä neuvoksi?</p>
							<p>Dinner Spinner tietenkin!</p>
							<p>Näin pohdittiin LAURA Rekrytoinnin toimitiloissa arkisen lounaspaikka mietinnän yhteydessä. Pohdinta johti lopulta ajatukseen, joka oli pakko toteuttaa ihan vaikka vain nimensä vuoksi.</p>
							<p>Valitse valikosta haluamasi vaihtoehdot Itä-Pasilan ravintoloiden, tai ruokavaihtoehtojen joukosta. Valittuasi vähintään kaksi vaihtoehtoa voit pysäyttää Dinner Spinnerin ja antaa sen arpoa voittajan.</p>
							<p>Dinner Spinner on virallinen arvontatyökalu, eikä sen tulosta tule kyseenalaistaa.</p>
						</div>
					</div>
				</div>

				<div class='wheel-container'>
					<h1 class="winner-box"></h1>
					<div id="wheel" class="wheel-bg">
						<div id="canvas-area">
							<canvas id="canvas" width="600px" height="600px"></canvas>
						</div>
						<div class="wheel-bottom">
							<div id="spin" style="display: none;">
								<div class="spin-icon">
									<img src="/dinnerspinner/images/pause.svg">
								</div>
								Pysäytä
							</div>
							<div class="notice-message">
								<img src="/dinnerspinner/images/warning.svg" class="notice-image warning">
								<img src="/dinnerspinner/images/info-circle.png" class="notice-image info">
								Valitse vähintään 2 <span class="notice-text"><?php echo $selection_lang; ?></span>
								<img src="/dinnerspinner/images/info-circle.png" class="notice-image info second-info">
							</div>
						</div>
					</div>
				</div>

				<div class="selection-boxes">
					<div class="toggle-switch">
						<div class="switch-name">Ravintolat</div>
						<label class="switch">
							<input type="checkbox" class="selection-toggle" <?php echo $selection_check; ?>>
							<span class="slider round"></span>
						</label>
						<div class="switch-name">Ruoat</div>
					</div>
					<div class="selection-box-wrapper restaurants">
						<div class="selection-box">
							<div class="checkboxes">
							<p>Valitse spinneriin haluamasi ravintolat:</p>
								<div class="wheel-selection" action="index.php">
									<?php 
// Create the restaurant selections.
									$json_restaurants = array();
// Check that we have restaurants to use.
									if (mysqli_num_rows($db_restaurants) > 0) {
// Store all data to array.
										while ($rows = mysqli_fetch_assoc($db_restaurants)) {
											$json_restaurants[] = $rows;
										}
// Sort the restaurants alphabetically.
										$sorted_restaurants = array_orderby($json_restaurants, 'Name', SORT_ASC);
// Echo each individual selection to UI.
										foreach ($sorted_restaurants as $restaurant_value) {
											echo '<div class="selection-wrapper"><label class="selection-switch"><input type="checkbox" name="' . $restaurant_value['Name'] . '" value="' . $restaurant_value['ID'] . '" id="' . $restaurant_value['Name'] . '-color" class="restaurant-selection"' . '><span class="selection-slider round"></span></label><a href="' . $restaurant_value['Website'] . '" target="_blank">' . $restaurant_value['Name'] . '<img src="/dinnerspinner/images/external-link.svg" class="external-link"></a></div>';
										}
// Error if we didn't get any restaurants.
									} else {
										echo "Jotain meni vikaan :(";
									}
									?>

								</div>
							</div>
						</div>
					</div>

					<div class="selection-box-wrapper foods" style="display: none;">
						<div class="selection-box">
							<div class="checkboxes">
							<p>Valitse spinneriin haluamasi ruoat:</p>
								<div class="wheel-selection" action="index.php">
									<?php 
// Create the foods selection.
									$json_foods = array();
// Check that we have data to use.
									if (mysqli_num_rows($db_foods) > 0) {
// Store all data to array.
										while ($rows = mysqli_fetch_assoc($db_foods)) {
											$json_foods[] = $rows;
										}
// Sort the foods alphabetically.
										$sorted_foods = array_orderby($json_foods, 'Name', SORT_ASC);
// Echo each individual selection to UI.
										foreach ($sorted_foods as $food_value) {
											echo '<div class="selection-wrapper"><label class="selection-switch"><input type="checkbox" name="' . $food_value['Name'] . '" value="' . $food_value['ID'] . '" id="' . $food_value['Name'] . '-color" class="restaurant-selection"' . '><span class="selection-slider round"></span></label>' . $food_value['Name'] . '</div>';
										}
// Error if we didn't get any data.
									} else {
										echo "Jotain meni vikaan :(";
									}
// Merge the two arrays so we can access all data in javascript.
									$json_selected = array_merge($json_restaurants, $json_foods);
									?>
									<hr>									
								</div>
							</div>
						</div>
					</div>
					<div class="selection-box-wrapper" >
						<div class="selection-box">
							<div class="checkboxes">
								<div class="wheel-selection">
								<hr>
									<div class="selection-wrapper">
										<label class="selection-switch">
											<input type="checkbox" name="sounds" value="1" id="sounds" class="restaurant-selection">
											<span class="selection-slider round"></span>
										</label>Äänet
									</div>											
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="footer">
				<span class="copyright">Copyright © <?php echo date("Y"); ?></span>
				<span class="creators">
					<div>Created by:</div>
					<a href='https://www.linkedin.com/in/tommi-lahtinen' target='_blank'>Tommi Lahtinen<img src="/dinnerspinner/images/external-link.svg" class="external-link"></a>
					<a href='https://www.linkedin.com/in/iiro-hongisto-249072a4' target='_blank'>Iiro Hongisto<img src="/dinnerspinner/images/external-link.svg" class="external-link"></a>
				</span>
				<span class="style-switch">
					<div class="style-name">Nykypäivä</div>
					<label class="switch">
						<input type="checkbox" class="style-toggle" <?php echo $style_check; ?>>
						<span class="slider round"></span>
					</label>
					<div class="style-name">80-luku</div>
				</span>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
		<script src="<?php echo $base_url; ?>js/dinnerspinner-js.js"></script>

		<script> var json_selected = <?php echo json_encode($json_selected); ?>	</script>
		
		




	</body>
</html>