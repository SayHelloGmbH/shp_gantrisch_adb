<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| Global date helper functions
|
*/


/**
 * Show date
 *
 * @access public
 * @param mixed $date
 * @return void
 */
function parks_show_date($date) {
	$return = '';

	// Date from
	$return .= '<strong>'.$date['date_from']['date'].'</strong> ';
	if (!empty($date['date_from']['hour']) && ($date['date_from']['hour'] != '00')) {
		$return .= $date['date_from']['hour'].":".$date['date_from']['minute'];
	}

	// Date to
	if (
		isset($date['date_to'])
		&&
		!empty($date['date_to'])
		&&
		!empty($date['date_to']['date'])
		&&
		(
			($date['date_from']['date'] != $date['date_to']['date'])
			||
			(!empty($date['date_to']['hour']) && ($date['date_to']['hour'] != '00'))
		)
	) {
		$return .= ' - ';
		if ($date['date_from']['date'] != $date['date_to']['date']) {
			$return .= '<strong>'.$date['date_to']['date'].'</strong> ';
		}
		if (!empty($date['date_to']['hour']) && ($date['date_to']['hour'] != '00')) {
			$return .= $date['date_to']['hour'].":".$date['date_to']['minute'];
		}
	}

	return $return;
}


/**
 * Convert MySQL to a form format
 *
 * @access public
 * @param mixed $mysql_date
 * @return void
 */
function parks_mysql2form($mysql_date) {
	$return = array();

	$return['date'] = substr($mysql_date, 0, 10);
	$return['hour'] = substr($mysql_date, 11, 2);
	$return['minute'] = substr($mysql_date, 14, 2);

	return $return;
}


/**
 * Convert MySQL to a date format
 *
 * @access public
 * @param mixed $mysql_date
 * @param bool $time (default: false)
 * @param bool $ts (default: false)
 * @return void
 */
function parks_mysql2date($mysql_date, $time = false, $ts = false) {
	$date = substr($mysql_date, 0, 10);
	$date_time = '';
	if ($time != false) {
		$date_time = ' '.substr($mysql_date, 11);
	}

	$date_explode = explode("-", $date);

	if (count($date_explode) < 3 || (intval($date_explode[0]) == 0) && (intval($date_explode[1]) == 0) && (intval($date_explode[2]) == 0)) {
		return '';
	}

	$date_explode_ts = mktime(0, 0, 0, $date_explode[1], $date_explode[2], $date_explode[0]);
	if ($ts == true) {
		return $date_explode_ts;
	}
	return date('d.m.Y', $date_explode_ts).$date_time;
}



/**
 * Split hours and minutes by minutes
 *
 * @param int $total_minutes
 * @return void
 */
function split_hours_and_minutes($total_minutes) {
    if ($total_minutes >= 1) {

		// Split total minutes into hours and minutes
	    $hours = floor($total_minutes / 60);
		$minutes = ($total_minutes % 60);
		
		// Return result
		return array(
			'hours' => $hours,
			'minutes' => $minutes
		);

	}
	
	return FALSE;
}



/**
 * Activities: Get time required info and return formatted value
 *
 * @param object $offer
 * @param object $lang
 * @param bool $short_labels (default: FALSE)
 * @return string $return
 */
function activity_get_time_required($offer, $lang, $short_labels = FALSE) {

	// Init
	$return = '';

	// Format minutes
	if ( ! empty($offer->time_required_minutes)) {

		// Split hours and minutes
		$time_required = split_hours_and_minutes($offer->time_required_minutes);

		// Set hours
		$hours = '';
		if (!empty($time_required['hours']) && ($time_required['hours'] > 0)) {

			// Set label for hours (singular, plural)
			$label_hours = 'h';
			if ($short_labels == FALSE) {
				$label_hours = ($time_required['hours'] == 1) ? $lang->get('offer_hour') : $lang->get('offer_hours');
			}

			// Set hours
			$hours = intval($time_required['hours']).' '.$label_hours;

		}
		
		// Set minutes
		$minutes = '';
		if (!empty($time_required['minutes']) && ($time_required['minutes'] > 0)) {

			// Set label for hours (singular, plural)
			$label_minutes = 'min';
			if ($short_labels == FALSE) {
				$label_minutes = ($time_required['minutes'] == 1) ? $lang->get('offer_minute') : $lang->get('offer_minutes');
			}

			// Set minutes
			$minutes = ' '.intval($time_required['minutes']).' '.$label_minutes;

		}

		// Return hours and minutes
		$return = $hours.$minutes; 

	} 

	// Format time category
	else if ( ! empty($offer->time_required)) {

		// Return category
		$return = $offer->time_required;

	}

	return $return;
}