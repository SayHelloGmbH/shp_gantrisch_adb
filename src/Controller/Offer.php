<?php

namespace SayHello\ShpGantrischAdb\Controller;

use SayHello\ShpGantrischAdb\Package\Rewrites as RewritesPackage;

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
		add_action('the_title', [$this, 'offerTitle'], 10, 2);
		add_filter('get_canonical_url', [$this, 'canonicalURL'], 10, 2);
		add_filter('wpseo_canonical', [$this, 'canonicalURL'], 10, 2);
		add_filter('get_shortlink', [$this, 'shortlink']);
	}

	public function queryVarName()
	{
		return $this->query_var;
	}

	public function isConfiguredSinglePage()
	{
		$single_page_id = shp_gantrisch_adb_get_instance()->Model->Offer->getSinglePageID();
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

		$offer_id = shp_gantrisch_adb_get_instance()->Model->Offer->getRequestedOfferID();

		if (!$this->isConfiguredSinglePage() && $offer_id) {
			header("HTTP/1.1 404 Not Found");
			exit;
		}

		// Has an ID been passed in?
		if ($this->isConfiguredSinglePage() && !$offer_id) {
			header("HTTP/1.1 404 Not Found");
			exit;
		}

		if ($this->isConfiguredSinglePage() && $offer_id) {
			// Is there a valid offer for the ID which has been passed in?
			$offer = shp_gantrisch_adb_get_instance()->Model->Offer->getOffer($offer_id);
			if (!$offer) {
				header("HTTP/1.1 404 Not Found");
				exit;
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

		if (!$this->isConfiguredSinglePage()) {
			return $post_title;
		}

		$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

		if ($post_id !== $offer_model->getSinglePageID()) {
			return $post_title;
		}

		$offer_id = $offer_model->getRequestedOfferID();

		if (!$offer_id) {
			return $post_title;
		}

		$offer_title = $offer_model->getTitle($offer_id);

		if (empty($offer_title) || is_wp_error($offer_title)) {
			return $post_title;
		}

		return $offer_title;
	}

	public function singleUrl($offer_id)
	{

		if (is_array($offer_id)) {
			$offer_id = $offer_id['offer_id'] ?? null;
		}

		$single_page = shp_gantrisch_adb_get_instance()->Model->Offer->getSinglePageID();

		if (!$single_page) {
			return '#';
		}

		$rewrites_package = new RewritesPackage();
		$rewrite_key = $rewrites_package->getVarKey();
		$permalink = get_permalink($single_page);

		if (!$permalink || empty($permalink)) {
			return '#';
		}

		return sprintf(
			'%s%s/%s/',
			$permalink,
			$rewrite_key,
			$offer_id
		);
	}

	public function canonicalURL($canonical_url)
	{

		$single_page = shp_gantrisch_adb_get_instance()->Model->Offer->getSinglePageID();

		if (!$single_page || get_the_ID() !== $single_page) {
			return $canonical_url;
		}

		$rewrites_package = new RewritesPackage();
		$rewrite_key = $rewrites_package->getVarKey();

		if (!$rewrite_key) {
			return $canonical_url;
		}

		$offer_id = shp_gantrisch_adb_get_instance()->Model->Offer->getRequestedOfferID();

		if (!$offer_id) {
			return $canonical_url;
		}

		return "{$canonical_url}{$rewrite_key}/{$offer_id}/";
	}

	public function shortlink($shortlink)
	{
		$single_page = shp_gantrisch_adb_get_instance()->Model->Offer->getSinglePageID();

		if (!$single_page) {
			return $shortlink;
		}

		return '';
	}
}
