<?php
// Database information.
$db_name = "dinner_spinner";
$servername = "localhost";
$username = "username";
$password = "password";

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
$style_check = $selection_check = $current_style = $current_selection = $stylesheet = $css_link = $animation_classes_dinner = $animation_classes_spinner = '';

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
	$animation_classes_dinner = "animate__animated animate__zoomInDown animate__infinite";
	$animation_classes_spinner = "animate__animated animate__zoomInDown animate__delay-2s animate__infinite";
} else {
	$stylesheet = 'dinnerspinner-dark-style.css';
	$animation_classes_dinner = "animate__animated animate__rollIn";
	$animation_classes_spinner = "animate__animated animate__rollIn animate__delay-2s";
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

$about_text = "<p>Valitse valikosta haluamasi vaihtoehdot Itä-Pasilan ravintoloiden, tai ruokavaihtoehtojen joukosta. Valittuasi vähintään kaksi vaihtoehtoa voit pysäyttää Dinner Spinnerin ja antaa sen arpoa voittajan.</p>
<p>Dinner Spinner on virallinen arvontatyökalu, eikä sen tulosta tule kyseenalaistaa.</p>";

?>
<!DOCTYPE html>
<html xml:lang="fi" lang="fi" id="html">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 

		<title>Dinner Spinner</title>
		<meta name="author" content="Tommi Lahtinen">
		<meta name="description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">

		<!-- Facebook OpenGraph tags -->
		<meta property="og:title" content="Dinner Spinner">
		<meta property="og:type" content="website">
		<meta property="og:description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">
		<meta property="og:image" content="../images/dinnerspinner-some-image.png">
		<meta property='og:image:type' content='image/png' />
		<meta property='og:image:width' content='1200' />
		<meta property='og:image:height' content='673' />

		<!-- Twitter cards tags -->
		<meta name='twitter:card' content='summary_large_image' />
		<meta name="twitter:title" content="Dinner Spinner">
		<meta name="twitter:description" content="Anna Dinner Spinnerin päättää mitä tänään syödään!">

		<link href="css/normalize.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
		<link href="css/<?php echo $stylesheet; ?>" rel="stylesheet" type="text/css" id="style">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
		<link href="css/responsive.css" rel="stylesheet" type="text/css">

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Urbanist">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Balsamiq+Sans">

		<link rel="icon" type="image/png" href="../images/favicon.png"/>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

	</head>

	<body id="main" class="main">

	<div id="about-dialog" title="Mikä ihmeen Dinner Spinner?"><?php echo $about_text; ?></div>

		<div id="wrapper">
			<div id="title-bar">
				<h1 class="page-title">DINNER SPINNER</h1>
			</div>

			<div class="content-container">
				<div id="info-box-wrapper">
					<div id="info-box">
						<div class="about">Mikä ihmeen Dinner Spinner?<img src="../images/question-circle.svg" alt="help" class="help-icon"></div>
						<div class="about-text">
							<?php echo $about_text; ?>
						</div>
						<div class="sounds-info">Kytke äänet päälle saadaksesi autenttisimman Dinner Spinner kokemuksen:</div>
						<div class="sounds-wrapper">
							<label class="selection-switch">
								<input type="checkbox" name="sounds" value="1" id="sounds" class="toggle-checkbox">
								<span class="selection-slider round"></span>
							</label>Äänet
						</div>
					</div>
				</div>

				<div class='wheel-container'>
					<div id="wheel" class="wheel-bg">
						<div id="canvas-area">
							<div class="canvas-bg-name dinner <?php echo $animation_classes_dinner; ?>">DINNER</div>
							<div class="canvas-bg-name spinner <?php echo $animation_classes_spinner; ?>">SPINNER</div>
							<canvas id="canvas" width="600" height="600"></canvas>
						</div>
						<div class="wheel-bottom">
							<div id="spin" style="display: none;">
								<div class="spin-icon">
									<img src="../images/pause.svg" alt="stop">
								</div>
								Arvo voittaja!
							</div>
							<div class="notice-message">
								<img src="../images/warning.svg" alt="warning icon" class="notice-image warning">
								<img src="../images/info-circle.png" alt="info icon" class="notice-image info">
								Valitse vähintään 2 <span class="notice-text"><?php echo $selection_lang; ?></span>
								<img src="../images/info-circle.png" alt="info icon" class="notice-image info second-info">
							</div>
						</div>
					</div>
				</div>

				<div class="selection-boxes">
					<div class="toggle-switch">
						<div class="switch-name toggle-restaurants">Ravintolat</div>
						<label class="switch">
							<input type="checkbox" class="selection-toggle" <?php echo $selection_check; ?>>
							<span class="slider round"></span>
						</label>
						<div class="switch-name toggle-foods">Ruoat</div>
					</div>
					<div class="selection-box-wrapper restaurants">
						<div class="selection-box">
							<div class="checkboxes">
							<p>Valitse spinneriin haluamasi ravintolat:</p>
								<div class="wheel-selection">
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
											echo '<div class="selection-wrapper"><label class="selection-switch"><input type="checkbox" name="' . $restaurant_value['Name'] . '" value="' . $restaurant_value['ID'] . '" id="restaurant-' . $restaurant_value['ID'] . '" class="restaurant-selection toggle-checkbox"' . '><span class="selection-slider round"></span></label><a href="' . $restaurant_value['Website'] . '" target="_blank"><span class="spinner-choice-name">' . $restaurant_value['Name'] . '</span><img src="../images/external-link.svg" alt="external link" class="external-link"></a></div>';
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
								<div class="wheel-selection">
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
											echo '<div class="selection-wrapper"><label class="selection-switch"><input type="checkbox" name="' . $food_value['Name'] . '" value="' . $food_value['ID'] . '" id="food-' . $food_value['ID'] . '" class="restaurant-selection toggle-checkbox"' . '><span class="selection-slider round"></span></label><span class="spinner-choice-name">' . $food_value['Name'] . '</span></div>';
										}
// Error if we didn't get any data.
									} else {
										echo "Jotain meni vikaan :(";
									}
// Merge the two arrays so we can access all data in javascript.
									$json_selected = array_merge($json_restaurants, $json_foods);
									?>
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="footer">
				<div class="copyright">
					<div class="copyright-stamp">Copyright © <?php echo date("Y"); ?></div>
					<div class="music-credit">Music from <a href='https://pixabay.com/fi/music/' target='_blank'>Pixabay</a> and <a href='https://freepd.com/' target='_blank'>FreePD.com</a></div>
				</div>
				<div class="creators">
					<div class="created-by">Created by:</div>
					<a href='https://www.linkedin.com/in/tommi-lahtinen' target='_blank'>Tommi Lahtinen<img src="../images/external-link.svg" alt="external link" class="external-link"></a>
					<a href='https://www.linkedin.com/in/iiro-hongisto-249072a4' target='_blank'>Iiro Hongisto<img src="../images/external-link.svg" alt="external link" class="external-link"></a>
				</div>
				<div class="style-switch">
					<div class="style-name">Nykypäivä</div>
					<label class="switch">
						<input type="checkbox" class="style-toggle" <?php echo $style_check; ?>>
						<span class="slider round"></span>
					</label>
					<div class="style-name">80-luku</div>
				</div>
			</div>
		</div>
									
		<script src="js/dinnerspinner-js.js"></script>

		<script> var json_selected = <?php echo json_encode($json_selected); ?>	</script>

	</body>
</html>