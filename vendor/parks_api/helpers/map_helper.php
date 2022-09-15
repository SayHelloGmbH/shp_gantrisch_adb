<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| Global map helper functions
|
*/


/**
 * Convert latitude and longitued values to CH1903 format
 *
 * @access public
 * @param mixed $latitude
 * @param mixed $longitude
 * @return void
 */
function convertLatLonToCH1903($latitude, $longitude) {
	$return = array();

	$return['x'] = intval(WGStoCHx($latitude, $longitude) + 1000000);
	$return['y'] = intval(WGStoCHy($latitude, $longitude) + 2000000);

	return $return;
}


/**
 * Convert WGS lat/long (° dec) to CH y
 *
 * @access public
 * @param mixed $lat
 * @param mixed $long
 * @return void
 */
function WGStoCHy($lat, $long) {

	// Converts degrees dec to sex
	$lat = DECtoSEX($lat);
	$long = DECtoSEX($long);

	// Converts degrees to seconds (sex)
	$lat = DEGtoSEC($lat);
	$long = DEGtoSEC($long);

	// Axiliary values (% Bern)
	$lat_aux = ($lat - 169028.66)/10000;
	$long_aux = ($long - 26782.5)/10000;

	// Process Y
	$y = 600072.37
		 + 211455.93 * $long_aux
		 -	10938.51 * $long_aux * $lat_aux
		 -			0.36 * $long_aux * pow($lat_aux,2)
		 -		 44.54 * pow($long_aux,3);

	return $y;
}


/**
 * Convert WGS lat/long (° dec) to CH x
 *
 * @access public
 * @param mixed $lat
 * @param mixed $long
 * @return void
 */
function WGStoCHx($lat, $long) {

	// Converts degrees dec to sex
	$lat = DECtoSEX($lat);
	$long = DECtoSEX($long);

	// Converts degrees to seconds (sex)
	$lat = DEGtoSEC($lat);
	$long = DEGtoSEC($long);

	// Axiliary values (% Bern)
	$lat_aux = ($lat - 169028.66)/10000;
	$long_aux = ($long - 26782.5)/10000;

	// Process X
	$x = 200147.07
		 + 308807.95 * $lat_aux
		 +	 3745.25 * pow($long_aux,2)
		 +		 76.63 * pow($lat_aux,2)
		 -		194.56 * pow($long_aux,2) * $lat_aux
		 +		119.79 * pow($lat_aux,3);

	return $x;

}



/**
 * Convert CH y/x to WGS lat
 *
 * @access public
 * @param mixed $y
 * @param mixed $x
 * @return void
 */
function CHtoWGSlat($y, $x) {

	// Converts militar to civil and	to unit = 1000km
	// Axiliary values (% Bern)
	$y_aux = ($y - 600000)/1000000;
	$x_aux = ($x - 200000)/1000000;

	// Process lat
	$lat = 16.9023892
			 +	3.238272 * $x_aux
			 -	0.270978 * pow($y_aux,2)
			 -	0.002528 * pow($x_aux,2)
			 -	0.0447	 * pow($y_aux,2) * $x_aux
			 -	0.0140	 * pow($x_aux,3);

	// Unit 10000" to 1 " and converts seconds to degrees (dec)
	$lat = $lat * 100/36;

	return $lat;

}


/**
 * Convert CH y/x to WGS long
 *
 * @access public
 * @param mixed $y
 * @param mixed $x
 * @return void
 */
function CHtoWGSlong($y, $x) {

	// Converts militar to civil and	to unit = 1000km
	// Axiliary values (% Bern)
	$y_aux = ($y - 600000)/1000000;
	$x_aux = ($x - 200000)/1000000;

	// Process long
	$long = 2.6779094
				+ 4.728982 * $y_aux
				+ 0.791484 * $y_aux * $x_aux
				+ 0.1306	 * $y_aux * pow($x_aux,2)
				- 0.0436	 * pow($y_aux,3);

	// Unit 10000" to 1 " and converts seconds to degrees (dec)
	$long = $long * 100/36;

	return $long;

}


/**
 * Convert SEX DMS angle to DEC
 *
 * @access public
 * @param mixed $angle
 * @return void
 */
function SEXtoDEC($angle) {

	// Extract DMS
	$deg = intval( $angle );
	$min = intval( ($angle-$deg)*100 );
	$sec = ((($angle-$deg)*100) - $min) * 100;

	// Result in degrees sex (dd.mmss)
	return $deg + ($sec/60 + $min)/60;

}


/**
 * Convert DEC angle to SEX DMS
 *
 * @access public
 * @param mixed $angle
 * @return void
 */
function DECtoSEX($angle) {

	// Extract DMS
	$deg = intval( $angle );
	$min = intval( ($angle-$deg)*60 );
	$sec =	((($angle-$deg)*60)-$min)*60;

	// Result in degrees sex (dd.mmss)
	return $deg + $min/100 + $sec/10000;

}


/**
 * Convert Degrees angle to seconds
 *
 * @access public
 * @param mixed $angle
 * @return void
 */
function DEGtoSEC($angle) {

	// Extract DMS
	$deg = intval( $angle );
	$min = intval( ($angle-$deg)*100 );
	$sec = ((($angle-$deg)*100) - $min) * 100;

	// Result in degrees sex (dd.mmss)
	return $sec + $min*60 + $deg*3600;

}