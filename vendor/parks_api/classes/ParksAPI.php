<?php
/*
|---------------------------------------------------------------
| parks.swiss API
| Netzwerk Schweizer Pärke
|---------------------------------------------------------------
|
| Main API class
|
*/


class ParksAPI {


	/**
	 * API info
	 */
	public $api;


	/**
	 * API hash
	 */
	public $hash;


	/**
	 * Park ID
	 */
	public $park_id;


	/**
	 * Configuration
	 */
	public $config;


	/**
	 * Language
	 */
	public $lang_id;
	public $lang;


	/**
	 * Model object
	 */
	public $model;


	/**
	 * Import object
	 */
	public $import;


	/**
	 * Database object
	 */
	public $db;


	/**
	 * Logger object
	 */
	public $logger;


	/**
	 * Template
	 */
	public $template = array();


	/**
	 * View object
	 */
	public $view;


	/**
	 * Single offer view mode
	 */
	public $single_mode = FALSE;


	/**
	 * Map options
	 */
	public $map_options;


	/**
	 * View mode
	 * TRUE: Returns the API output as string
	 * FALSE: Echoes the output directly
	 */
	public $return_output;


	/**
	 * Filter object and data
	 */
	public $filter = FALSE;
	public $filter_data = array();
	public $system_filter = array();
	public $filter_display_keywords = FALSE;


	/**
	 * Pagination
	 */
	public $page;
	public $total;


	/**
	 * Session
	 */
	public $session_name;


	/**
	 * Favorites
	 */
	public $favorites_cookie_name = '';
	public $favorites = array();



	/**
	 * Constructor
	 *
	 * @access public
	 * @param  string
	 * @param  string
	 * @return void
	 */
	function __construct($language = NULL, $hash = NULL) {

		// Get config
		$this->config = $this->_get_config();

		// Check config
		if (!empty($this->config)) {

			// Get park id
			$this->park_id = !empty($this->config['park_id']) ? $this->config['park_id'] : NULL;

			// Set absolute path
			$dirname = dirname(__FILE__);
			$this->config['absolute_path'] = substr($dirname, 0, strrpos($dirname, '/'));

			// Set data hash
			$this->hash = !empty($hash) ? $hash : $this->config['api_hash'];
			$this->return_output = isset($this->config['return_output']) ? $this->config['return_output'] : FALSE;

			// Instance of ParksLanguage
			$this->lang = new ParksLanguage($language, $this);
			$this->lang_id = $this->lang->lang_id;

			// Instance of ParksLog
			$this->logger = new ParksLog($this);

			// Instance of ParksMySQL
			$this->db = new ParksMySQL($this);

			// Instance of ParksModel
			$this->model = new ParksModel($this);

			// Instance of ParksImport
			$this->import = new ParksImport($this);

			// Load system config
			$this->_load_system_config();

			// Instance of ParksView
			if (class_exists($this->config['class_view'])) {
				$this->view = new $this->config['class_view']($this);
			}
			else {
				echo 'The custom view file does not exist.';
				exit();
			}

			// Init setup
			$this->_setup();

			// Init seo urls
			$page_slug = !empty($this->config['seo_url_page_slug']) ? $this->config['seo_url_page_slug'] : '';
			$reset_slug = !empty($this->config['seo_url_reset_slug']) ? $this->config['seo_url_reset_slug'] : '';

			// Init session
			if (!empty($this->config['use_sessions'])) {

				// Set session name for seo urls
				$session_url = $this->view->script_url;
				if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE)) {

					// Clean url from double slashes
					$session_url = str_replace('//', '/', $this->view->script_url);

					// Clean url from page param
					$session_url = preg_replace('/\/'.$page_slug.'\/(\d*)/m', '', $session_url);

					// Clean url from reset param
					$session_url = str_replace('/'.$reset_slug, '', $session_url);

					// Remove last slash
					$session_url = rtrim($session_url, '/');

				}

				// Set session
				$this->session_name = $this->config['session_name'].'_'.md5($session_url);

				// Init favorites
				$this->favorites_cookie_name = $this->config['session_name'].'_favorites';
				if (isset($_SESSION[$this->favorites_cookie_name]) && is_array($_SESSION[$this->favorites_cookie_name]) && !empty($_SESSION[$this->favorites_cookie_name])) {
					$this->favorites = $_SESSION[$this->favorites_cookie_name];
				}
				elseif (isset($_COOKIE[$this->favorites_cookie_name]) && ($_COOKIE[$this->favorites_cookie_name] != '')) {
					$this->favorites = unserialize($_COOKIE[$this->favorites_cookie_name]);
					$_SESSION[$this->favorites_cookie_name] = $this->favorites;
				}
			}

			// Init reset filter
			$reset_filter = FALSE;

			// Reset filter with seo urls
			if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE)) {
				if (strstr($this->view->script_url, '/'.$reset_slug)) {
					$reset_filter = TRUE;
				}
			}

			// Reset filter with default urls
			elseif (isset($_GET[$this->config['url_param_prefix'].'reset'])) {
				$reset_filter = TRUE;
			}

			// Reset or init filter
			if ($reset_filter === TRUE) {
				$this->_reset_filter();
			}
			else {
				$this->_init_filter();
			}

			// Load template
			$this->load_template();

		}
	}


	/**
	 * Updates database from XML export
	 *
	 * @access public
	 * @return void
	 */
	public function update($force = FALSE) {

		$xml = $this->config['xml_export_offer_url'].$this->hash;
		$xml_map_layer = $this->config['xml_export_map_layer_url'].$this->hash;
		$xml_active_offers = $this->config['xml_export_active_offers'].$this->hash;

		// Import data from XML into database
		$this->import->import($xml, $force);
		$this->import->import_map_layers($xml_map_layer);
		$this->import->clean_up_offers($xml_active_offers);

	}


	/**
	 * Migrate database updates
	 *
	 * @access public
	 * @return void
	 */
	public function migrate() {

		// Migrate database to newer versions
		$migration = new ParksMigration($this);
		$migration->start();

	}


	/**
	 * Load template
	 *
	 * @access public
	 * @return void
	 */
	public function load_template() {

		// Load detail template
		if (file_exists(__DIR__.'/../template/'.$this->config['template_folder'].'/detail.tpl')) {
			$this->template['detail'] = file_get_contents(__DIR__.'/../template/'.$this->config['template_folder'].'/detail.tpl');
			$this->template['detail'] = preg_replace("/<!--(.*?)-->/s", "", $this->template['detail']);
		}

		// Load filter template
		if (file_exists(__DIR__.'/../template/'.$this->config['template_folder'].'/filter.tpl')) {
			$this->template['filter'] = file_get_contents(__DIR__.'/../template/'.$this->config['template_folder'].'/filter.tpl');
			$this->template['filter'] = preg_replace("/<!--(.*?)-->/s", "", $this->template['filter']);
		}

	}


	/**
	 * Compile template
	 *
	 * @access public
	 * @param mixed $section
	 * @param array $template_data (default: array())
	 * @param array $conditions (default: array())
	 * @return mixed
	 */
	public function compile_template($section, $template_data = array(), $conditions = array()) {
		if (!empty($section)) {

			// Load section
			$return = $this->template[$section];

			// Compile template main category conditions
			foreach ($this->config['template_conditions'] as $condition_tag) {
				preg_match_all("/\[".$condition_tag."\@start\](.*?)\[".$condition_tag."\@stop\]/s", $return, $condition_found);
				if (!empty($condition_found[0])) {
					foreach ($condition_found[0] as $condition_found) {
						if (array_key_exists($condition_tag, $conditions) && !empty($conditions[$condition_tag])) {
							$remove_condition = $condition_found;
							$remove_condition = str_replace("[".$condition_tag."@start]", '', $remove_condition);
							$remove_condition = str_replace("[".$condition_tag."@stop]", '', $remove_condition);
							$return = str_replace($condition_found, $remove_condition, $return);
						}
						else {
							$return = str_replace($condition_found, '', $return);
						}
					}
				}
			}

			// Compile tags
			foreach ($this->config['template_tags'] as $tag) {

				// ISSET
				preg_match_all("/\[ISSET\(".$tag."\)\@start\](.*?)\[ISSET\(".$tag."\)@stop\]/s", $return, $condition_found);
				if (!empty($condition_found[0])) {
					foreach ($condition_found[0] as $condition_found) {
						if (array_key_exists($tag, $template_data) && !empty($template_data[$tag])) {
							$remove_condition = $condition_found;
							$remove_condition = str_replace("[ISSET(".$tag.")@start]", '', $remove_condition);
							$remove_condition = str_replace("[ISSET(".$tag.")@stop]", '', $remove_condition);
							$return = str_replace($condition_found, $remove_condition, $return);
						}
						else {
							$return = str_replace($condition_found, '', $return);
						}
					}
				}

				// NOTISSET
				preg_match_all("/\[NOTISSET\(".$tag."\)\@start\](.*?)\[NOTISSET\(".$tag."\)@stop\]/s", $return, $condition_found);
				if (!empty($condition_found[0])) {
					foreach ($condition_found[0] as $condition_found) {
						if ( ! array_key_exists($tag, $template_data) || empty($template_data[$tag])) {
							$remove_condition = $condition_found;
							$remove_condition = str_replace("[NOTISSET(".$tag.")@start]", '', $remove_condition);
							$remove_condition = str_replace("[NOTISSET(".$tag.")@stop]", '', $remove_condition);
							$return = str_replace($condition_found, $remove_condition, $return);
						}
						else {
							$return = str_replace($condition_found, '', $return);
						}
					}
				}

			}

			// Compile language labels
			preg_match_all("/__LANG\[(.+)\]__/i", $return, $lang_vars);
			if (!empty($lang_vars[1])) {
				foreach ($lang_vars[1] as $lang_label) {
					$lang_content = $this->lang->get($lang_label);
					if ($lang_content == $lang_label) {
						$lang_content = '';
					}
					$return = str_replace('__LANG['.$lang_label.']__', $lang_content, $return);
				}
			}

			// Compile template tags
			if (!empty($this->config['template_tags'])) {
				foreach ($this->config['template_tags'] as $tag) {
					$tag_content = '';
					if (array_key_exists($tag, $template_data) && !empty($template_data[$tag])) {
						$tag_content = $template_data[$tag];
					}
					$return = str_replace('__'.$tag.'__', $tag_content, $return);
				}
			}

			return $return;
		}

		return FALSE;
	}


	/**
	 * Displays filter for offers
	 *
	 * @access public
	 * @return void
	 */
	public function show_offers_filter($categories = array(), $filter = array(), $park_id = NULL) {

		// Init categories
		$param_categories = $categories;

		// Init main restrictions
		if (!empty($filter['system_filter']['target_groups'])) {
			$this->system_filter['target_groups'] = $filter['system_filter']['target_groups'];
			unset($filter['system_filter']['target_groups']);
		}

		// Init option filter display with keywords
		if (!empty($filter['show_keywords_filter']) && ($filter['show_keywords_filter'] === TRUE)) {
			$this->filter_display_keywords = TRUE;
		}

		// Set park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		if (!$this->is_offer_detail() || $this->config['always_show_filter'] == TRUE) {

			// Get all offers and count activities and projects
			$event_count = 0;
			$booking_count = 0;
			$activity_count = 0;
			$product_count = 0;
			$projects_count = 0;

			$offers_count = $this->_get_offers($park_id, $categories, NULL, NULL, $filter, TRUE, TRUE, TRUE);
			if (!empty($offers_count)) {
				$event_count = $offers_count->event_count;
				$booking_count = $offers_count->booking_count;
				$activity_count = $offers_count->activity_count;
				$product_count = $offers_count->product_count;
				$projects_count = $offers_count->project_count;
			}

			// Get all categories where at least one offer exists
			$offer_categories = $this->_get_offers($park_id, $categories, NULL, NULL, $filter, TRUE, TRUE, FALSE, FALSE, TRUE);
			if (mysqli_num_rows($offer_categories) > 0) {
				$categories = array();
				foreach ($offer_categories as $offer_category) {
					foreach ($offer_category as $single_category) {
						if ($single_category > 0) {
							$categories[$single_category] = $single_category;
						}
					}
				}
			}

			// Flat categories
			$flat_categories = $categories;

			// Categories has to be an array
			$categories = is_array($categories) ? $categories : array($categories);

			// Check, if categories are not empty
			$first = reset($categories);
			if (count($categories) == 0 || (count($categories) == 1 && empty($first))) {
				$categories = FALSE;
			}

			// Prepare categories for select field
			$categories = $this->_prepare_categories($categories);
			$categories = $this->_format_categories_for_select($categories);

			// Check if event filter (date-from, date-to) should be displayed
			$show_event_filter = FALSE;
			if ($event_count > 0) {
				$show_event_filter = TRUE;
			}

			// Check if route filter should be displayed (has enough routes, and is activated)
			$show_route_filter = FALSE;
			if (($this->config['show_route_filter'] == TRUE) && ($activity_count > 0)) {
				$show_route_filter = TRUE;
			}

			// Check if project filter should be displayed (has enough projects, and is activated)
			$show_project_filter = FALSE;
			if ($projects_count > 0) {
				$show_project_filter = TRUE;
			}

			// Prepare parks/users for select
			$users = array();
			if (empty($this->park_id)) {
				$users = $this->model->get_all_users($flat_categories, $filter);
			}

			// Check if only projects exists in export
			$projects_only = FALSE;
			if (
				($projects_count > 0)
				||
				(
					is_array($param_categories)
					&&
					in_array(CATEGORY_RESEARCH, $param_categories)
				)
			) {
				if (($event_count == 0) && ($booking_count == 0) && ($activity_count == 0) && ($product_count == 0)) {
					$projects_only = TRUE;
				}
			}

			// Load view
			$params = array(
				'categories' => $categories,
				'selected' => $this->filter_data,
				'filter' => $this->filter,
				'users' => empty($filter['hide_user_filter']) ? $users : array(),
				'show_event_filter' => $show_event_filter,
				'show_route_filter' => $show_route_filter,
				'show_target_group_filter' => $this->config['show_target_group_filter'],
				'show_accessibility_filter' => $this->config['show_accessibility_filter'],
				'show_project_filter' => $show_project_filter,
				'hide_accessibility_filter' => $filter['hide_accessibility_filter'] ?? FALSE,
				'projects_only' => $projects_only,
			);

			return $this->view->filter($params);

		}
	}


	/**
	 * Displays list of offers (optionally filtered)
	 *
	 * @access public
	 * @param array $categories (default: array())
	 * @param array $filter (default: array())
	 * @param int $park_id (default: NULL)
	 * @return string
	 */
	public function show_offers_list($categories = array(), $filter = array(), $park_id = NULL) {

		// Init main restrictions
		if (!empty($filter['system_filter']['target_groups'])) {
			$this->system_filter['target_groups'] = $filter['system_filter']['target_groups'];
			unset($filter['system_filter']['target_groups']);
		}

		// Set park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		if ($this->is_offer_detail() == FALSE) {
			$this->page = 1;

			// Handle seo urls
			$page_slug = !empty($this->config['seo_url_page_slug']) ? $this->config['seo_url_page_slug'] : '';
			if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE) && strstr($this->view->script_url, '/'.$page_slug.'/')) {

				// Split url by slashes
				$seo_url = explode('/', $this->view->script_url);

				// Get page id
				$page_segment_pos = count($seo_url) - 1;
				$this->page = intval($seo_url[$page_segment_pos]);

			}

			// Default urls
			else {
				$param_name = $this->config['url_param_prefix'].'page';
				if (isset($_GET[$param_name]) && $_GET[$param_name] >= 1) {
					$this->page = $_GET[$param_name];
				}
			}

			// Special case: exclude offers from user SAJA in offers list on parks.swiss
			if (empty($park_id) && strstr($_SERVER['HTTP_HOST'], 'parks.swiss')) {
				$filter['exclude_park_ids'] = array(37);
			}

			// Filter keywords by param
			if ((isset($_REQUEST['keyword']) || !empty($_SESSION[$this->session_name]['keyword'])) && !empty($this->config['keyword_filter'][$this->lang_id])) {

				$get_keyword = '';

				// Save selected keyword into session
				if (isset($_REQUEST['keyword'])) {
					$_SESSION[$this->session_name]['keyword'] = $_REQUEST['keyword'];
					$get_keyword = $_REQUEST['keyword'];
				}

				// Get selected keyword from session
				else if (!empty($_SESSION[$this->session_name]['keyword'])) {
					$get_keyword = $_SESSION[$this->session_name]['keyword'];
				}

				// Prepare keyword, replace special chars
				$get_keyword = str_replace("---", "*", $get_keyword);
				$get_keyword = str_replace("-_-", "/", $get_keyword);

				// Check keyword
				if (in_array($get_keyword, $this->config['keyword_filter'][$this->lang_id])) {
					$filter['keywords'] = $get_keyword;
				}
				
			}

			// Get offers and total
			$offers = $this->_get_offers($park_id, $categories, $this->page, $this->config['offers_per_page'], $filter, FALSE, TRUE);

			// Pagination
			$this->total = !empty($offers['total']) ? ceil($offers['total'] / $this->config['offers_per_page']) : 0;
			if ($this->total > $this->config['offers_per_page']) {
				$offset = ($this->page-1) * $this->config['offers_per_page'];
			}

			// Show offers list
			return $this->view->list_offers($offers);

		}

	}



	/**
	 * Displays map of offers (optionally filtered)
	 *
	 * @access public
	 * @param array $categories (default: array())
	 * @param array $filter (default: array())
	 * @param mixed $park_id (default: NULL)
	 * @return string
	 */
	public function show_offers_map($categories = array(), $filter = array(), $park_id = NULL) {

		// Init main restrictions
		if (!empty($filter['system_filter']['target_groups'])) {
			$this->system_filter['target_groups'] = $filter['system_filter']['target_groups'];
			unset($filter['system_filter']['target_groups']);
		}

		// Set park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		if (!$this->is_offer_detail()) {
			$return = $this->_load_maps_api();

			// Set park
			$this->_set_selected_park($park_id);

			// Get offers and total
			$offers = $this->_get_offers($park_id, $categories, NULL, NULL, $filter, FALSE, FALSE, FALSE, TRUE);
			$return .= $this->view->map($offers['data'] ?? '');

			return $return;
		}
	}


	/**
	 * Displays list of poi (get by id)
	 *
	 * @access public
	 * @param integer
	 * @return array
	 */
	public function show_offer_poi_list($poi = NULL) {
		$offers = array();

		if (is_array($poi) && !empty($poi)) {
			foreach ($poi as $offer_id) {
				$poi_offer = $this->model->get_offer($offer_id);
				if (!empty($poi_offer)) {
					array_push($offers, $poi_offer);
				}
			}
		}

		return $offers;
	}


	/**
	 * Show offers total
	 *
	 * @access public
	 * @return void
	 */
	public function show_total() {
		if ($this->total > 0) {
			echo $this->total.' '.(($this->total == 1) ? $this->lang->get('offer') : $this->lang->get('offers'));
		}
	}


	/**
	 * Displays pagination
	 *
	 * @access public
	 * @return string
	 */
	public function show_offers_pagination() {
		if (!$this->is_offer_detail()) {
			return $this->view->pagination($this->page, $this->total);
		}
	}



	/**
	 * Display detail of offer
	 *
	 * @access public
	 * @param mixed $single_offer_id (default: NULL)
	 * @return void
	 */
	public function show_offer_detail($single_offer_id = NULL) {

		if ($this->is_offer_detail() || !empty($single_offer_id)) {

			// Init
			$original_offer_id = 0;
			$param_name = $this->config['url_param_prefix'].'offer';
			$detail_slug = !empty($this->config['seo_url_detail_slug']) ? $this->config['seo_url_detail_slug'] : '';
			$poi_slug = !empty($this->config['seo_url_poi_slug']) ? $this->config['seo_url_poi_slug'] : '';

			// Single offer mode
			if (!empty($single_offer_id) && (intval($single_offer_id) > 0)) {
				$this->single_mode = TRUE;
				$offer_id = intval($single_offer_id);
			}
			// Default mode
			else {

				// Handle seo urls
				if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE) && strstr($this->view->script_url, '/'.$detail_slug.'/')) {

					// Remove slash on last string position
					$script_url = rtrim($this->view->script_url, '/');

					// Split url by slashes
					$seo_url = explode('/', $script_url);

					// Handle poi details
					if (strstr($script_url, $poi_slug)) {

						// Get original offer id
						$last_segment_position = count($seo_url) - 1;
						$original_offer_id = intval($seo_url[$last_segment_position]);

						// Remove poi id from seo url
						$seo_url = array_slice($seo_url, 0, -2);
					}

					// Split last url segment by dashes
					$last_segment_position = count($seo_url) - 1;
					$title_slug = explode('-', $seo_url[$last_segment_position]);

					// Get offer id from last url segment
					$offer_id_position = count($title_slug) - 1;
					$offer_id = intval($title_slug[$offer_id_position]);

				}

				// Default urls
				else {
					$offer_id = intval($_GET[$param_name]);
				}

			}

			// Check offer id
			if (!empty($offer_id)) {

				// Get offer
				$offer = $this->model->get_offer($offer_id);
				if (!empty($offer)) {

					// Set selected park
					$this->_set_selected_park($offer->park_id);

					// Load view
					$return = $this->_load_maps_api();
					$return .= $this->view->detail($offer, $original_offer_id);

					return $return;
				}
				else {

					// Show 404 error
					header('HTTP/1.0 404 Not Found');
					echo '
						<h1>404 Not Found</h1>
						The page that you have requested could not be found.
					';
					exit();

				}
			}

			return FALSE;
		}
	}


	/**
	 * Display all users favorites
	 *
	 * @access public
	 * @return void
	 */
	public function show_favorites() {

		// Offer detail
		if ($this->is_offer_detail()) {
			$this->show_offer_detail();
		}

		// Offer list
		else {

			// Get all saved favorites
			$favorites = array();
			if (is_array($this->favorites) && !empty($this->favorites)) {
				foreach ($this->favorites as $offer_id) {
					$offer = $this->model->get_offer($offer_id);
					if (!empty($offer)) {
						array_push($favorites, $offer);
					}
				}

				// Prepare offers
				$offers = array(
					'data' => $favorites,
					'total' => count($favorites),
				);
			}

			// Show favorites list
			if (isset($offers['total']) && ($offers['total'] > 0)) {
				$this->view->list_offers($offers, FALSE,  FALSE,  NULL, '<a href="'.$this->config['favorites_script_path'].'/favorite.php?action=clean" id="clean_favorites" class="reset_link">'.$this->lang->get('favorites_remove_all').'</a>');
			}

			// No favorites available
			else {
				echo '<p class="no_favorites">'.$this->lang->get('favorites_empty').'</p>';
			}

		}

	}


	/**
	 * Toggle favorite state
	 * Add or remove favorite from list
	 *
	 * @access public
	 * @param mixed $offer_id
	 * @return mixed
	 */
	public function toggle_favorite($offer_id) {
		if (($offer_id > 0) && ($this->config['use_sessions'] == TRUE)) {

			// Remove offer from favorites
			if (in_array($offer_id, $this->favorites)) {
				unset($this->favorites[$offer_id]);
				$return = FALSE;
			}

			// Add new favorite
			else {
				$this->favorites[$offer_id] = $offer_id;
				$return = TRUE;
			}

			// Save session
			$_SESSION[$this->favorites_cookie_name] = $this->favorites;

			// Save cookie
			setcookie($this->favorites_cookie_name, serialize($this->favorites), time()+(60*60*24*365), '/');

			return $return;
		}

		return FALSE;
	}


	/**
	 * Empty favorites
	 *
	 * @access public
	 * @param mixed $offer_id
	 * @return void
	 */
	public function clean_favorites() {
		// Save session
		$_SESSION[$this->favorites_cookie_name] = array();

		// Save cookie
		setcookie($this->favorites_cookie_name, '', time(), '/');
	}


	/**
	 * Returns if detail page should be displayed
	 *
	 * @access public
	 * @return bool
	 */
	public function is_offer_detail() {

		// Init
		$is_offer_detail = FALSE;
		$param_name = $this->config['url_param_prefix'].'offer';
		$offer_id = !empty($_GET[$param_name]) ? intval($_GET[$param_name]) : NULL;
		$detail_slug = !empty($this->config['seo_url_detail_slug']) ? $this->config['seo_url_detail_slug'] : '';

		// Check seo url
		if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE) && strstr($this->view->script_url, '/'.$detail_slug.'/')) {
			$is_offer_detail = TRUE;
		}

		elseif (($offer_id > 0) && $this->model->offer_exists($offer_id)) {
			$is_offer_detail = TRUE;
		}

		return $is_offer_detail;
	}


	/**
	 * Returns if filter is active
	 *
	 * @access public
	 * @return bool
	 */
	public function is_filter_activated() {
		return $this->filter;
	}


	/**
	 * Returns filter data
	 *
	 * @access public
	 * @return bool
	 */
	public function get_filter_data() {
		return $this->filter_data;
	}


	/**
	 * Get list of offers
	 *
	 * @access public
	 * @param mixed $park_id (default: NULL)
	 * @param array $categories (default: array())
	 * @param mixed $page (default: NULL)
	 * @param mixed $limit (default: NULL)
	 * @param array $filter (default: array())
	 * @param mixed $ignore_filter (default: FALSE)
	 * @param mixed $return_minimal (default: FALSE)
	 * @param mixed $only_count_categories (default: FALSE)
	 * @param mixed $map_mode (default: FALSE)
	 * @return void
	 */
	public function get_offers_list($park_id = NULL, $categories = array(), $page = NULL, $limit = NULL, $filter = array(), $ignore_filter = FALSE, $return_minimal = FALSE, $only_count_categories = FALSE, $map_mode = FALSE) {
		return $this->_get_offers($park_id, $categories, $page, $limit, $filter, $ignore_filter, $return_minimal, $only_count_categories, $map_mode);
	}


	/**
	 * Get offer details
	 *
	 * @access public
	 * @param mixed $offer_id
	 * @return void
	 */
	public function get_offer_detail($offer_id) {
		return $this->model->get_offer($offer_id);
	}


	/**
	 * Get list of categories
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_categories_list() {
		return $this->model->get_all_categories();
	}


	/**
	 * Get list of categories preformatted for select inputs
	 *
	 * @access public
	 * @return mixed
	 */
	public function get_categories_list_for_select() {
		$categories = $this->model->get_category_tree();
		return $categories;
	}


	/**
	 * Setup process
	 *
	 * @access private
	 * @return void
	 */
	private function _setup() {
		$this->_security_checks();

		$q_api = $this->db->get('api');

		if ($q_api) {
			if (mysqli_num_rows($q_api) > 0) {
				$this->api = mysqli_fetch_object($q_api);
			}
			else {
				$this->api->initialized = false;
				$this->api->version = API_VERSION;

				$this->db->insert('api', (array)$this->api);
			}

			if (version_compare(API_VERSION, $this->api->version)) {
				$logger = new ParksLog($this);
				$logger->info('The current API version is out of date. Please update database.');
			}

			if (!$this->api->initialized) {

				//Initalize API
				$xml = $this->config['xml_export_offer_url'].$this->hash;
				$xml_map_layer =  $this->config['xml_export_map_layer_url'].$this->hash;

				// Import data from XML into database
				$this->import->import($xml);
				$this->import->import_map_layers($xml_map_layer);

				$this->api->initialized = true;
				$this->db->update('api', array('initialized' => 1));
			}
		}
		else {
			die("API could not be initialized.");
		}
	}


	/**
	 * Set current selected park
	 *
	 * @access public
	 * @param integer $park_id
	 * @return void
	 */
	public function _set_selected_park($park_id = NULL) {

		// Init park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		if ($this->config['use_sessions'] == TRUE) {
			if (!empty($park_id) && ($park_id > 0) && !empty($this->config['parks'][$park_id])) {
				$_SESSION[$this->session_name]['selectedPark'] = $this->config['parks'][$park_id];
			}
			else {
				unset($_SESSION[$this->session_name]['selectedPark']);
			}
		}

	}


	/**
	 * Validate hash
	 *
	 * @access public
	 * @return void
	 */
	public function _validate_hash() {
		$validate = @file_get_contents($this->config['xml_export_url'].'validate/'.$this->hash);

		if ($validate == "Valid") {
			return TRUE;
		}
		else if (!empty($this->hash) && strlen($this->hash) >= 32) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * Init filter
	 *
	 * @access public
	 * @return void
	 */
	public function _init_filter() {

		// Init
		$param_name = $this->config['form_prefix'].'filter';
		$post_data = isset($_POST[$param_name]) ? $_POST[$param_name] : NULL;

		// Prepare URL params
		$get_params = FALSE;
		$allowed_get_params = array('categories', 'target_groups', 'accessibilities');
		foreach ($allowed_get_params as $param) {
			if (!empty($_GET[$param])) {
				$get_params = TRUE;
			}
		}

		// Set filter by all types
		$fields = array('categories', 'date_from', 'date_to', 'search', 'park_id', 'time_required', 'level_technics', 'level_condition', 'route_length_min', 'route_length_max', 'project_status', 'target_groups', 'accessibilities');
		foreach ($fields as $field) {

			// Get categories from URL
			if (empty($post_data) && ($get_params === TRUE) && in_array($field, $allowed_get_params)) {
				foreach ($allowed_get_params as $param) {
					if (!empty($_GET[$param])) {

						// Prepare get params
						$get_values = explode(',', $_GET[$param]);
						foreach ($get_values as $key => $value) {

							// Only allow integers
							if ($value > 0) {
								$get_values[$key] = intval($value);
							}
							// Otherwise, remove param
							else {
								unset($get_values[$key]);
							}

						}

						// Set filter by params
						if (!empty($get_values)) {
							$this->filter = TRUE;
							$this->filter_data[$param] = $get_values;
						}
					}
				}
			}

			// Post data
			elseif (!empty($post_data)) {
				$this->filter = TRUE;
				$this->filter_data[$field] = isset($post_data[$field]) ? $post_data[$field] : NULL;
			}

			// Session data
			elseif ($this->config['use_sessions'] && (isset($_SESSION[$this->session_name]['filter'][$field]) && !empty($_SESSION[$this->session_name]['filter'][$field]))) {
				$this->filter = TRUE;
				$this->filter_data[$field] = $_SESSION[$this->session_name]['filter'][$field];
			}

		}

		// Set filter in session
		if ($this->config['use_sessions']) {
			$_SESSION[$this->session_name]['filter'] = $this->filter_data;
		}

		// Reload page
		if (!empty($post_data) || ($get_params === TRUE)) {
			$this->_reload_page();
		}

	}


	/**
	 * Reset filter
	 *
	 * @access public
	 * @return void
	 */
	public function _reset_filter() {

		$this->filter_data = array();
		if ($this->config['use_sessions']) {
			unset($_SESSION[$this->session_name]['filter']);
		}

	}



	/**
	 * Reload page
	 * 
	 * @access protected
	 * @return void
	 */
	protected function _reload_page($reset_mode = FALSE) {

		// SEO URLs
		if (!empty($this->config['seo_urls']) && ($this->config['seo_urls'] === TRUE)) {
			header('Location: '.$this->view->script_url);
		}

		// No SEO URLs
		else {

			// Set URL
			$url = $this->view->script_url_with_params;

			// Remove reset param
			if ($reset_mode === TRUE) {
				$url = str_replace(array('?reset=1', '&reset=1'), '', $url);
			}

			// Redirect
			header('Location: '.$url);

		}
		
		exit;
	}



	/**
	 * Get offers
	 *
	 * @access public
	 * @param mixed $categories
	 * @return void
	 */
	public function get_offers($park_id = NULL, $categories = array()) {

		// Set park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		return $this->_get_offers($park_id, $categories);
	}


	/**
	 * Get offers
	 *
	 * @param int $park_id
	 * @param array $categories
	 * @param int $page
	 * @param int $limit
	 * @param array $additional_filter
	 * @param bool $ignore_filter
	 * @param bool $return_minimal
	 * @param bool $only_count_categories
	 * @param bool $map_mode
	 * @param bool $return_only_categories
	 * @return object
	 */
	public function _get_offers($park_id = NULL, $categories = array(), $page = NULL, $limit = NULL, $additional_filter = array(), $ignore_filter = FALSE, $return_minimal = FALSE, $only_count_categories = FALSE, $map_mode = FALSE, $return_only_categories = FALSE) {

		// Set park id
		if (empty($park_id)) {
			$park_id = $this->park_id;
		}

		// Init filter
		$filter = array();
		if ($ignore_filter == FALSE) {
			foreach ($this->filter_data as $field => $value) {
				if (!empty($value)) {
					$fieldname = substr($field, strlen($this->config['form_prefix']), strlen($field));
					$filter[$fieldname] = $value;
				}
			}
		}

		// Overwrite filter fields
		if (!empty($categories) && !isset($filter['categories'])) {
			$filter['categories'] = $categories;
		}

		// Offer settings
		if (!empty($additional_filter['offer_settings'])) {
			$filter['offer_settings'] = $additional_filter['offer_settings'];
		}

		// Target groups
		if (!empty($additional_filter['target_groups'])) {
			$filter['target_groups'] = $additional_filter['target_groups'];
		}

		// Accessibilities
		if (!empty($additional_filter['accessibilities'])) {
			$filter['accessibilities'] = $additional_filter['accessibilities'];
		}

		// Search words
		if (!empty($additional_filter['search'])) {
			$filter['search'] = $additional_filter['search'];
		}

		// Exclude park ids
		if (!empty($additional_filter['exclude_park_ids']) && is_array($additional_filter['exclude_park_ids'])) {
			$filter['exclude_park_ids'] = $additional_filter['exclude_park_ids'];
		}

		// Order_by
		if (!empty($additional_filter['order_by'])) {
			$filter['order_by'] = $additional_filter['order_by'];
		}

		// TextOfferSearch
		if (!empty($additional_filter['textOfferSearch'])) {
			$filter['search'] .= $additional_filter['textOfferSearch'];
		}

		// Online_shop_enabled
		if (!empty($additional_filter['online_shop_enabled'])) {
			$filter['online_shop_enabled'] = $additional_filter['online_shop_enabled'];
		}

		// Offers_barrier_free
		if (!empty($additional_filter['offers_barrier_free'])) {
			$filter['barrier_free'] = $additional_filter['offers_barrier_free'];
		}

		// Offers_learning_opportunity
		if (!empty($additional_filter['offers_learning_opportunity'])) {
			$filter['learning_opportunity'] = $additional_filter['offers_learning_opportunity'];
		}

		// Offers_child_friendly
		if (!empty($additional_filter['offers_child_friendly'])) {
			$filter['child_friendly'] = $additional_filter['offers_child_friendly'];
		}

		// Offer_filter_hints
		if (isset($additional_filter['offers_filter_hints'])) {
			$filter['is_hint'] = $additional_filter['offers_filter_hints'];
		}

		// Keywords
		if (isset($additional_filter['keywords'])) {
			$filter['keywords'] = $additional_filter['keywords'];
		}

		// Contact_is_park_partner
		if (isset($additional_filter['contact_is_park_partner'])) {
			$filter['contact_is_park_partner'] = $additional_filter['contact_is_park_partner'];
		}

		// Is_park_event
		if (isset($additional_filter['offers_is_park_event'])) {
			$filter['offers_is_park_event'] = $additional_filter['offers_is_park_event'];
		}

		// Has accessibility informations
		if (isset($additional_filter['has_accessibility_informations'])) {
			$filter['has_accessibility_informations'] = $additional_filter['has_accessibility_informations'];
		}

		// Offers
		if (isset($additional_filter['offers'])) {
			$filter['offers'] = $additional_filter['offers'];
		}

		// Offer restrictions
		if (!empty($additional_filter['offerRestriction']) && ($additional_filter['offerRestriction'] == 'today')) {
			$filter['offers_of_today'] = TRUE;
		}

		// Park id
		if (!empty($park_id)) {
			$filter['park_id'] = $park_id;
		}

		// Get offset and limit
		$offset = 0;
		if (!empty($page) && is_numeric($page)) {
			$offset = ($page - 1) * $this->config['offers_per_page'];
		}

		return $this->model->filter_offers($filter, $limit, $offset, $return_minimal, $only_count_categories, $map_mode, $return_only_categories);
	}


	/**
	 * Load map api
	 *
	 * @access public
	 * @return string
	 */
	public function _load_maps_api() {
		if ($this->config['prevent_css_js_include'] == FALSE) {

			$output = '
				<script type="text/javascript">var dojoConfig = { locale: "'.$this->lang_id.'", parseOnLoad: true, useDeferredInstrumentation: false };</script>
				<link rel="stylesheet" type="text/css" media="screen,projection" href="https://js.arcgis.com/3.32/esri/css/esri.css" />
				<link rel="stylesheet" type="text/css" href="'.$this->config['base_url'].'swissmap/css-min/swissmap.css" />
				<script type="text/javascript" src="https://js.arcgis.com/3.32/"></script>
				<script type="text/javascript" src="https://angebote.paerke.ch/swissmap/js-min/swissmap.js"></script>
				<script type="text/javascript" src="https://angebote.paerke.ch/swissmap/js-min/maps.arcgis.js"></script>
			';
			// Show output
			if ($this->return_output === TRUE) {
				return $output;
			}
			else {
				echo $output;
			}

		}
	}


	/**
	 * Format categories for selcect-field
	 *
	 * @access public
	 * @param mixed $categories
	 * @param mixed $index (default: 0)
	 * @param int $level (default: 0)
	 * @return array
	 */
	public function _format_categories_for_select($categories, $index = 0, $level = 0) {
		$return = array();

		// Get main category on top
		$main_category = array();
		if (isset($categories[0]) && is_array($categories[0])) {
			$main_category = array_keys($categories[0]);
			$main_category = reset($main_category);
		}

		// Special cases
		if (($level == 0) && ($index == 0) && ($main_category == CATEGORY_PRODUCT) && isset($categories[CATEGORY_GASTRONOMY_AND_ACCOMMODATION]) && (count($categories[CATEGORY_PRODUCT]) == 1)) {
			$index = CATEGORY_GASTRONOMY_AND_ACCOMMODATION;
		}

		if (isset($categories[$index]) && !empty($categories[$index])) {
			foreach ($categories[$index] as $id => $category) {
				if (
						(($level != 0) || in_array($id, array(CATEGORY_ACTIVITY, CATEGORY_PRODUCT)))
						&&
						(($level != 1) || !in_array($index, array(CATEGORY_ACTIVITY, CATEGORY_PRODUCT)))
						&&
						(($level != 2) || !in_array($index, array(CATEGORY_GASTRONOMY_AND_ACCOMMODATION)))
				) {
					$return[$id] = $category;
				}

				if (isset($categories[$id])) {
					$childs = $this->_format_categories_for_select($categories, $id, $level+1);
					if (!empty($childs)) {
						if (
							(($level == 0) && !in_array($id, array(CATEGORY_ACTIVITY, CATEGORY_PRODUCT)))
							||
							(($level == 1) && in_array($index, array(CATEGORY_ACTIVITY, CATEGORY_PRODUCT)))
							||
							(($level == 2) && in_array($index, array(CATEGORY_GASTRONOMY_AND_ACCOMMODATION)))
						) {
							$return[$category] = $childs;
						}
						else {
							$return += $childs;
						}
					}
				}
			}
		}

		return $return;
	}


	/**
	 * Prepare categories
	 *
	 * @access public
	 * @param mixed $categories (default: FALSE)
	 * @return void
	 */
	public function _prepare_categories($categories = FALSE) {
		$return = array();
		$parents = array();

		// Get all categories
		$all_categories = $this->model->get_all_categories();

		// Get all parents
		if (is_array($categories) && !empty($categories)) {
			foreach ($categories as $category_id) {
				if (($category_id > 0) && isset($all_categories[$category_id]) && !empty($all_categories[$category_id])) {
					$parents = array_merge($parents, $all_categories[$category_id]->parents);
				}
			}
		}

		// Return structured categories
		if (is_array($all_categories) && !empty($all_categories)) {
			foreach ($all_categories as $category_id => $category) {
				// Set parent id
				$parent_id = $category->parent_id ? $category->parent_id : 0;

				// Add category
				if (($categories == FALSE) || (in_array($category_id, $categories) || in_array($category_id, $parents))) {
					$return[$parent_id][$category_id] = $category->body;
				}
			}
		}

		// Return flat (non structured) categories
		else {
			$return = $categories;
		}

		return $return;
	}


	/**
	 * Security checks
	 *
	 * @access public
	 * @return void
	 */
	public function _security_checks() {
		if (!empty($_GET) && is_array($_GET)) {
			foreach ($_GET as $key => $value) {
				if (is_string($value)) {

					// Sql injection protection
					$tmp = str_replace(array("*", "'", '"', ";", "="), "", $value);
					
					// Cross site scripting protection
					$tmp = str_replace(array("<", ">"), array("&lt;", "&gt;"), $tmp);

					// Escape html entities
					$tmp = htmlentities($tmp, ENT_QUOTES);

					// Reset param
					$_GET[$key] = $tmp;
				
				}
			}
		}
	}


	/**
	 * Add slashes
	 *
	 * @access public
	 * @param mixed &$item
	 * @param mixed $key
	 * @return void
	 */
	public function _add_slashes(&$item, $key) {
		$item = (!is_array($item) ? addslashes($item) : $item);
	}



	/**
	 * Get API configuration
	 *
	 * @access public
	 * @return void
	 */
	public function _get_config() {

		// Load config file
		if (file_exists($config_path = realpath(dirname(__FILE__)).'/../config.php')) {
			require($config_path);
		}
		else {
			echo 'The configuration file does not exist.';
			exit();
		}

		// Check config data
		if (!isset($config) || !is_array($config)) {
			echo 'Your config file does not appear to be formatted correctly.';
			exit();
		}

		return $config;

	}


	/**
	 * Load system config and merge with user config
	 *
	 * @access private
	 * @return void
	 */
	private function _load_system_config() {

		// Base url
		$this->config['base_url'] = "https://angebote.paerke.ch/";

		// Xml import urls
		$this->config['xml_export_url'] = $this->config['base_url']."export/";
		$this->config['xml_export_offer_url'] = $this->config['base_url']."export/xml/";
		$this->config['xml_export_map_layer_url'] = $this->config['base_url']."export/xml/map_layers/";
		$this->config['xml_export_active_offers'] = $this->config['base_url']."export/xml/active_offers/";
		$this->config['json_export_target_groups'] = $this->config['base_url']."export/json/target_groups";
		$this->config['json_export_categories'] = $this->config['base_url']."export/json/categories";
		$this->config['json_export_accessibilities'] = $this->config['base_url']."export/json/accessibilities";

		// Sbb link
		$this->config['min_chars_sbb_link'] = 3;

		// Project status
		$this->config['project_status_de'] = array(
			1 => 'Geplant',
			2 => 'Laufend',
			3 => 'Abgeschlossen',
		);
		$this->config['project_status_fr'] = array(
			1 => 'Planifié',
			2 => 'Actuel',
			3 => 'Terminé',
		);
		$this->config['project_status_it'] = array(
			1 => 'Pianificato,',
			2 => 'In corso',
			3 => 'Concluso',
		);
		$this->config['project_status_en'] = array(
			1 => 'Planned',
			2 => 'Ongoing',
			3 => 'Finished',
		);

		// Parks
		$this->config['parks'] = array(
			2  => 'lpb',
			3  => 'snp',
			4  => 'jpa',
			5  => 'prc',
			6  => 'die',
			8  => 'frg',
			9  => 'bvm',
			10 => 'ela',
			11 => 'ube',
			12 => 'npt',
			13 => 'gpe',
			14 => 'wpz',
			15 => 'npb',
			16 => 'prd',
			17 => 'adu',
			18 => 'pnl',
			19 => 'pjv',
			20 => 'npf',
			27 => 'nps',
			28 => 'npn',
			33 => 'sar',
			34 => 'pdj',
			37 => 'wja',
			43 => 'cal',
			48 => 'pvt'
		);

		// Route times
		$this->config['route_times'] = array(
			$this->lang->get('offer_time_required_2h'),
			$this->lang->get('offer_time_required_2_4h'),
			$this->lang->get('offer_time_required_4_6h'),
			$this->lang->get('offer_time_required_6h')
		);

		// Route levels
		$this->config['route_levels'] = array(
			1 => $this->lang->get('offer_easy'),
			2 => $this->lang->get('offer_average'),
			3 => $this->lang->get('offer_difficult')
		);

		// Hidden categories on overview and detail page
		$this->config['hidden_categories'] = array(
			CATEGORY_GASTRONOMY_AND_ACCOMMODATION,
			CATEGORY_REGIONAL_PRODUCT,
			CATEGORY_GASTRONOMY,
			CATEGORY_ACCOMMODATION,
			CATEGORY_SIGHT,
			CATEGORY_SUMMER_ACTIVITIES,
			CATEGORY_WINTER_ACTIVITIES,
			CATEGORY_INFORMATION,
			CATEGORY_INFRASTRUCTURE,
		);

		// Template tags
		$this->config['template_tags'] = array(
			'OFFER_TITLE',
			'OFFER_SHORT_INFO',
			'OFFER_CATEGORIES',
			'OFFER_IMAGES',
			'OFFER_ABSTRACT',
			'OFFER_DESCRIPTION',
			'OFFER_ADDITIONAL_INFO',
			'OFFER_DATES',
			'OFFER_DOCUMENTS',
			'OFFER_EVENT_DETAIL',
			'OFFER_PRODUCT_DETAIL',
			'OFFER_BOOKING_DETAIL',
			'OFFER_ACTIVITY_DETAIL',
			'OFFER_RESEARCH_DETAIL',
			'OFFER_LINKS',
			'OFFER_ACCESSIBILITIES',
			'OFFER_TARGET_GROUPS',
			'OFFER_SUPPLIERS',
			'OFFER_INSTITUTION',
			'OFFER_CONTACT',
			'OFFER_POI_LIST',
			'OFFER_ROUTE_LIST',
			'OFFER_EVENT_LOCATION',
			'OFFER_EVENT_LOCATION_SHORT',
			'OFFER_EVENT_LOCATION_DETAILS',
			'OFFER_EVENT_TRANSPORT',
			'OFFER_EVENT_DATE_DETAILS',
			'OFFER_EVENT_PRICE',
			'OFFER_PRODUCT_OPENING_HOURS',
			'OFFER_PRODUCT_PUBLIC_TRANSPORT',
			'OFFER_PRODUCT_PRICE',
			'OFFER_PRODUCT_INFRASTRUCTURE',
			'OFFER_ONLINE_SHOP_CHECKOUT_BUTTON',
			'OFFER_BOOKING_GROUPS',
			'OFFER_BOOKING_TRANSPORT',
			'OFFER_BOOKING_BENEFITS',
			'OFFER_BOOKING_REQUIREMENTS',
			'OFFER_BOOKING_PRICE',
			'OFFER_BOOKING_ACCOMMODATIONS',
			'OFFER_ACTIVITY_ROUTE',
			'OFFER_ACTIVITY_ARRIVAL',
			'OFFER_ACTIVITY_PRICE',
			'OFFER_ACTIVITY_CATERING',
			'OFFER_ACTIVITY_MATERIAL_RENT',
			'OFFER_ACTIVITY_SAFETY_INSTRUCTIONS',
			'OFFER_ACTIVITY_SIGNALIZATION',
			'OFFER_ACTIVITY_DATES',
			'OFFER_ACTIVITY_INFRASTRUCTURE',
			'OFFER_PROJECT_DURATION',
			'OFFER_PROJECT_STATUS',
			'OFFER_SUBSCRIPTION',
			'OFFER_INTERNAL_INFOS',
			'OFFER_PROJECT_DETAIL',
			'OFFER_MAP',
			'OFFER_PRINT_LINK',
			'OFFER_BACK_LINK',
			'OFFER_KEYWORDS',
			'FILTER_TEXT_SEARCH',
			'FILTER_CATEGORIES',
			'FILTER_DATES',
			'FILTER_TARGET_GROUPS',
			'FILTER_ACCESSIBILITIES',
			'FILTER_PARKS',
			'FILTER_PROJECT',
			'FILTER_ROUTE_LENGTH',
			'FILTER_ROUTE_TIME',
			'FILTER_ROUTE_CONDITION',
			'FILTER_KEYWORDS',
			'FILTER_SHOW_LINK',
			'FILTER_FORM_START',
			'FILTER_FORM_STOP',
			'FILTER_RESET_BUTTON',
			'FILTER_SUBMIT_BUTTON'
		 );

		 // Template conditions
		 $this->config['template_conditions'] = array(
			'OFFER_EVENT',
			'OFFER_PRODUCT',
			'OFFER_BOOKING',
			'OFFER_ACTIVITY',
			'OFFER_PROJECT',
			'OFFER_RESEARCH'
		 );

	}



	/**
	 * Load external xml source
	 *
	 * @param string $url
	 * @return mixed
	 */
	function load_external_xml($url) {
		if ($url != '') {
	
			// Init CURL
			$ch = curl_init();

			// Set CURL options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			// No SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			// Get external data
			$external_data = curl_exec($ch);

			// Close CURL
			curl_close($ch);

			// Error: No data found
			if (empty($external_data)) {
				return FALSE;
			}

			// Success: Return xml
			return simplexml_load_string($external_data);

		}

		return FALSE;
	}


}