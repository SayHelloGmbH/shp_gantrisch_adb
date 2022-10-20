<?php

namespace SayHello\ShpGantrischAdb\Model;

use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use DateTime;
use WP_Error;

class Offer
{
	private $locale = 'de_CH';
	private $language = 'de';
	private $cache = true;
	private $single_page = false;

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
	private $transient_lives = ['single' => 10];

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
	 * @param integer $offer_id
	 * @return mixed The database result.
	 */
	public function getOffer(int $offer_id)
	{

		$transient_key = "shp_gantrisch_adb_offer_{$offer_id}";
		$cached_result = get_transient($transient_key);

		if (empty($cached_result) || !$this->cache) {
			global $wpdb;
			$sql = $wpdb->prepare("SELECT offer.*,i18n.* FROM {$this->tables['offer']} offer, {$this->tables['offer_i18n']} i18n WHERE offer.offer_id = %s and offer.offer_id = i18n.offer_id and i18n.language = %s", $offer_id, $this->getLanguage());

			$results = $wpdb->get_results($sql);

			if (empty($results)) {
				return null;
			}

			$result = $results[0];
			if (!empty($result)) {
				set_transient($transient_key, $result, $this->transient_lives['single']);
			}
		}

		if (empty($result)) {
			return null;
		}

		unset($result->park);
		unset($result->park_id);
		unset($result->route_url);

		return $result;
	}

	public function getOfferTitle(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return new WP_Error(404, _x('There is no localised title available for this entry', 'Fallback title', 'shp_gantrisch_adb'));
		}

		return strip_tags($data->title);
	}

	public function getOfferKeywords(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return null;
		}

		$data->keywords = explode(PHP_EOL, $data->keywords);
		return array_map('strip_tags', $data->keywords);
	}

	public function getOfferCategories(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT i18n.category_id, i18n.body, c.parent_id, cl.offer_id, c.sort FROM {$this->tables['category_link']} cl, {$this->tables['category_i18n']} i18n, {$this->tables['category']} c WHERE cl.offer_id = %s AND cl.category_id = i18n.category_id AND cl.category_id = c.category_id AND i18n.language = %s ORDER BY c.sort", $offer_id, $this->getLanguage());
		$results = $wpdb->get_results($sql, ARRAY_A);

		if (empty($results)) {
			return null;
		}

		$categories = [];

		foreach ($results as $result) {
			if (!array_key_exists("category{$result['category_id']}", $categories)) {
				$categories["category{$result['category_id']}"] = $result;
				$categories["category{$result['category_id']}"]['categories'] = [];
			}
		}


		foreach ($results as $result) {
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
	public function getOfferDates(int $offer_id, $format = 'raw')
	{

		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM {$this->tables['offer_date']} WHERE offer_id = %s", $offer_id);
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return null;
		}

		foreach ($results as &$result) {
			unset($result->offer_date_id);

			// Default format is raw, which will return the
			// unmanipulated value from the database table
			switch ($format) {
				case 'legible': {
						$result->date_from = wp_date($this->date_format, strtotime($result->date_from));
						$result->date_to = wp_date($this->date_format, strtotime($result->date_to));
						break;
					}
				case 'integer': {
						$result->date_from = strtotime($result->date_from);
						$result->date_to = strtotime($result->date_to);
						break;
					}
			}
		}

		return $results;
	}

	/**
	 * Get all images for the indicated offer
	 *
	 * @param integer $offer_id
	 * @return array
	 */
	public function getOfferImages(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT i.offer_id, i.small, i.medium, i.large, i.original, i.copyright FROM {$this->tables['offer']} o, {$this->tables['offer_image']} i WHERE o.offer_id = %s AND o.offer_id = i.offer_id", $offer_id);
		$results = $wpdb->get_results($sql);
		return $results;
	}

	/**
	 * Get i18n excerpt (description_medium)
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getOfferExcerpt(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return null;
		}

		return strip_tags($data->description_medium);
	}

	/**
	 * Get infrastructure
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getInfrastructure(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return null;
		}

		return strip_tags($data->other_infrastructure ?? '');
	}

	/**
	 * Get long description
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getOfferDescriptionLong(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return null;
		}

		return strip_tags($data->description_long);
	}

	/**
	 * Get contact info - not i18n!
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getContact(int $offer_id)
	{
		$data = $this->getOffer($offer_id);

		if (empty($data)) {
			return null;
		}

		return [
			'contact' => nl2br(make_clickable(strip_tags($data->contact))),
			'is_partner' => (bool) $data->contact_is_park_partner,
		];
	}

	/**
	 * Get park season information
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getOfferSeason(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer_id, season_months FROM {$this->tables['booking']} WHERE offer_id = %s LIMIT 1", $offer_id);
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return '';
		}

		$season_months = $results[0]->season_months ?? '';
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
	public function getOfferBenefits(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer_id, benefits FROM {$this->tables['offer_i18n']} WHERE offer_id = %s and language = %s LIMIT 1", $offer_id, $this->getLanguage());
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return '';
		}

		return $results[0]->benefits ?? '';
	}

	/**
	 * Get offer price
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getOfferPrice(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer_id, price FROM {$this->tables['offer_i18n']} WHERE offer_id = %s and language = %s LIMIT 1", $offer_id, $this->getLanguage());
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return '';
		}

		return $results[0]->price ?? '';
	}

	/**
	 * Get offer target audience
	 *
	 * @param integer $offer_id
	 * @return string
	 */
	public function getOfferTarget(int $offer_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT l.offer_id, i.body FROM {$this->tables['target_group_link']} l, {$this->tables['target_group_i18n']} i, {$this->tables['target_group']} g WHERE l.offer_id = %s AND l.target_group_id = g.target_group_id AND l.target_group_id = i.target_group_id AND i.body != '' AND i.language = %s ORDER BY g.sort ASC", $offer_id, $this->getLanguage());
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return '';
		}

		$return = [];

		foreach ($results as $result) {
			$return[] = $result->body;
		}

		return implode(chr(10), $return);
	}

	/**
	 * Get offer subscription information
	 *
	 * @param integer $offer_id
	 * @return mixed
	 */
	public function getOfferSubscription(int $offer_id)
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

	public function getOfferTransportStop(int $offer_id, string $start_stop = 'start')
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

	public function getAll(bool $shuffle = false)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer.*,i18n.* FROM {$this->tables['offer']} offer, {$this->tables['offer_i18n']} i18n WHERE offer.offer_id = i18n.offer_id and i18n.language = %s", $this->getLanguage());
		$offers = @$wpdb->get_results($sql, ARRAY_A);

		if (!is_array($offers)) {
			return new WP_Error(500, "Database error when requesting all offers.");
		}

		if (empty($offers)) {
			return [];
		}

		$this->extendOffersData($offers);

		// Optionally shuffle array entries
		if ($shuffle) {
			shuffle($offers);
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
	public function getByCategory(int $category_id, bool $shuffle = false)
	{
		if (!$category_id) {
			return [];
		}

		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer.*,i18n.* FROM {$this->tables['offer']} offer, {$this->tables['offer_i18n']} i18n WHERE offer.offer_id = i18n.offer_id and i18n.language = %s", $this->getLanguage());
		$offers = @$wpdb->get_results($sql, ARRAY_A);

		if (!is_array($offers)) {
			return new WP_Error(500, "Database error when requesting all offers.");
		}

		if (empty($offers)) {
			return [];
		}

		// Optionally shuffle array entries
		if ($shuffle) {
			shuffle($offers);
		}

		$this->extendOffersData($offers);

		return $offers;
	}

	/**
	 * Extend data from database with custom stuff
	 * Offers array passed by reference, no return needed.
	 *
	 * @param array $offers
	 * @return void
	 */
	private function extendOffersData(&$offers)
	{
		foreach ($offers as &$offer) {
			$this->extendOfferData($offer);
		}
	}

	/**
	 * Extend data from database with custom stuff
	 * Offer passed by reference, no return needed.
	 *
	 * @param array $offer
	 * @return void
	 */
	private function extendOfferData(&$offer)
	{
		if (is_array($offer) && isset($offer['offer_id']) && !isset($offer['id'])) {
			$offer['id'] = $offer['offer_id'];
		}
	}
}
