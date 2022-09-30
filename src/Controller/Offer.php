<?php

namespace SayHello\ShpGantrischAdb\Controller;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

/**
 * Handles general request controlling for
 * the Angebotsdatenbank
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Offer
{

	private $query_var = 'adb_offer_id';
	private $model = null;

	public function run()
	{
		add_action('template_redirect', [$this, 'handleInvalidSingle']);
	}

	public function queryVarName()
	{
		return $this->query_var;
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

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$offer_id = $this->model->requestedOfferID();

		if (!$this->isConfiguredSinglePage() && $offer_id) {
			header("HTTP/1.1 404 Not Found");
			return;
		}

		// Has an ID been passed in?
		if ($this->isConfiguredSinglePage() && !$offer_id) {
			header("HTTP/1.1 404 Not Found");
			return;
		}

		if ($this->isConfiguredSinglePage() && $offer_id) {
			// Is there a valid offer for the ID which has been passed in?
			$offer = $this->model->getOffer((int) $offer_id);
			if (!$offer) {
				header("HTTP/1.1 404 Not Found");
			}
		}
	}
}
