<?php

namespace SayHello\ShpGantrischAdb\Model;

use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use DateTime;
use ParksAPI;
use stdClass;
use WP_Error;

class Offer
{
	private $locale = 'de_CH';
	private $language = 'de';
	private $cache = true;
	private $single_page = false;
	private $offers = [];

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
		'de', 'fr', 'it', 'en'
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

	private $date_format = 'Y/m/d';

	public function __construct()
	{
		//$this->cache = !defined('WP_DEBUG') || !WP_DEBUG; // Buggy 30.9.2022 mhm
		$this->cache = false;
		$this->date_format = get_option('date_format');
		$this->locale = get_locale();
		$this->single_page = get_field('shp_gantrisch_adb_single_page', 'options');

		$lang_sub = substr($this->locale, 0, 2);
		if (in_array($lang_sub, $this->supported_languages)) {
			$this->language = $lang_sub;
		}
	}

	public function run()
	{
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
		return $this->single_page;
	}

	public function requestedOfferID()
	{
		$controller = new OfferController();
		$var_name = $controller->queryVarName();
		$var_value = get_query_var($var_name);

		if (empty($var_value)) {
			return null;
		}

		return preg_replace('/[^0-9]/', '', $var_value);
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


		die($offer_id);

		if (!$offer_id) {
			$offer_id = $this->requestedOfferID();
		}


		if (!$offer_id) {
			return null;
		}

		dump($offer_id, 1, 1);
		if (isset($offers[$offer_id])) {
			return $offers[$offer_id];
		}

		$api = shp_gantrisch_adb_get_instance()->Controller->API->getApi();

		if (!$api instanceof ParksAPI) {
			return null;
		}

		$offers[$offer_id] = $api->model->get_offer($offer_id);
		return $offers[$offer_id];
	}

	public function getTitle($offer_id = null)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer || !isset($offer->title) || empty($offer->title)) {
			return new WP_Error(404, _x('There is no localised title available for this entry', 'Fallback title', 'shp_gantrisch_adb'));
		}

		return strip_tags($offer->title);
	}

	public function getKeywords(int $offer_id)
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

	public function getCategories(int $offer_id)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		$results = $offer->categories ?? [];

		if (empty($results)) {
			return null;
		}

		$categories = [];

		foreach ($results as $result) {
			$result = (array) $result;
			if (!array_key_exists("category{$result['category_id']}", $categories)) {
				$categories["category{$result['category_id']}"] = $result;
				$categories["category{$result['category_id']}"]['categories'] = [];
			}
		}

		foreach ($results as $result) {
			$result = (array) $result;
			if (isset($categories["category{$result['parent_id']}"])) {
				$categories["category{$result['parent_id']}"]['categories'][] = (array) $result;
			}
		}

		foreach ($categories as $key => $category) {
			if (empty($category['categories'])) {
				unset($categories[$key]);
			}
		}

		return $categories;
	}

	/**
	 * Get the from and to dates for a specific offer.
	 *
	 * @param integer $offer_id
	 * @param boolean $formatted Return format: raw, legible or integer
	 * @return void
	 */
	public function getDates(int $offer_id, $format = 'raw')
	{

		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return [];
		}

		$return = [];

		// Default format is raw, which will return the
		// unmanipulated value from the database table
		switch ($format) {
			case 'legible': {
					$return['date_from'] = wp_date($this->date_format, strtotime($offer->date_from ?? 0));
					$return['date_to'] = wp_date($this->date_format, strtotime($offer->date_to ?? 0));
					break;
				}
			case 'integer': {
					$return['date_from'] = strtotime($offer->date_from ?? 0);
					$return['date_to'] = strtotime($offer->date_to ?? 0);
					break;
				}
			case 'raw': {
					$return['date_from'] = $offer->date_from ?? 0;
					$return['date_to'] = $offer->date_to ?? 0;
					break;
				}
		}

		return $return;
	}

	/**
	 * Get all images for the indicated offer
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getImages(int $offer_id)
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
	public function getExcerpt(int $offer_id)
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
	public function getInfrastructure(int $offer_id)
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
	public function getDescriptionLong(int $offer_id)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return null;
		}

		return strip_tags($offer->description_long ?? '');
	}

	/**
	 * Get contact info - not i18n!
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getContact(int $offer_id)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return null;
		}

		return [
			'contact' => nl2br(make_clickable(strip_tags($offer->contact ?? ''))),
			'is_partner' => (bool) $offer->contact_is_park_partner,
		];
	}

	/**
	 * Get park season information
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getSeason(int $offer_id)
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
	public function getBenefits(int $offer_id)
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
	public function getPrice(int $offer_id)
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
	public function getTarget(int $offer_id)
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
	public function getSubscription(int $offer_id)
	{
		global $wpdb;
		$sql_subscription = $wpdb->prepare("SELECT * FROM {$this->tables['subscription']} WHERE offer_id = %s LIMIT 1", $offer_id);
		$results_subscription = $wpdb->get_results($sql_subscription);

		if (empty($results_subscription) || empty(array_filter($results_subscription))) {
			return null;
		}

		$data = $results_subscription[0];

		$data->subscription_details = '';

		$sql_details = $wpdb->prepare("SELECT subscription_details FROM {$this->tables['subscription_i18n']} WHERE offer_id = %s AND language = %s AND subscription_details != '' LIMIT 1", $offer_id, $this->getLanguage());
		$results_details = $wpdb->get_results($sql_details);

		if (!empty($results_details)) {
			$data->subscription_details = $results_details[0]->subscription_details;
		}

		return [
			'contact' => $data->subscription_contact ?? '',
			'details' => $data->subscription_details ?? '',
			'link' => $data->subscription_link ?? false,
			'mandatory' => $data->subscription_mandatory ?? false,
			'enabled' => $data->online_subscription_enabled ?? false,
		];
	}

	public function getTransportStop(int $offer_id, string $start_stop = 'start')
	{
		$transient_key = "shp_gantrisch_adb_offer_startstop_{$offer_id}";
		$results = get_transient($transient_key);

		if (empty($results) || !$this->cache) {
			global $wpdb;
			$sql = $wpdb->prepare("SELECT public_transport_start, public_transport_stop FROM {$this->tables['activity']} WHERE offer_id = %s LIMIT 1", $offer_id);
			$results = $wpdb->get_results($sql, ARRAY_A);
			if (!empty($results)) {
				set_transient($transient_key, $results, $this->transient_lives['single']);
			}
		}

		if (empty($results)) {
			return false;
		}

		return $results[0]["public_transport_{$start_stop}"] ?? '';
	}

	public function getAll($category_ids = [], $keywords = [])
	{

		if (!is_array($category_ids)) {
			$category_ids = (array) $category_ids;
		}

		if (!is_array($keywords)) {
			$keywords = (array) $keywords;
		}

		$transient_cat = md5(implode('', $category_ids));
		$transient_keywords = md5(implode('', $keywords));
		$transient_key = !empty($category_ids) ? "adb_offers_cat_{$transient_cat}_key_{$transient_keywords}" : "adb_offer_all";
		$offers = get_transient($transient_key);

		if (empty($offers) || (bool)($_GET['force'] ?? '') === true) {
			$api = shp_gantrisch_adb_get_instance()->Controller->API->getApi();

			if (!empty($keywords)) {
				$offers = $api->_get_offers(NULL, $category_ids, NULL, NULL, ['keywords' => implode(' ', $keywords)]);
			} else {
				$offers = $api->_get_offers(NULL, $category_ids);
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

		return $offers;
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
	 * Pass by reference. Clean and convert the input to a trimmed array
	 *
	 * @param mixed $keywords
	 * @return array
	 */
	public function prepareKeywords($keywords)
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

		return $keywords;
	}

	public function getTermin(int $offer_id)
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

		$date_from = parks_mysql2date($offer->date_from, TRUE);
		$date_to = parks_mysql2date($offer->date_to, TRUE);

		return parks_show_date([
			'date_from' => parks_mysql2form($date_from),
			'date_to' => parks_mysql2form($date_to)
		]);
	}

	public function getPlace(int $offer_id)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		return $offer->location_details ?? '';
	}

	public function getOpeningTimes(int $offer_id)
	{
		$offer = $this->getOffer($offer_id);

		if (!$offer instanceof stdClass) {
			return '';
		}

		return $offer->opening_hours ?? '';
	}

	public function getTimeRequired(int $offer_id)
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
}
