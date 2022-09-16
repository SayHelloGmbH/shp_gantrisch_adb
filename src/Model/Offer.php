<?php

namespace SayHello\ShpGantrischAdb\Model;

class Offer
{
	private $locale = 'de_CH';
	private $language = 'de';

	private $supported_languages = [
		'de', 'fr', 'it', 'en'
	];

	private $tables = [
		'offer' => 'offer',
		'offer_date' => 'offer_date',
		'offer_i18n' => 'offer_i18n',
	];

	private $date_format = 'Y/m/d';

	public function __construct()
	{
		$this->date_format = get_option('date_format');
		$this->locale = get_locale();

		$lang_sub = substr($this->locale, 0, 2);
		if (in_array($lang_sub, $this->supported_languages)) {
			$this->language = $lang_sub;
		}
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
		global $wpdb;
		$sql = $wpdb->prepare("SELECT offer.*,i18n.* FROM {$this->tables['offer']} offer, {$this->tables['offer_i18n']} i18n WHERE offer.offer_id = %s and offer.offer_id = i18n.offer_id and i18n.language = %s", $offer_id, $this->language);

		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return [];
		}

		unset($results[0]->park);
		unset($results[0]->park_id);
		unset($results[0]->route_url);

		return $results[0];
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
}
