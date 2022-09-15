<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| Main model
|
*/


class ParksModel {


	/**
	 * API
	 */
	public $api;


	/**
	 * Offer levels for difficulty
	 */
	public $levels;


	/**
	 * Target groups
	 */
	public $target_groups = array();


	/**
	 * All categories
	 */
	public $categories = array();


	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string
	 * @return void
	 */
	function __construct($api) {

		// Api instance
		$this->api = $api;

		// Set levels
		$this->levels = array(
			0 => '',
			1 => $this->api->lang->get('offer_easy'),
			2 => $this->api->lang->get('offer_average'),
			3 => $this->api->lang->get('offer_difficult')
		);

		// Set target groups
		$q_target_group = $this->api->db->get('target_group', array('language' => $this->api->lang->lang_id), array('target_group_i18n' => 'target_group.target_group_id = target_group_i18n.target_group_id'), NULL, NULL, NULL, NULL, 'target_group.sort');
		if (mysqli_num_rows($q_target_group) > 0) {
			while ($row = mysqli_fetch_object($q_target_group)) {
				$this->target_groups[$row->target_group_id] = $row->body;
			}
		}

		// Set categories
		$q_category = $this->api->db->get('category', array('language' => $this->api->lang->lang_id), array('category_i18n' => 'category.category_id = category_i18n.category_id'));
		if (mysqli_num_rows($q_category) > 0) {
			while ($row = mysqli_fetch_object($q_category)) {
				$this->categories[$row->category_id] = $row;
			}
		}

	}



	/**
	 * Checks if an offer with the given id exists
	 *
	 * @access public
	 * @param  int
	 * @return mixed
	 */
	public function offer_exists($offer_id) {
		$q_offer = $this->api->db->query("SELECT `offer_id` FROM `offer` WHERE `offer_id` = ".$offer_id);
		if (mysqli_num_rows($q_offer) > 0) {
			return TRUE;
		}

		return FALSE;
	}



	/**
	 * Get Custom Layers
	 * 
	 * @access public
	 * @return void
	 */
	public function get_custom_layers() {

		$query = 'SELECT * FROM map_layer WHERE languages LIKE "%'. $this->api->lang->lang_id.'%";';
		$q_layers = $this->api->db->query($query);

		if (mysqli_num_rows($q_layers) > 0) {
			$layers = array();

			while ($row = mysqli_fetch_object($q_layers)) {

				// Retrieve content
				$query_i18n = 'SELECT * FROM map_layer_i18n WHERE map_layer_id = '. $row->map_layer_id;
				$q_layers_i18n = $this->api->db->query($query_i18n);
				if (mysqli_num_rows($q_layers_i18n) > 0) {
					$layer_content = array();

					// Parse content for each langauge
					while ($row_i18n = mysqli_fetch_object($q_layers_i18n)) {
						if (isset($row_i18n->language) && isset($row_i18n->popup_content)) {
							$layer_content[$row_i18n->language] = $row_i18n->popup_content;
						}
					}

					// Add parsed content to row
					$row->popup_content = $layer_content;
 				}

				$layers[$row->map_layer_id] = $row;
			}
			return $layers;
		}
		return FALSE;
	}



	/**
	 * Get offers
	 *
	 * @access public
	 * @param  mixed          An array containing filter values
	 * @return mixed|boolean  An array of offers or FALSE when no offers found
	 */
	public function filter_offers($filter, $limit = NULL, $offset = NULL, $return_minimal = FALSE, $only_count_categories = FALSE, $map_mode = FALSE, $return_only_categories = FALSE, $ignore_hint_order = FALSE, $order_by_rand = FALSE) {
		
		// Select offers
		$select = "
			SELECT
				SQL_CALC_FOUND_ROWS *,
				main_offer.`offer_id`,
				`offer_i18n`.`language`,

				IF ( ".CATEGORY_EVENT." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `event`.`public_transport_stop`,
					IF ( ".CATEGORY_PRODUCT." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `product`.`public_transport_stop`,
						IF ( ".CATEGORY_BOOKING." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `booking`.`public_transport_stop`,
							`activity`.`public_transport_stop` )
						)
					)
				AS public_transport_stop,

				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`has_playground`, `product`.`has_playground` ) AS has_playground,
				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`has_picnic_place`, `product`.`has_picnic_place` ) AS has_picnic_place,
				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`has_fireplace`, `product`.`has_fireplace` ) AS has_fireplace,
				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`has_washrooms`, `product`.`has_washrooms` ) AS has_washrooms,
				
				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`season_months`, 
					IF ( ".CATEGORY_PRODUCT." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `product`.`season_months`,
						`booking`.`season_months`)) AS season_months,

				IF ((c1.`category_id` IN (1000)) OR (c2.`category_id` IN (1000)) OR (c3.`category_id` IN (1000)), 1, 0) AS is_project,
				IF ((`offer_date`.`date_from` IS NOT NULL) AND ((c1.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH.")) OR (c2.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH.")) OR (c3.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH."))), `offer_date`.`date_from`, offer_i18n.`title`) AS special_order_by,
				IF ((`offer_date`.`date_from` IS NOT NULL) AND ((c1.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH.")) OR (c2.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH.")) OR (c3.`category_id` IN (".CATEGORY_EVENT.",".CATEGORY_RESEARCH."))), `offer_date`.`offer_date_id`, main_offer.`offer_id`) AS special_group_by,

				IF ( ".CATEGORY_ACTIVITY." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`), `activity`.`poi`, `project`.`poi` ) AS poi
			FROM `offer` main_offer ";

		// Alternative mode: count categories
		if ($only_count_categories == TRUE) {
			$select = "
				SELECT
					COUNT(event.offer_id) as event_count,
					COUNT(booking.offer_id) as booking_count,
					COUNT(activity.offer_id) as activity_count,
					COUNT(product.offer_id) as product_count,
					COUNT(project.offer_id) as project_count
				FROM `offer` main_offer
			";
		}

		// Alternative mode: get only categories
		else if ($return_only_categories == TRUE) {
			$select = "
				SELECT
					`category_link`.`category_id` as c0,
					c1.`category_id` as c1,
					c2.`category_id` as c2,
					c3.`category_id` as c3
				FROM `offer` main_offer
			";
		}

		// Join i18n data
		$join = "INNER JOIN `offer_i18n` ON main_offer.`offer_id` = `offer_i18n`.`offer_id` ";

		// Join main categories
		$join .= " LEFT OUTER JOIN `event` ON `event`.`offer_id` = main_offer.`offer_id` ";
		$join .= " LEFT OUTER JOIN `booking` ON `booking`.`offer_id` = main_offer.`offer_id` ";
		$join .= " LEFT OUTER JOIN `activity` ON `activity`.`offer_id` = main_offer.`offer_id` ";
		$join .= " LEFT OUTER JOIN `product` ON `product`.`offer_id` = main_offer.`offer_id` ";
		$join .= " LEFT OUTER JOIN `project` ON `project`.`offer_id` = main_offer.`offer_id` ";

		// Join subscription settings
		$join .= " LEFT OUTER JOIN `subscription` ON `subscription`.`offer_id` = main_offer.`offer_id` ";
		$join .= " LEFT OUTER JOIN `subscription_i18n` ON `subscription_i18n`.`offer_id` = main_offer.`offer_id` AND `subscription_i18n`.`language` = '".$this->api->lang->lang_id."' ";

		// Join offer dates
		$join .= " LEFT OUTER JOIN `offer_date` ON main_offer.`offer_id` = `offer_date`.`offer_id` ";

		// Join categories
		$join .= " INNER JOIN `category_link` ON `category_link`.`offer_id` = main_offer.`offer_id` ";
		$join .= " INNER JOIN `category` AS c1 ON `category_link`.`category_id` = `c1`.`category_id` ";
		$join .= " LEFT JOIN `category` AS c2 ON `c1`.`parent_id` = `c2`.`category_id` ";
		$join .= " LEFT JOIN `category` AS c3 ON `c2`.`parent_id` = `c3`.`category_id` ";

		// Filter: categories
		$where = array();
		if (isset($filter['categories']) && !empty($filter['categories'])) {
			if (!is_array($filter['categories'])) {
				$filter['categories'] = array($filter['categories']);
			}
			if (!empty($filter['categories'])) {
				$where_categories = "";
				foreach ($filter['categories'] as $category) {
					if ($category != '') {
						$where_categories .= $category." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`) OR ";
					}
				}
				$where_categories = substr($where_categories, 0, -4);
				if ($where_categories != '') {
					$where[] = "(".$where_categories.")";
				}
			}
		}

		// Filter: is hint
		if (isset($filter['is_hint'])) {
			if ($filter['is_hint'] == 1) {
				$where[] .= " main_offer.`is_hint` = 1";
			}
			else {
				$where[] .= " 	(
									main_offer.`is_hint` = 0
									OR main_offer.`is_hint` IS NULL
								)";
			}
		}

		// Filter: contact_is_park_partner
		if (isset($filter['contact_is_park_partner'])) {
			if ($filter['contact_is_park_partner'] == 1) {
				$where[] .= " main_offer.`contact_is_park_partner` = 1";
			}
			else {
				$where[] .= " 	(
									main_offer.`contact_is_park_partner` = 0
									OR main_offer.`contact_is_park_partner` IS NULL
								)";
			}
		}

		// Filter: offers_is_park_event
		if (isset($filter['offers_is_park_event'])) {
			if ($filter['offers_is_park_event'] == TRUE) {
				$where[] .= " event.`is_park_event` = 1";
			}
			else {
				$where[] .= "	(
									event.`is_park_event` = 0
									OR event.`is_park_event` IS NULL
								)";
			}
		}

		// Select language and show every entry, no matter which language is available
		if (isset($this->api->config['language_independence']) && ($this->api->config['language_independence'] == TRUE) && isset($this->api->config['language_priority'])) {

			// Get language priorities
			$language_priority = $this->api->config['language_priority'][ $this->api->lang->lang_id ];
			if (!empty($language_priority) && is_array($language_priority)) {

				$where[] = "`offer_i18n`.`language` = (

					SELECT IF (sub_offer_i18n.`language` IS NOT NULL, sub_offer_i18n.`language`, (
							SELECT IF (sub_offer_i18n.`language` IS NOT NULL, sub_offer_i18n.`language`, (
									SELECT
										IF (sub_offer_i18n.`language` IS NOT NULL, sub_offer_i18n.`language`, ((
											SELECT IF (sub_offer_i18n.`language` IS NOT NULL, sub_offer_i18n.`language`, 'de')
											FROM `offer` sub_offer
											LEFT JOIN `offer_i18n` sub_offer_i18n ON sub_offer_i18n.`offer_id` = sub_offer.`offer_id` AND sub_offer_i18n.`language` = '".$language_priority[2]."'
											WHERE main_offer.`offer_id` = sub_offer.`offer_id`)
										))
									FROM `offer` sub_offer
									LEFT JOIN `offer_i18n` sub_offer_i18n ON sub_offer_i18n.`offer_id` = sub_offer.`offer_id` AND sub_offer_i18n.`language` = '".$language_priority[1]."'
									WHERE main_offer.`offer_id` = sub_offer.`offer_id`
								))
							FROM `offer` sub_offer
							LEFT JOIN `offer_i18n` sub_offer_i18n ON sub_offer_i18n.`offer_id` = sub_offer.`offer_id` AND sub_offer_i18n.`language` = '".$language_priority[0]."'
							WHERE main_offer.`offer_id` = sub_offer.`offer_id`
						))
					FROM `offer` sub_offer
					LEFT JOIN `offer_i18n` sub_offer_i18n ON sub_offer_i18n.`offer_id` = sub_offer.`offer_id` AND sub_offer_i18n.`language` = '".$this->api->lang->lang_id."'
					WHERE main_offer.`offer_id` = sub_offer.`offer_id`

				)";

			}
		}

		// Select only offers which are available in the current language
		else {
			$where[] = "`offer_i18n`.`language` = '".$this->api->lang->lang_id."'";
		}

		// Filter: offer settings
		if (isset($filter['offer_settings']) && !empty($filter['offer_settings'])) {
			foreach ($filter['offer_settings'] as $key => $value) {
				$where[] = "main_offer.`".$key."` = '".$value."'";
			}
		}

		// Filter: target groups
		$join .= " LEFT JOIN `target_group_link` ON `target_group_link`.`offer_id` = main_offer.`offer_id` ";
		if (!empty($filter['target_groups'])) {
			$where[] = "(
				(`target_group_link`.`target_group_id` IN (".implode(',', $filter['target_groups'])."))
				OR
				(`target_group_link`.`target_group_id` IS NULL)
			)";
		}
		if (!empty($this->api->system_filter['target_groups'])) {
			$where[] = "(
				(`target_group_link`.`target_group_id` IN (".implode(',', $this->api->system_filter['target_groups'])."))
				OR
				(`target_group_link`.`target_group_id` IS NULL)
			)";
		}

		// Filter: park
		if (isset($filter['park_id']) && !empty($filter['park_id'])) {
			if (!is_array($filter['park_id'])) {
				$where[] = "main_offer.`park_id` = ".intval($filter['park_id']);
			}
			else {
				$where_in_park_ids = implode(",", $filter['park_id']);
				$where[] = "main_offer.`park_id` IN (".$where_in_park_ids.")";
			}
		}
		if (isset($filter['user']) && !empty($filter['user'])) {
			$where[] = "main_offer.`park` = '".$filter['park_id']."'";
		}

		// Filter: exlcude parks
		if (!empty($filter['exclude_park_ids']) && is_array($filter['exclude_park_ids'])) {
			$where[] = "main_offer.`park` NOT IN (".implode(",", $filter['exclude_park_ids']).")";
		}

		// Filter: offers of today
		if (isset($filter['offers_of_today']) && ($filter['offers_of_today'] == TRUE)) {
			$now = new DateTime();
			$filter['date_from'] = $now->format('Y-m-d');
			$filter['date_to'] = $now->format('Y-m-d');
		}

		// Filter: date
		if ((isset($filter['date_from']) && !empty($filter['date_from'])) || (isset($filter['date_to']) && !empty($filter['date_to']))) {
			$date_from = isset($filter['date_from']) ? $this->_get_datetime($filter['date_from']) : FALSE;
			$date_to = isset($filter['date_to']) ? $this->_get_datetime($filter['date_to']) : FALSE;

			// Only filter dates on events
			$where[] = " ( ".CATEGORY_EVENT." IN (c1.`category_id`, c1.`parent_id`, c2.`category_id`, c2.`parent_id`, c3.`category_id`, c3.`parent_id`) ) ";

			// Check time span
			if (!empty($date_from) && !empty($date_to)) {

				// First, check if this offer date is between the filter dates
				// Second, check if this date is in the filter time span
				$where[] = "
					(
						(
							'".$date_from."' BETWEEN `date_from` AND `date_to`
							OR
							'".$date_to."' BETWEEN `date_from` AND `date_to`
						)

						OR

						(
							`date_from` >= DATE('".$date_from."') AND `date_from` < DATE_ADD('".$date_to."', INTERVAL 1 DAY)
							OR
							`date_to` >= DATE('".$date_from."') AND `date_to` < DATE_ADD('".$date_to."', INTERVAL 1 DAY)
						)
					)
				";

			}

			// Check only date from
			else if (!empty($date_from)) {
				$where[] = " ( `offer_date`.`date_from` >= '".$date_from."' OR `offer_date`.`date_to` >= '".$date_from."' ) ";
			}

			// Check only date to
			else if (!empty($date_to)) {
				$where[] = "
					(
						# its a timespan:
						# check only date_to (add 1 day)
						(
							`offer_date`.`date_to` IS NOT NULL
							AND
							`offer_date`.`date_to` != '0000-00-00 00:00:00'
							AND
							`offer_date`.`date_to` < '".$date_to." 23:59:59'
							AND
							`offer_date`.`date_to` >= NOW()
						)

						OR

						# or, its only a date_from
						# check date from (add 1 day)
						(
							(
								`offer_date`.`date_to` IS NULL
								OR
								`offer_date`.`date_to` = '0000-00-00 00:00:00'
							)
							AND
							`offer_date`.`date_from` < '".$date_to." 00:00:00'
							AND
							`offer_date`.`date_from` >= NOW()
						)
					)
				";
			}

		}
		else {
			// Show only coming events
			$where[] = "
				(
						`offer_date`.`date_from` IS NULL
					OR
						`offer_date`.`date_from` >= NOW()
					OR
						`offer_date`.`date_to` >= NOW()
					OR
						(".CATEGORY_EVENT." NOT IN (`c1`.`category_id`, `c1`.`parent_id`, `c2`.`category_id`, `c2`.`parent_id`, `c3`.`category_id`, `c3`.`parent_id`))
					OR
						(".CATEGORY_RESEARCH." NOT IN (`c1`.`category_id`, `c1`.`parent_id`, `c2`.`category_id`, `c2`.`parent_id`, `c3`.`category_id`, `c3`.`parent_id`))

				)";
		}

		// Filter: search words
		if (isset($filter['search']) && ($filter['search'] != '')) {
			$search_words = explode(' ', $filter['search']);
			if (!empty($search_words)) {
				$search_query = "(";
				foreach ($search_words as $word) {
					$search_query .= "(
								(main_offer.`offer_id` = '".$word."')
								OR main_offer.keywords LIKE '%".$word."%'
								OR (offer_i18n.title LIKE '%".$word."%')
								OR (offer_i18n.abstract LIKE '%".$word."%')
								OR (offer_i18n.description_medium LIKE '%".$word."%')
								OR (offer_i18n.description_long LIKE '%".$word."%')
								OR (offer_i18n.details LIKE '%".$word."%')
								OR (offer_i18n.price LIKE '%".$word."%')
								OR (offer_i18n.location_details LIKE '%".$word."%')
								OR (offer_i18n.opening_hours LIKE '%".$word."%')
								OR (offer_i18n.benefits LIKE '%".$word."%')
								OR (offer_i18n.requirements LIKE '%".$word."%')
								OR (offer_i18n.additional_informations LIKE '%".$word."%')
								OR (offer_i18n.catering_informations LIKE '%".$word."%')
								OR (offer_i18n.material_rent LIKE '%".$word."%')
								OR (offer_i18n.safety_instructions LIKE '%".$word."%')
								OR (offer_i18n.signalization LIKE '%".$word."%')
								OR (offer_i18n.other_infrastructure LIKE '%".$word."%')
							) AND ";
				}
				$search_query = substr($search_query, 0, -4).")";
				$where[] = $search_query;
			}
		}

		// Filter: keywords
		if (isset($filter['keywords']) && ($filter['keywords'] != '')) {
			$keywords = explode(' ', $filter['keywords']);
			if (!empty($keywords)) {
				$restriction = 'OR';
				if ($this->api->config['filter_keywords_with_and'] == TRUE) {
					$restriction = 'AND';
				}
				$keywords_query = "(";
				foreach ($keywords as $word) {
					$keywords_query .= " main_offer.keywords LIKE '%".$word."%' ".$restriction." ";
				}
				$keywords_query = substr($keywords_query, 0, -4).")";
				$where[] = $keywords_query;
			}
		}

		// Filter: route length
		if (isset($filter['route_length_min']) && !empty($filter['route_length_min']) && intval($filter['route_length_min']) != 0) {
			$where[] = " (`activity`.`route_length` >= ".intval($filter['route_length_min']).")";
		}

		// Filter: route max length
		if (isset($filter['route_length_max']) && !empty($filter['route_length_max']) && intval($filter['route_length_max']) != 50) {
			$where[] = " (`activity`.`route_length` <= ".intval($filter['route_length_max']).")";
		}

		// Filter: time required
		if (isset($filter['time_required']) && !empty($filter['time_required'])) {
			if (is_array($filter['time_required'])) {

				// Time minutes
				$where_time_required = array();
				$time_category_minutes = array(
					'< 2h' => '(`activity`.`time_required_minutes` <= 120)',
					'2 - 4h' => '((`activity`.`time_required_minutes` > 120) AND (`activity`.`time_required_minutes` <= 240))',
					'4 - 6h' => '((`activity`.`time_required_minutes` > 240) AND (`activity`.`time_required_minutes` <= 360))',
					'> 6h' => '(`activity`.`time_required_minutes` > 360)',
				);

				// Prepare filter data
				foreach ($filter['time_required'] as $key => $value) {

					// Decode html
					$filter['time_required'][$key] = html_entity_decode($value);

					// Set time condition
					if (!empty($time_category_minutes[$value])) {
						$where_time_required[] = "
							(
								(
									`activity`.`time_required_minutes` IS NULL
									AND
									`activity`.`time_required` = '".$value."'
								)
								OR
								(
									`activity`.`time_required_minutes` IS NOT NULL
									AND
									`activity`.`time_required_minutes` <> 0
									AND
									".$time_category_minutes[$value]."
								)
							)
						";
					}

				}

				if (!empty($where_time_required)) {
					$where[] = '('.implode(' OR ', $where_time_required).')';
				}
			}

		}

		// Filter: level_technics
		if (isset($filter['level_technics']) && !empty($filter['level_technics'])) {
			if (is_array($filter['level_technics'])) {
				$where_in_level_technics = implode(",", $filter['level_technics']);
				$where[] = " (`activity`.`level_technics` IN (".$where_in_level_technics."))";
			}
			else {
				$where[] = " (`activity`.`level_technics` = ".$filter['level_technics'].")";
			}
		}

		// Filter: time_required
		if (isset($filter['level_condition']) && !empty($filter['level_condition'])) {
			if (is_array($filter['level_condition'])) {
				$where_in_level_condition = implode(",", $filter['level_condition']);
				$where[] = " (`activity`.`level_condition` IN (".$where_in_level_condition."))";
			}
			else {
				$where[] = " (`activity`.`level_condition` = ".$filter['level_condition']."L)";
			}
		}

		// Filter: project status
		if (isset($filter['project_status']) && !empty($filter['project_status'])) {
			if (is_array($filter['project_status'])) {
				$where_in_level_condition = implode(",", $filter['project_status']);
				$where[] = " (`project`.`status` IN (".$where_in_level_condition."))";
			}
			else {
				$where[] = " (`project`.`status` = ".$filter['project_status']."L)";
			}
		}

		// Filter: additional infos
		$where_additional = "";
		if (isset($filter['online_shop_enabled']) && $filter['online_shop_enabled'] == 1) {
			$where_additional .= "product.`online_shop_enabled` = 1 OR ";
		}
		if (isset($filter['barrier_free']) && $filter['barrier_free'] == 1) {
			$where_additional .= "main_offer.`barrier_free` = 1 OR ";
		}
		if (isset($filter['learning_opportunity']) && $filter['learning_opportunity'] == 1) {
			$where_additional .= "main_offer.`learning_opportunity` = 1 OR ";
		}
		if (isset($filter['child_friendly']) && $filter['child_friendly'] == 1) {
			$where_additional .= "main_offer.`child_friendly` = 1 OR ";
		}
		if (strlen($where_additional) > 0) {
			$where[] = " (".substr($where_additional, 0, -4).")";
		}
		// Filter: offers
		if (isset($filter['offers']) && !empty($filter['offers'])) {
			if (!is_array($filter['offers'])) {
				$filter['offers'] = array($filter['offers']);
			}
			if (!empty($filter['offers'])) {
				$where_offer = "";
				foreach ($filter['offers'] as $offer) {
					$where_offer .= " main_offer.`offer_id` = ".$offer." OR ";
				}
				$where_offer = substr($where_offer, 0, -4);
				$where[] = "(".$where_offer.")";
			}
		}

		// Init where
		if (!empty($where)) {
			$where = " WHERE ".implode(" AND ", $where);
		}
		else {
			$where = "";
		}

		// Group and order by
		$group_by = $order_by = "";
		if (($only_count_categories == FALSE) && ($return_only_categories == FALSE)) {
			$group_by = " GROUP BY special_group_by ";
			if ($order_by_rand === TRUE) {
				$order_by = " ORDER BY RAND() ";
			}
			elseif ($ignore_hint_order === TRUE) {
				$order_by = "
					ORDER BY
						CASE is_project WHEN '1' THEN project.duration_from END DESC,
						special_order_by,	
						`offer_i18n`.title ASC
				";
			}
			else {
				$order_by = "
					ORDER BY
						is_hint DESC, 
						CASE is_project WHEN '1' THEN project.duration_from END DESC,
						special_order_by,	
						`offer_i18n`.title ASC
				";
			}
		}

		// Limit query
		$limit_sql = "";
		if (!is_null($limit) && is_numeric($limit) && ($limit > 0)) {
			$limit_sql = " LIMIT ".(isset($offset) && is_numeric($offset) ? intval($offset).', ' : '').intval($limit);
		}

		// Run query
		$q_offers = $this->api->db->query($select.$join.$where.$group_by.$order_by.$limit_sql);

		// Return only count of offers
		if ($only_count_categories == TRUE) {
			return mysqli_fetch_object($q_offers);
		}

		// Return only linked categories
		elseif ($return_only_categories == TRUE) {
			return $q_offers;
		}

		// Return offer data
		else if (mysqli_num_rows($q_offers) > 0) {

			// Get total
			$total_query = $this->api->db->query("SELECT FOUND_ROWS()");
			$result_array = mysqli_fetch_array($total_query);

			// Get offers
			$offers = array(
				'data' => array(),
				'total' => array_shift($result_array)
			);

			while ($offer = mysqli_fetch_object($q_offers)) {
				if (!empty($offer) && ($offer->offer_id > 0)) {
					$offers['data'][] = $this->get_offer($offer, $return_minimal, $map_mode);
				}
			}

			return $offers;
		}

		return FALSE;
	}



	/**
	 * Get offer with additional infos
	 *
	 * @access public
	 * @param  int
	 * @return mixed
	 */
	public function get_offer($offer, $return_minimal = FALSE, $map_mode = FALSE) {

		// Get offer if needed
		if (is_numeric($offer) && !is_object($offer)) {

			// Filter by offer id
			$filter = array('offers' => $offer);

			// Get offer main data
			$offer = $this->filter_offers($filter, 1, 0);
			if (isset($offer['data']) && is_array($offer['data'])) {
				$offer = array_shift($offer['data']);
			}
			else {
				$offer = array();
			}

		}

		// Get additional offer informations
		if (is_object($offer) && !empty($offer) && ($offer->offer_id > 0)) {

			// Get offer category links
			$offer->root_category = NULL;
			$q_categories = $this->api->db->get('category_link', array('offer_id' => $offer->offer_id));
			if (mysqli_num_rows($q_categories) > 0) {
				$offer->categories = array();
				while ($row = mysqli_fetch_object($q_categories)) {
					$offer->categories[$row->category_id] = $this->get_category($row->category_id);
					if (!$offer->root_category) {
						$offer->root_category = $this->_get_root_category($row->category_id);
					}
				}
			}

			// Get offer target groups
			if ($map_mode == FALSE) {
				$q_target_groups = $this->api->db->get('target_group_link', array('offer_id' => $offer->offer_id));
				if (mysqli_num_rows($q_target_groups) > 0) {

					// Get target group links
					$target_group_links = array();
					while ($row = mysqli_fetch_object($q_target_groups)) {
						if (!empty($row->target_group_id) && array_key_exists($row->target_group_id, $this->target_groups)) {
							$target_group_links[] = $row->target_group_id;
						}
					}

					// Sort target groups
					$offer->target_groups = array();
					if (!empty($this->target_groups) && !empty($target_group_links)) {
						foreach ($this->target_groups as $target_group_id => $target_group) {
							if (in_array($target_group_id, $target_group_links)) {
								$offer->target_groups[$target_group_id] = $target_group;
							}
						}
					}

				}
			}

			// Get dates
			$offer->dates = array();
			if (($map_mode == FALSE) || (($map_mode == TRUE) && in_array($offer->root_category, array(CATEGORY_EVENT, CATEGORY_RESEARCH)))) {
				$q_dates = $this->api->db->get(
					'offer_date',
					array('offer_id' => $offer->offer_id), 
					NULL, 
					array("DATE_FORMAT(date_from, '".$this->api->config['mysql_date_format']."')" => 'date_from', "DATE_FORMAT(date_to, '".$this->api->config['mysql_date_format']."')" => 'date_to'),
					NULL,
					NULL,
					NULL,
					"DATE_FORMAT(date_from, '%Y-%m-%d %H:%i')"
				);
				if (mysqli_num_rows($q_dates) > 0) {
					while ($row = mysqli_fetch_object($q_dates)) {
						$offer->dates[] = $row;
					}
				}
			}

			// Get images
			$q_images = $this->api->db->get('image', array('offer_id' => $offer->offer_id));
			if (mysqli_num_rows($q_images) > 0) {
				$offer->images = array();
				while ($row = mysqli_fetch_object($q_images)) {
					$offer->images[] = $row;
				}
			}

			// Get extended data
			if ($return_minimal == FALSE) {

				// Get offer routes
				if ($offer->root_category == CATEGORY_ACTIVITY) {
					$n_th_point = !empty($this->api->config['n_th_point']) ? $this->api->config['n_th_point'] : 1;
					$n_th_point_min_count = !empty($this->api->config['n_th_point_min_count']) ? $this->api->config['n_th_point_min_count'] : 1;
					if ($this->api->is_offer_detail()) {
						$n_th_point = 1;
					}
					$q_route = $this->api->db->query("
						SELECT *
						FROM `offer_route`
						WHERE (
							`offer_id` = ".$offer->offer_id."
							AND (
								(SELECT max(sort) FROM `offer_route` WHERE `offer_id` = ".$offer->offer_id.") < ".$n_th_point_min_count."
								OR `sort` mod ".$n_th_point." = 0
								OR `sort` = 1
								OR `sort` = (SELECT max(sort) FROM `offer_route` WHERE `offer_id` = ".$offer->offer_id.")
							)
						)
						ORDER BY offer_id ASC, sort ASC;
					");
					if (mysqli_num_rows($q_route) > 0) {
						$offer->route = array();
						while ($row = mysqli_fetch_object($q_route)) {
							$offer->route[$row->sort] = $row;
						}
					}
				}

				// Documents
				if ($map_mode == FALSE) {
					$q_documents = $this->api->db->get('document', array('offer_id' => $offer->offer_id, 'language' => $this->api->lang->lang_id));
					if (mysqli_num_rows($q_documents) > 0) {
						$offer->documents = array();
						while ($row = mysqli_fetch_object($q_documents)) {
							$offer->documents[] = $row;
						}
					}
				}

				// Documents intern
				if ($map_mode == FALSE) {
					$q_documents = $this->api->db->get('document_intern', array('offer_id' => $offer->offer_id, 'language' => $this->api->lang->lang_id));
					if (mysqli_num_rows($q_documents) > 0) {
						$offer->documents_intern = array();
						while ($row = mysqli_fetch_object($q_documents)) {
							$offer->documents_intern[] = $row;
						}
					}
				}

				// Hyperlinks
				if ($map_mode == FALSE) {
					$q_hyperlinks = $this->api->db->get('hyperlink', array('offer_id' => $offer->offer_id, 'language' => $this->api->lang->lang_id));
					if (mysqli_num_rows($q_hyperlinks) > 0) {
						$offer->hyperlinks = array();
						while ($row = mysqli_fetch_object($q_hyperlinks)) {
							$offer->hyperlinks[] = $row;
						}
					}
				}

				// Hyperlinks intern
				if ($map_mode == FALSE) {
					$q_hyperlinks = $this->api->db->get('hyperlink_intern', array('offer_id' => $offer->offer_id, 'language' => $this->api->lang->lang_id));
					if (mysqli_num_rows($q_hyperlinks) > 0) {
						$offer->hyperlinks_intern = array();
						while ($row = mysqli_fetch_object($q_hyperlinks)) {
							$offer->hyperlinks_intern[] = $row;
						}
					}
				}

				// Accessibilities
				if ($map_mode == FALSE) {
					$q_accessibilities = $this->api->db->query("
						SELECT *
						FROM `accessibility`
						INNER JOIN `accessibility_pictogram` ON `accessibility_pictogram`.`accessibility_pictogram_id` = `accessibility`.`accessibility_pictogram_id`
						WHERE `accessibility`.`offer_id` = ".$offer->offer_id."
					");
					if (mysqli_num_rows($q_accessibilities) > 0) {
						$offer->accessibilities = array();
						while ($row = mysqli_fetch_object($q_accessibilities)) {
							$offer->accessibilities[] = $row;
						}
					}
				}

			}

			// Product
			if (($offer->root_category == CATEGORY_PRODUCT) && ($map_mode == FALSE)) {

				// Suppliers
				$q_suppliers = $this->api->db->get('supplier', array('offer_id' => $offer->offer_id));
				if (mysqli_num_rows($q_suppliers) > 0) {
					$offer->suppliers = array();
					while ($row = mysqli_fetch_object($q_suppliers)) {
						$offer->suppliers[] = array('contact' => $row->contact, 'is_park_partner' => $row->is_park_partner);
					}
				}

				// Online shop: product articles
				if (($offer->online_shop_enabled == TRUE) && ($return_minimal == FALSE)) {

					// Get language priorities
					$language_priority = $this->api->config['language_priority'][ $this->api->lang->lang_id ];
					if (!empty($language_priority) && is_array($language_priority)) {

						// Get articles including i18n and respecting language priorities
						$q_articles = $this->api->db->query("

							SELECT *
							FROM `product_article` AS main_article
							INNER JOIN `product_article_i18n` main_article_i18n ON main_article_i18n.`product_article_id` = main_article.`product_article_id`
							WHERE 
								main_article.`offer_id` = ".$offer->offer_id."
								AND main_article_i18n.`language` = (
													
									SELECT IF (sub_article_i18n.`language` IS NOT NULL, sub_article_i18n.`language`, (
										SELECT IF (sub_article_i18n.`language` IS NOT NULL, sub_article_i18n.`language`, (
											SELECT
												IF (sub_article_i18n.`language` IS NOT NULL, sub_article_i18n.`language`, ((
													SELECT IF (sub_article_i18n.`language` IS NOT NULL, sub_article_i18n.`language`, 'de')
													FROM `product_article` AS sub_article
													LEFT JOIN `product_article_i18n` sub_article_i18n ON sub_article_i18n.`product_article_id` = sub_article.`product_article_id` AND sub_article_i18n.`language` = '".$language_priority[2]."'
													WHERE main_article.`product_article_id` = sub_article.`product_article_id`)
												))
											FROM `product_article` AS sub_article
											LEFT JOIN `product_article_i18n` sub_article_i18n ON sub_article_i18n.`product_article_id` = sub_article.`product_article_id` AND sub_article_i18n.`language` = '".$language_priority[1]."'
											WHERE main_article.`product_article_id` = sub_article.`product_article_id`
										))
										FROM `product_article` AS sub_article
										LEFT JOIN `product_article_i18n` sub_article_i18n ON sub_article_i18n.`product_article_id` = sub_article.`product_article_id` AND sub_article_i18n.`language` = '".$language_priority[0]."'
										WHERE main_article.`product_article_id` = sub_article.`product_article_id`
									))
									FROM `product_article` AS sub_article
									LEFT JOIN `product_article_i18n` sub_article_i18n ON sub_article_i18n.`product_article_id` = sub_article.`product_article_id` AND sub_article_i18n.`language` = '".$this->api->lang->lang_id."'
									WHERE main_article.`product_article_id` = sub_article.`product_article_id`
									
								)

						");
						
						// Iterate each article
						if (mysqli_num_rows($q_articles) > 0) {
							$offer->articles = array();
							while ($article = mysqli_fetch_object($q_articles)) {

								// Set article labels
								$q_article_labels = $this->api->db->query("
									SELECT *
									FROM `product_article_label`
									WHERE 
										`product_article_id` = ".$article->product_article_id."
										AND `language` = '".$this->api->lang->lang_id."'
								");
								if (mysqli_num_rows($q_article_labels) > 0) {
									$article->labels = array();
									while ($article_label = mysqli_fetch_object($q_article_labels)) {
										$article->labels[] = $article_label;
									}
								}
								
								// Set article
								$offer->articles[] = $article;

							}
						}
					}
				}
			}

			// Booking
			else if (($offer->root_category == CATEGORY_BOOKING) && ($map_mode == FALSE)) {

				// Accommodations
				$q_accommodations = $this->api->db->get('accommodation', array('offer_id' => $offer->offer_id));
				if (mysqli_num_rows($q_accommodations) > 0) {
					$offer->accommodations = array();

					while ($row = mysqli_fetch_object($q_accommodations)) {
						$offer->accommodations[] = array('contact' => $row->contact, 'is_park_partner' => $row->is_park_partner);
					}
				}

			}

			// Project
			else if ($offer->root_category == CATEGORY_PROJECT) {

				// Project status
				$offer->project_status = $offer->status;

			}

			// Poi
			if (($return_minimal == FALSE) && ($map_mode == FALSE)) {
				if (isset($offer->poi) && !empty($offer->poi) && ($offer->poi != '') && !is_array($offer->poi)) {
					$poi = explode(',', $offer->poi);

					// Check if offer exists
					$existing_poi = array();
					if (is_array($poi) && !empty($poi)) {
						foreach ($poi as $offer_id) {
							$q_poi = $this->api->db->get('offer', array('offer_id' => $offer_id));
							if ($q_poi->num_rows == 1) {
								$existing_poi[] = $offer_id;
							}
						}
					}

					$offer->poi = $existing_poi;
				}
			}

			return $offer;
		}

		return FALSE;
	}



	/**
	 * Get category by ID
	 *
	 * @access public
	 * @param  int
	 * @return object  A category object or FALSE
	 */
	public function get_category($category_id) {
		if (array_key_exists($category_id, $this->categories)) {
			return $this->categories[$category_id];
		}
		return FALSE;
	}



	/**
	 * Get path for a category
	 *
	 * @access public
	 * @param  int
	 * @return object  A category object or FALSE
	 */
	public function get_category_path($category_id) {
		if (!empty($category_id)) {
			$path = array();

			while ($category_id > 0) {
				$category = $this->categories[$category_id];
				$category_id = $category->parent_id;
				$path[] = $category->category_id;
			}

			return $path;
		}

		return FALSE;
	}



	/**
	 * Get all categories
	 *
	 * @access public
	 * @return mixed|boolean  An array of categories or FALSE when nothing found
	 */
	public function get_all_categories($filter = array()) {
		$q_categories_string = "
			SELECT
				c1.`category_id` as category_id,
				c1.`parent_id`,
				`category_i18n`.`body`,
				c1.`marker`,
				CONCAT_WS(',', c1.`parent_id`, c2.`parent_id`, c3.`parent_id`, c4.`parent_id`) AS parents,
				c1.`sort`
			FROM `category` c1
			INNER JOIN `category_i18n` ON `category_i18n`.`category_id` = c1.`category_id` AND `category_i18n`.`language` = '".$this->api->lang->lang_id."'
			LEFT JOIN `category` c2 ON c1.`parent_id` = c2.`category_id`
			LEFT JOIN `category` c3 ON c2.`parent_id` = c3.`category_id`
			LEFT JOIN `category` c4 ON c3.`parent_id` = c4.`category_id`
		";

		// Additional information
		if (!empty($filter) && count($filter) > 0) {

			$q_categories_string .= "
				LEFT JOIN `category_link` ON `category_link`.`category_id` = c1.`category_id`
				LEFT JOIN `offer` ON `category_link`.`offer_id` = `offer`.`offer_id`
				LEFT JOIN `event` ON `event`.`offer_id` = `offer`.`offer_id`
				WHERE 1
			";

			$q_additional = $this->_prepare_additional_infos($filter);
			if (!empty($q_additional)) {
				$q_categories_string .= "AND (1 ".$q_additional.") OR (c1.`parent_id` = 0)";
			}

		}

		$q_categories_string .= "GROUP BY c1.`category_id` ";
		$q_categories_string .= "ORDER BY c1.`sort` ";

		$q_categories = $this->api->db->query($q_categories_string);

		if (mysqli_num_rows($q_categories) > 0) {
			$categories = array();

			while ($row = mysqli_fetch_object($q_categories)) {
				$categories[$row->category_id] = (object) array(
					'category_id' => $row->category_id,
					'parent_id' => $row->parent_id,
					'language' => $this->api->lang->lang_id,
					'body' => $row->body,
					'marker' => $row->marker,
					'parents' => explode(",", $row->parents),
					'sort' => $row->sort
				);
			}

			return $categories;
		}

		return FALSE;
	}



	/**
	 * Get all category links
	 *
	 * @access public
	 * @return mixed|boolean  An array of users or FALSE when nothing found
	 */
	public function get_all_category_links() {
		$q_category_links = $this->api->db->query("SELECT category_id FROM category_link GROUP BY category_link.category_id");

		if (mysqli_num_rows($q_category_links) > 0) {
			// Add main categories
			$category_links = array(CATEGORY_EVENT, CATEGORY_PRODUCT, CATEGORY_BOOKING, CATEGORY_ACTIVITY, CATEGORY_RESEARCH);

			while ($row = mysqli_fetch_object($q_category_links)) {
				$category_links[] = $row->category_id;
			}

			return $category_links;
		}

		return FALSE;
	}



	/**
	 * Get all users/parks
	 *
	 * @access public
	 * @return mixed|boolean  An array of users or FALSE when nothing found
	 */
	public function get_all_users($categories = array(), $filter = array()) {
		if (empty($categories)) {
			$categories = $this->get_all_category_links();
		}

		// Get all users
		$query = "SELECT park_id, park FROM offer";
		if (is_array($categories) && !empty($categories)) {
			$query .= "
				INNER JOIN category_link ON offer.offer_id = category_link.offer_id
				LEFT JOIN `event` ON `event`.`offer_id` = `offer`.`offer_id`
				WHERE category_link.category_id IN (".implode(",", $categories).")";
		}
		$query .= $this->_prepare_additional_infos($filter);
		$query .= " GROUP BY park_id ORDER BY park ASC";
		$q_users = $this->api->db->query($query);

		// No results: check single categories
		if (is_array($categories) && (count($categories) == 1) && mysqli_num_rows($q_users) == 0) {
			$query = "
				SELECT park_id, park
				FROM offer
				INNER JOIN category_link ON offer.offer_id = category_link.offer_id

				INNER JOIN `category` AS c1 ON category_link.`category_id` = c1.`category_id`
				LEFT JOIN `category` AS c2 ON c1.parent_id = c2.category_id
				LEFT JOIN `category` AS c3 ON c2.parent_id = c3.category_id

				WHERE ".$categories[0]." IN (`c1`.`category_id`, `c1`.`parent_id`, `c2`.`category_id`, `c2`.`parent_id`, `c3`.`category_id`, `c3`.`parent_id`)

				GROUP BY park_id
				ORDER BY park ASC
			;";
			$q_users = $this->api->db->query($query);
		}

		if (mysqli_num_rows($q_users) > 0) {
			$users = array();

			while ($row = mysqli_fetch_object($q_users)) {
				$users[$row->park_id] = $row->park;
			}

			return $users;
		}

		return FALSE;
	}



	/**
	 * Get full category tree
	 *
	 * @access public
	 * @return void
	 */
	public function get_category_tree() {
		$categories = array();
		$q_categories = $this->api->db->query("

			SELECT
				`category`.`category_id`,
				IF (`category`.`parent_id` = 0, `category_i18n`.`body`,
					IF (c2.`parent_id` = 0, CONCAT('--- ', `category_i18n`.`body`),
						IF (c3.`parent_id` = 0, CONCAT('------ ', `category_i18n`.`body`), CONCAT('--------- ', `category_i18n`.`body`))
					)
				) AS body,
				`category`.`sort`,
				`category`.`alpstein_id`,
				`category`.`marker`

				FROM `category`
				LEFT JOIN `category` AS c2 ON `category`.parent_id = c2.category_id
				LEFT JOIN `category` AS c3 ON c2.parent_id = c3.category_id
				INNER JOIN `category_i18n` ON `category_i18n`.`category_id` = `category`.`category_id`

				WHERE `category_i18n`.`language` = '".$this->api->lang->lang_id."'
				ORDER BY `category`.`sort`

		");

		if (mysqli_num_rows($q_categories) > 0) {
			while ($category = mysqli_fetch_object($q_categories)) {
				$categories[$category->category_id] = $category->body;
			}
		}

		return $categories;
	}




	/**
	 * Sync target groups
	 *
	 * @access public
	 * @param array $target_groups
	 * @return void
	 */
	public function sync_target_groups($target_groups) {
		if (!empty($target_groups) && is_array($target_groups)) {

			// Delete existing target groups
			$this->api->db->delete('target_group');
			$this->api->db->delete('target_group_i18n');

			// Insert target groups
			foreach ($target_groups as $lang => $items) {
				$sort = 0;
				foreach ($items as $target_group_id => $target_group) {
					$sort++;

					// Table target_group – only once
					if ($lang == 'de') {
						$this->api->db->insert('target_group', array(
							'target_group_id' => $target_group_id,
							'sort' => $sort,
						));
					}

					// Table target_group_i18n
					$this->api->db->insert('target_group_i18n', array(
						'target_group_id' => $target_group_id,
						'language' => $lang,
						'body' => $target_group,
					));

				}
			}

			return TRUE;
		}

		return FALSE;
	}




	/**
	 * Sync categories
	 *
	 * @access public
	 * @param array $categories
	 * @return void
	 */
	public function sync_categories($categories) {
		if (!empty($categories) && is_array($categories)) {

			// Delete existing target groups
			$this->api->db->delete('category');
			$this->api->db->delete('category_i18n');

			// Insert categories
			foreach ($categories as $lang => $items) {
				foreach ($items as $category_id => $category) {

					// Table category – only once
					if ($lang == 'de') {
						$this->api->db->insert('category', array(
							'category_id' => $category_id,
							'parent_id' => $category['parent_id'],
							'stnet_id' => $category['stnet_id'],
							'alpstein_id' => $category['alpstein_id'],
							'contact_visible_for_alpstein' => $category['contact_visible_for_alpstein'],
							'marker' => $category['marker'],
							'sort' => $category['sort'],
						));
					}

					// Table category_i18n
					$this->api->db->insert('category_i18n', array(
						'category_id' => $category_id,
						'language' => $lang,
						'body' => $category['body'],
					));

				}
			}

			return TRUE;
		}

		return FALSE;
	}



	/**
	 * Private method for retrieving the root category
	 *
	 * @param category_id
	 * @return integer|boolean
	 */
	private function _get_root_category($category_id) {
		if (!empty($category_id)) {

			while ($category_id > 0) {
				$category = $this->categories[$category_id];
				$category_id = $category->parent_id;
				$last_category_id = $category->category_id;
			}

			return (int)$last_category_id;
		}

		return FALSE;
	}



	/**
	 * Get date time
	 *
	 * @access private
	 * @param mixed $value
	 * @param string $format (default: 'Y-m-d H:i:s')
	 * @return void
	 */
	private function _get_datetime($value, $format = 'Y-m-d H:i:s') {
		$datetime = new DateTime($value);
		return $datetime->format($format);
	}



	/**
	 * Prepare additional infos for sql query
	 *
	 * @access private
	 * @param mixed $filter
	 * @return void
	 */
	private function _prepare_additional_infos($filter) {
		$additional_query = '';
		if (is_array($filter) && !empty($filter)) {
			foreach ($filter as $key => $value) {
				if (!is_array($value) && !empty($value)) {
					$key = str_replace('offers_', '', $key);
					if (!empty($key) && is_string($key)) {
						switch ($key) {

							case 'keywords':
								$additional_query .= " AND `offer`.`keywords` LIKE '%".$value."%'";
								break;

							case 'offers_filter_hints':
								$additional_query .= " AND `offer`.`is_hint` = '".$value."'";
								break;

							case 'contact_is_park_partner':
								$additional_query .= " AND `offer`.`contact_is_park_partner` = '".$value."'";
								break;

							case 'search':
							case 'order_by':
								break;

							case 'filter_hints':
								$additional_query .= " AND `offer`.`is_hint` = '".$value."'";
								break;

							case 'is_park_event':
								$additional_query .= " AND `event`.`is_park_event` = '".$value."'";
								break;

							default:
								if (!empty($key) && is_string($key) && (strlen($key) > 2)) {
									$additional_query .= " AND `offer`.".$key." = '".$value."'";
								}
								break;

						}
					}
				}
			}
		}

		return $additional_query;
	}


}