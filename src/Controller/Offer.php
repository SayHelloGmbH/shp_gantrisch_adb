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
		add_action('the_title', [$this, 'offerTitle'], 10, 2);
	}

	public function queryVarName()
	{
		return $this->query_var;
	}

	public function isConfiguredSinglePage()
	{
		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$single_page_id = $this->model->getSinglePageID();
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

	/**
	 * The post title for e.g. page display or menu display.
	 *
	 * @param string $post_title
	 * @param int $post_id
	 * @return string
	 */
	public function offerTitle($post_title, $post_id)
	{

		if (is_admin()) {
			return $post_title;
		}

		if (!$this->controller) {
			$this->controller = new OfferController();
		}

		if (!$this->controller->isConfiguredSinglePage()) {
			return $post_title;
		}

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		if ($post_id !== $this->model->getSinglePageID()) {
			return $post_title;
		}

		$offer_id = $this->model->requestedOfferID();

		if (!$offer_id) {
			return $post_title;
		}

		$offer_title = $this->model->getOfferTitle($offer_id);

		if (empty($offer_title) || is_wp_error($offer_title)) {
			return $post_title;
		}

		return $offer_title;
	}
}
