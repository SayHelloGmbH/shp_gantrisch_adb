<?php

namespace SayHello\ShpGantrischAdb\Plugin;

use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use SayHello\ShpGantrischAdb\Package\Rewrites as RewritesPackage;

class Yoast
{
	private $controller = null;
	private $model = null;

	public function run()
	{
		add_action('wpseo_title', [$this, 'seoTitle']);
		add_action('wpseo_opengraph_title', [$this, 'seoTitle']);
		add_action('wpseo_opengraph_desc', [$this, 'seoDescription']);
		add_action('wpseo_opengraph_url', [$this, 'seoUrl']);
	}

	private function getOfferId()
	{
		if (!$this->controller) {
			$this->controller = new OfferController();
		}

		if (!$this->controller->isConfiguredSinglePage()) {
			return null;
		}

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		return (int) $this->model->requestedOfferID();
	}

	/**
	 * The title used by the Yoast SEO plugin
	 *
	 * @param string $seo_title
	 * @return string
	 */
	public function seoTitle($seo_title)
	{

		if (!function_exists('YoastSEO')) {
			return $seo_title;
		}

		$offer_id = $this->getOfferId();

		if (!$offer_id) {
			return $seo_title;
		}

		$offer_title = $this->model->getOfferTitle($offer_id);

		if ($offer_title === $seo_title) {
			return $seo_title;
		}

		$blog_name = get_option('blogname');
		$separator = apply_filters('document_title_separator', '-');

		return "{$offer_title} {$separator} {$blog_name}";
	}

	/**
	 * The description used by the Yoast SEO plugin
	 *
	 * @param string $seo_description
	 * @return string
	 */
	public function seoDescription($seo_description)
	{

		if (!function_exists('YoastSEO')) {
			return $seo_description;
		}

		$offer_id = $this->getOfferId();

		if (!$offer_id) {
			return $seo_description;
		}

		$offer_excerpt = $this->model->getOfferExcerpt((int) $offer_id);

		if (!$offer_excerpt) {
			return $seo_description;
		}

		$offer_excerpt = strip_tags(preg_replace('/' . PHP_EOL . '/', ' - ', $offer_excerpt));

		return $offer_excerpt;
	}

	/**
	 * The URL used by the Yoast SEO plugin
	 *
	 * @param string $seo_url
	 * @return string
	 */
	public function seoUrl($seo_url)
	{

		if (!function_exists('YoastSEO')) {
			return $seo_url;
		}

		$offer_id = $this->getOfferId();

		if (!$offer_id) {
			return $seo_url;
		}

		$rewrite_package = new RewritesPackage();
		$var_key = $rewrite_package->getVarKey();

		$permalink = get_permalink();

		return "{$permalink}{$var_key}/{$offer_id}/";
	}
}
