<?php

namespace SayHello\ShpGantrischAdb\Model;

use SayHello\ShpGantrischAdb\Controller\API as APIController;
use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;

use DateTime;
use DOMElement;
use DOMNodeList;
use ParksAPI;
use stdClass;
use WP_Error;

class Offer
{
	private $locale = 'de_CH';
	private $language = 'de';
	private $offers = [];
	private $requested_id = null;
	private $date_format = 'Y/m/d';
	private $debug = false;
	private $api_controller = null;

	/**
	 * Individual data sets will be cached for a short
	 * time, so that multiple blocks can make multiple
	 * data requests in the same stream without
	 * firing multiple database queries.
	 *
	 * Each entry is in seconds unless otherwise noted.
	 *
	 * @var array
	 */
	private $transient_lives = [
		'single' => 10,
		'all_offers' => HOUR_IN_SECONDS
	];

	/**
	 * Which languages are available in the data which
	 * is delivered by the remote API?
	 *
	 * @var array
	 */
	private $supported_languages = [
		'de',
		'fr',
		'it',
		'en'
	];

	private $sbb_timetable_urls = [
		'de' => 'https://www.sbb.ch/de/kaufen/pages/fahrplan/fahrplan.xhtml?nach=%s',
		'fr' => 'https://www.sbb.ch/fr/acheter/pages/fahrplan/fahrplan.xhtml?nach=%s',
		'it' => 'https://www.sbb.ch/it/acquistare/pages/fahrplan/fahrplan.xhtml?nach=%s',
		'en' => 'https://www.sbb.ch/en/buying/pages/fahrplan/fahrplan.xhtml?nach=%s',
	];

	/**
	 * Database table names. Parametrised in case they change later.
	 *
	 * @var array
	 */
	private $tables = [
		'activity' => 'activity',
		'booking' => 'booking',
		'offer' => 'offer',
		'offer_image' => 'image',
		'offer_date' => 'offer_date',
		'offer_i18n' => 'offer_i18n',
		'category' => 'category',
		'category_i18n' => 'category_i18n',
		'category_link' => 'category_link',
		'subscription' => 'subscription',
		'subscription_i18n' => 'subscription_i18n',
		'target_group' => 'target_group',
		'target_group_i18n' => 'target_group_i18n',
		'target_group_link' => 'target_group_link',
	];

	public function __construct()
	{
		$this->api_controller = new APIController();
		$this->setConfig();
	}

	public function run()
	{
		add_action('wp', [$this, 'setLanguage']);
	}

	public function setConfig()
	{
		$this->date_format = get_option('date_format');
		$this->debug = defined('WP_DEBUG') && WP_DEBUG;
	}

	public function setLanguage()
	{
		$this->locale = get_locale();

		$lang_sub = substr($this->locale, 0, 2);

		if (in_array($lang_sub, $this->supported_languages)) {
			$this->language = $lang_sub;
		} else {
			$this->language = 'de';
		}
	}

	/**
	 * Getter for the private tables obkect
	 *
	 * @return array
	 */
	public function getTables()
	{
		return $this->tables;
	}

	/**
	 * Getter for the private language object
	 *
	 * @return array
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Getter for the private tables object
	 *
	 * @return array
	 */
	public function getSupportedLanguages()
	{
		return $this->supported_languages;
	}

	public function getSinglePageID()
	{
		return (int) get_field('shp_gantrisch_adb_single_page', 'options');
	}

	public function getRequestedOfferID()
	{

		if ($this->requested_id !== null) {
			return $this->requested_id;
		}

		// Do NOT run this on the constructor or it will create an infinite loop
		$offer_controller = new OfferController();

		$var_name = $offer_controller->queryVarName();
		$var_value = get_query_var($var_name);

		if (empty($var_value)) {
			return null;
		}

		$this->requested_id = preg_replace('/[^0-9]/', '', $var_value);

		return $this->requested_id;
	}

	/**
	 * Get the language-appropriate SBB timetable URL
	 *
	 * @return string
	 */
	public function getSBBTimetableURL()
	{
		return $this->sbb_timetable_urls[$this->getLanguage()] ?? '#%s';
	}

	/**
	 * Get all of the offer data by offer ID.
	 * The dataset will contain the localised data from
	 * the i18n table.
	 *
	 * @param integer $offer_id Optional - if not defined, the model class will try and find the current order_id
	 * @return mixed The database result.
	 */
	public function getOffer($offer_id = null)
	{

		if (!$offer_id) {
			$offer_id = $this->getRequestedOfferID();
		}

		if (!$offer_id) {
			return null;
		}

		if (isset($this->offers[$offer_id])) {
			return $this->offers[$offer_id];
		}



		$api = $this->api_controller->getApi();

		if (!$api instanceof ParksAPI) {
			return null;
		}

		$this->offers[$offer_id] = $api->model->get_offer($offer_id);
		return $this->offers[$offer_id];
	}

	public function getTitle($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer || !isset($offer->title) || empty($offer->title)) {
			return null;
		}

		return strip_tags($offer->title);
	}

	public function getKeywords($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass || !isset($offer->keywords)) {
			return new WP_Error(404, _x('There is no localised title available for this entry', 'Fallback title', 'shp_gantrisch_adb'));
		}

		if (empty($offer->keywords)) {
			return null;
		}

		$keywords = explode(PHP_EOL, $offer->keywords);
		return array_map('strip_tags', $keywords);
	}

	public function getCategories($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer) {
			return null;
		}

		$results = $offer->categories ?? [];

		if (empty($results)) {
			return null;
		}

		$categories = [];

		// Create all top-level categories
		foreach ($results as $result) {
			$result = (array) $result;
			if (!array_key_exists("category{$result['category_id']}", $categories)) {
				$categories["category{$result['category_id']}"] = $result;
				$categories["category{$result['category_id']}"]['categories'] = [];
			}
		}

		// Create all second-level categories
		foreach ($results as $result) {
			$result = (array) $result;
			if (isset($categories["category{$result['parent_id']}"])) {
				$categories["category{$result['parent_id']}"]['categories'][] = (array) $result;

				// Remove second-level categories from top level
				if (isset($categories["category{$result['category_id']}"])) {
					unset($categories["category{$result['category_id']}"]);
				}
			}
		}

		return $categories;
	}

	/**
	 * Get the from and to dates for a specific offer.
	 *
	 * @param mixed $offer
	 * @param string $formatted Return format: raw, legible or integer
	 * @param string $mode future dates only, or all
	 * @return void
	 */
	public function getDates($offer = null, $format = 'raw', $mode = '')
	{

		if ($offer instanceof DOMElement) {
			$offer = (int) str_replace('offer_', '', $offer->getAttribute('id'));
		}

		if (is_int($offer)) {
			$offer = $this->getOffer($offer);
		}

		if (is_array($offer)) {
			// Convert to stdClass
			$offer = json_decode(json_encode($offer));
		}

		if (!$offer instanceof stdClass || empty($offer)) {
			return [];
		}

		$return = [];

		$termine = $this->getTermine($offer->offer_id, 'YmdHis');

		if (!is_array($termine) || empty($termine)) {
			return [];
		}

		usort($termine, function ($a, $b) {
			return strtotime($a['date_from']) - strtotime($b['date_from']);
		});

		// Filter out past dates and reset array index
		if ($mode === 'future') {
			$termine = array_values(array_filter($termine, function ($termin) {
				return strtotime($termin['date_from']) > time();
			}));
		}

		// Default format is raw, which will return the
		// unmanipulated value from the database table
		switch ($format) {
			case 'legible':
				$return['date_from'] = wp_date($this->date_format, strtotime($termine[0]['date_from'] ?? 0));
				$return['date_to'] = wp_date($this->date_format, strtotime($termine[0]['date_to'] ?? 0));
				break;
			case 'integer':
				$return['date_from'] = strtotime($termine[0]['date_from'] ?? 0);
				$return['date_to'] = strtotime($termine[0]['date_to'] ?? 0);
				break;
			case 'raw':
				$return['date_from'] = $termine[0]['date_from'] ?? 0;
				$return['date_to'] = $termine[0]['date_to'] ?? 0;
				break;
		}

		return $return;
	}

	/**
	 * Get all images for the indicated offer
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getImages($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		return $offer->images ?? [];
	}

	/**
	 * Get i18n excerpt (description_medium)
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getExcerpt($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass || !isset($offer->description_medium)) {
			return new WP_Error(404, _x('There is no localised title available for this entry', 'Fallback title', 'shp_gantrisch_adb'));
		}

		return strip_tags($offer->description_medium ?? '');
	}

	/**
	 * Get infrastructure
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getInfrastructure($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return null;
		}

		return strip_tags($offer->other_infrastructure ?? '');
	}

	/**
	 * Get long description
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getDescriptionLong($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return null;
		}

		return wpautop(strip_tags($offer->description_long ?? ''));
	}

	/**
	 * Get contact info - not i18n!
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getContact($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return null;
		}

		return [
			'contact' => nl2br(make_clickable(strip_tags($offer->contact ?? ''))),
			'is_partner' => $this->isParkPartner($offer_id),
		];
	}

	/**
	 * Get park season information
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getSeason($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		$season_months = $offer->season_months ?? '';
		$season_months_array = array_filter(explode(',', $season_months));

		if (empty($season_months_array)) {
			return '';
		}

		$month_names = [];
		$year = wp_date('Y');

		foreach ($season_months_array as $month_number) {
			if ($month_number < 10) {
				$month_number = "0{$month_number}";
			}

			$dt = DateTime::createFromFormat(DateTime::ISO8601, "{$year}-{$month_number}-01T00:00:00Z");

			if (!$dt instanceof DateTime) {
				continue;
			}

			$month_names[] = wp_date('F', $dt->getTimestamp());
		}

		return $month_names;
	}

	/**
	 * Get offer benefits (Leistungen)
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getBenefits($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		return $offer->benefits ?? '';
	}

	/**
	 * Get offer price
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getPrice($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		return $offer->price ?? '';
	}

	/**
	 * Get offer target audience
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getTarget($offer_id = null)
	{

		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		return $offer->target_groups ?? [];
	}

	/**
	 * Get offer subscription information
	 *
	 * @param integer $offer_id
	 * @return mixed
	 */
	public function getSubscription($offer_id = null)
	{

		$offer = $this->getOffer($offer_id);

		if (!$offer) {
			return null;
		}

		return [
			'subscription_contact' => $offer->subscription_contact ?? '',
			'subscription_details' => $offer->subscription_details ?? '',
			'subscription_link' => $offer->subscription_link ?? false,
			'subscription_mandatory' => $offer->subscription_mandatory ?? false,
			'online_subscription_enabled' => $offer->online_subscription_enabled ?? false,
		];
	}

	public function getTransportStop(string $start_stop = 'start')
	{

		$offer = $this->getOffer();

		if (!$offer) {
			return '';
		}

		$property_name = "public_transport_{$start_stop}";

		return $offer->{$property_name} ?? '';
	}

	/**
	 * Get all offers. Random sort, then pull tips to the top of the list.
	 * If $number_required is passed, then splice the
	 * result set and return only those offers.
	 * The custom sort is only applied if $custom_sort is true.
	 *
	 * @param array $category_ids
	 * @param array $keywords
	 * @param integer $number_required
	 * @param boolean $exclude_current
	 * @return array
	 */
	public function getAll($category_ids = [], $keywords = [], $number_required = 0, $exclude_current = false, $custom_sort = true)
	{

		if (!is_array($category_ids)) {
			$category_ids = (array) $category_ids;
		}

		if (!is_array($keywords)) {
			$keywords = (array) $keywords;
		}

		$keywords = array_filter($keywords);

		$transient_cat = md5(implode('', $category_ids));
		$transient_keywords = md5(implode('', $keywords));
		$transient_key = !empty($category_ids) ? "adb_offers_cat_{$transient_cat}_key_{$transient_keywords}" : "adb_offer_all";
		$offers = get_transient($transient_key);

		if ($this->debug || empty($offers) || (bool)($_GET['force'] ?? '') === true) {
			$api = $this->api_controller->getApi();

			if (!empty($keywords)) {
				$offers = $api->_get_offers(null, $category_ids, null, null, ['keywords' => implode(' ', $keywords)]);
			} else {
				$offers = $api->_get_offers(null, $category_ids);
			}

			if (is_array($offers) && is_array($offers['data'] ?? false) && !empty($offers['data'])) {
				set_transient($transient_key, $offers, $this->transient_lives['all_offers']);
			}
		}

		$offers = $offers['data'] ?? [];

		if (!is_array($offers)) {
			return new WP_Error(500, "Database error when requesting all offers.");
		}

		if (empty($offers)) {
			return [];
		}

		// Don't include the current post in a list -- e.g. on a single view
		if ($exclude_current) {
			$current_id = (int) $this->getRequestedOfferID();
			if ($current_id) {
				foreach ($offers as $key => $offer) {
					if ((int) $offer->offer_id === $current_id) {
						unset($offers[$key]);
					}
				}
			}
		}

		if (!$custom_sort) {
			// Trim down the array if necessary
			if ($number_required > 0) {
				if (count($offers) > $number_required) {
					$offers = array_splice($offers, 0, $number_required);
				}
			}

			return $offers;
		}

		/**
		 * Custom sorting: hints first, then entries with dates, then sort
		 * the remaining entries in a random order. Note that we don't
		 * need to sort the hints or the entries with Termine becuase
		 * the API hs already put them in the correct order.
		 *
		 * Warning 5.7.2023: the same logic of this function is also
		 * applied through DomDocument manipulation on DOM nodes in
		 * the render_block filter in the list block.
		 *
		 * Make sure that the logic from this function is also applied
		 * in the sortOfferDomNodes function.
		 */

		$offers_sorted = [];
		$exclude_from_rest = [];

		// Pull hints to the top of the list
		foreach ($offers as $offer) {
			if ($offer->is_hint) {
				$offers_sorted["offer{$offer->offer_id}"] = $offer;

				// Make sure that this offer doesn't appear in the "rest" list
				if (!in_array($offer->offer_id, $exclude_from_rest)) {
					$exclude_from_rest[] = $offer->offer_id;
				}
			}
		}

		foreach ($offers as $offer) {
			$timestamps = $this->getDates($offer, 'integer', 'future');
			if ((int) $timestamps['date_from']) {
				$offers_sorted["ts-{$timestamps['date_from']}-offer-{$offer->offer_id}"] = $offer;

				// Make sure that this offer doesn't appear in the "rest" list
				if (!in_array($offer->offer_id, $exclude_from_rest)) {
					$exclude_from_rest[] = $offer->offer_id;
				}
			}
		}

		// Fill the array with the remaining entries
		$the_rest = [];
		$the_rest_iterator = 0;
		foreach ($offers as $offer) {
			// Exclude entries from $exclude_from_rest
			if (in_array($offer->offer_id, $exclude_from_rest)) {
				continue;
			}

			$the_rest[] = $offer;
			$the_rest_iterator++;
		}

		if (!empty($the_rest)) {
			shuffle($the_rest);
			foreach ($the_rest as $the_rest_entry) {
				if (!array_key_exists("offer{$the_rest_entry->offer_id}", $offers_sorted)) {
					$offers_sorted["offer{$the_rest_entry->offer_id}"] = $the_rest_entry;
				}
			}
		}

		// Trim down the array if necessary
		if ($number_required > 0) {
			if (count($offers_sorted) > $number_required) {
				$offers_sorted = array_splice($offers_sorted, 0, $number_required);
			}
		}

		return array_values($offers_sorted);
	}

	/**
	 * Get offers from the indicated category
	 *
	 * @param integer $category_id
	 * @param boolean $shuffle
	 * @return array
	 */
	public function getByCategory(int $category_id)
	{
		if (!$category_id) {
			return [];
		}

		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer.*,i18n.* FROM {$this->tables['offer']} offer, {$this->tables['offer_i18n']} i18n WHERE offer.offer_id = i18n.offer_id and i18n.language = %s ORDER BY offer.offer_id DESC", $this->getLanguage());
		$offers = @$wpdb->get_results($sql, ARRAY_A);

		if (!is_array($offers)) {
			return new WP_Error(500, "Database error when requesting all offers.");
		}

		if (empty($offers)) {
			return [];
		}

		return $offers;
	}

	/**
	 * Pass by reference. Clean and convert the input to an imploded trimmed array
	 *
	 * @param array|string $keywords
	 * @return string
	 */
	public function prepareKeywords(array|string $keywords): string
	{
		if (is_string($keywords)) {
			$keywords = preg_split('/([\r\n\t\s])/', trim($keywords));
		}

		if (is_array($keywords)) {
			$keywords = array_slice(array_filter($keywords), 0); // Remove empties
			array_walk($keywords, function (&$keyword) {
				preg_replace('/([\r\n\t])/', '', trim($keyword));
			});
		}

		if (is_array($keywords) && count($keywords) > 0) {
			$keywords = array_unique($keywords);
		}

		if (is_array($keywords)) {
			$keywords = implode(' ', $keywords);
		}

		return $keywords;
	}

	public function getTermin($offer_id = null)
	{

		if (!function_exists('parks_mysql2date') || !function_exists('parks_show_date') || !function_exists('parks_mysql2form')) {
			return '';
		}

		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		if (empty($offer->date_from) && empty($offer->date_to)) {
			return '';
		}

		$date_from = parks_mysql2date($offer->date_from, true);
		$date_to = parks_mysql2date($offer->date_to, true);

		return parks_show_date([
			'date_from' => parks_mysql2form($date_from),
			'date_to' => parks_mysql2form($date_to)
		], $this->api_controller->getApi()->lang);
	}

	/**
	 * Builds a list of Termine based on the dates field
	 *
	 * @param $offer_id
	 * @param string $format
	 * @return array
	 */
	public function getTermine($offer_id = null, $format = '')
	{

		if (!function_exists('parks_mysql2date') || !function_exists('parks_show_date') || !function_exists('parks_mysql2form')) {
			return '';
		}

		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		$return = [];

		foreach ($offer->dates as $termin) {
			// Don't use wp_date here: we need the specific date and time, not the timezone-relevant date and time
			$date_from = parks_mysql2date(date('Y-m-d H:i:s', strtotime($termin->date_from)), true);
			$date_to = parks_mysql2date(date('Y-m-d H:i:s', strtotime($termin->date_to)), true);

			switch ($format) {
				case 'YmdHis':
					$return[] = [
						'date_from' => $date_from,
						'date_to' => $date_to
					];
					break;
				default:
					$return[] = parks_show_date([
						'date_from' => parks_mysql2form($date_from),
						'date_to' => parks_mysql2form($date_to)
					], $this->api_controller->getApi()->lang);
					break;
			}
		}

		return $return;
	}

	public function getPlace($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		return $offer->location_details ?? '';
	}

	public function getOpeningTimes($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		return $offer->opening_hours ?? '';
	}

	public function getTimeRequired($offer_id = null)
	{

		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		$time_required = $offer->time_required_minutes ?? '';

		if ((int) $time_required < 1) {
			return '';
		}

		$time_required_hours = intdiv($time_required, 60);
		$time_required_minutes = $time_required % 60;

		if ($time_required_minutes < 1) {
			$time_required = sprintf(
				_nx(
					'%1$s Stunde',
					'%1$s Stunden',
					$time_required_hours,
					'ADB time required',
					'shp-gantrisch_adb'
				),
				$time_required_hours
			);
		} else {
			if ($time_required_minutes > 1) {
				$time_required = sprintf(
					_nx(
						'%1$s Stunde %2$s Minuten',
						'%1$s Stunden %2$s Minuten',
						$time_required_hours,
						'ADB time required',
						'shp-gantrisch_adb'
					),
					$time_required_hours,
					$time_required_minutes
				);
			} else {
				$time_required = sprintf(
					_nx(
						'%1$s Stunde %2$s Minute',
						'%1$s Stunden %2$s Minute',
						$time_required_hours,
						'ADB time required',
						'shp-gantrisch_adb'
					),
					$time_required_hours,
					$time_required_minutes
				);
			}
		}

		return $time_required;
	}

	/**
	 * This allows us to pass in an HTML DOM node list
	 * of offers and sort them, first with "hints" at
	 * the top of the list, then by date, then the remainder
	 * in a random order.
	 *
	 * @param DOMNodeList $nodes
	 * @return array
	 */
	public function sortOfferDomNodes(DOMNodeList $nodes)
	{

		/**
		 * Custom grouping: hints first, then sort by date, then sort
		 * the remaining entries in a random order. Note that we don't
		 * need to sort the hints or the entries with Termine becuase
		 * the API hs already put them in the correct order.
		 *
		 * Warning 5.7.2023: the same logic of this function is also
		 * applied through manipulation of the Order objects in
		 * the Model function getAll.
		 *
		 * Make sure that the logic from this function is also applied
		 * there.
		 */

		$nodes_by_group = [];
		$exclude_from_rest = [];

		// Pull hints to the top of the list
		foreach ($nodes as $node) {
			if ($node->getAttribute('data-hint') === 'true') {
				$node_id = str_replace('offer_', '', $node->getAttribute('id'));
				$nodes_by_group["offer-hint-{$node_id}"] = $node;

				// Make sure that this offer doesn't appear in the "rest" list
				if (!in_array($node_id, $exclude_from_rest)) {
					$exclude_from_rest[] = $node_id;
				}
			}
		}

		// This just pulls out the entries with dates and times
		// So that they aren't included in the shuffle logic below.
		$iterator = 0;
		foreach ($nodes as $node) {
			$timestamps = $this->getDates($node, 'integer', 'future');
			if ((int) ($timestamps['date_from'] ?? false)) {
				$node->setAttribute('data-date-from', date('Y-m-d H:i:s', $timestamps['date_from']));
				$node_id = str_replace('offer_', '', $node->getAttribute('id'));
				$node->setAttribute('data-date-from', date('Y-m-d H:i:s', $timestamps['date_from']));
				$nodes_by_group["offer-termine-{$node_id}-{$iterator}"] = $node;

				// Make sure that this offer doesn't appear in the "rest" list
				if (!in_array($node_id, $exclude_from_rest)) {
					$exclude_from_rest[] = $node_id;
				}

				$iterator++;
			}
		}

		// Fill the array with the remaining entries
		$the_rest = [];
		$the_rest_iterator = 0;
		foreach ($nodes as $node) {
			// Exclude entries from $exclude_from_rest
			$node_id = str_replace('offer_', '', $node->getAttribute('id'));
			if (in_array($node_id, $exclude_from_rest)) {
				continue;
			}

			$the_rest[] = $node;

			$the_rest_iterator++;
		}

		if (!empty($the_rest)) {
			shuffle($the_rest);
			foreach ($the_rest as $node) {
				$node_id = str_replace('offer_', '', $node->getAttribute('id'));
				$nodes_by_group["offer-rest-{$node_id}"] = $node;
			}
		}

		unset($the_rest);

		return array_values($nodes_by_group);
	}

	/**
	 * Is the offer related to a park partner? This logic since 10.7.2023
	 *
	 * @param integer $offer_id
	 * @return boolean
	 */
	public function isParkPartner($offer_id = 0)
	{
		if (!$offer_id) {
			$offer_id = $this->getRequestedOfferID();
		}

		$offer_id = (int) $offer_id;

		$offer = $this->getOffer($offer_id);

		if (!$offer) {
			return false;
		}

		return (bool) ($offer->institution_is_park_partner ?? false) || (bool) ($offer->contact_is_park_partner ?? false) || (bool) ($offer->is_park_partner_event ?? false) || (bool) ($offer->is_park_partner ?? false);
	}
}
