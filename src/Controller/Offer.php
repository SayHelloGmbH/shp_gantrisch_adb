<?php

namespace SayHello\ShpGantrischAdb\Controller;

/**
 * Handles general request controlling for
 * the Angebotsdatenbank
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Offer
{

	private $query_var = 'adb_offer_id';

	public function run()
	{
		add_action('template_redirect', [$this, 'handleInvalidSingle']);
	}

	public function isConfiguredSinglePage()
	{
		$single_page_id = (int) get_field('shp_gantrisch_adb_single_page', 'options');
		return $single_page_id && get_the_ID() && get_the_ID() === $single_page_id;
	}

	/**
	 * Handle requests for an invalid single request
	 * e.g. request with no ID or with an invalid ID
	 *
	 * The query var will only work on the configured
	 * single offer view page
	 *
	 * @return void
	 */
	public function handleInvalidSingle()
	{

		$single_id = preg_replace('/[^0-9]/', '', get_query_var($this->query_var));

		if (!$this->isConfiguredSinglePage() && $single_id) {
			header("HTTP/1.1 404 Not Found");
			return;
		}

		// Has an ID been passed in?
		if ($this->isConfiguredSinglePage() && !$single_id) {
			header("HTTP/1.1 404 Not Found");
			return;
		}

		if ($this->isConfiguredSinglePage() && $single_id) {
			// Is there a valid offer for the ID which has been passed in?
			$model = shp_gantrisch_adb_get_instance()->Model->Offer;
			$offer = $model->getOffer((int) $single_id);
			if (!$offer) {
				header("HTTP/1.1 404 Not Found");
			}
		}
	}
}
