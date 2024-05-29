<?php

namespace SayHello\ShpGantrischAdb\Plugin;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

class Yoast
{

	private $offer_model = null;

	public function __construct()
	{
		$this->offer_model = new OfferModel();
	}
	public function run()
	{
		add_action('wpseo_title', [$this, 'seoTitle']);
		add_action('wpseo_opengraph_title', [$this, 'seoTitle']);
		add_action('wpseo_opengraph_desc', [$this, 'seoDescription']);
		add_action('wpseo_opengraph_url', [$this, 'seoUrl']);
		add_action('wpseo_opengraph_show_publish_date', '__return_false');
		add_filter('wpseo_json_ld_output', '__return_false');
		//add_filter('wpseo_schema_graph', [$this, 'schemaGraph'], 11);
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

		$offer_title = $this->offer_model->getTitle();

		if (!$offer_title || $offer_title === $seo_title) {
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

		$offer_excerpt = $this->offer_model->getExcerpt();

		if (!$offer_excerpt || !is_string($offer_excerpt)) {
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
	public function seoUrl($seo_url = '')
	{

		if (!function_exists('YoastSEO')) {
			return $seo_url;
		}

		$offer_id = $this->offer_model->getRequestedOfferID();

		if (!$offer_id) {
			return $seo_url;
		}

		$rewrite_package = shp_gantrisch_adb_get_instance()->Package->Rewrites;
		$var_key = $rewrite_package->getVarKey();

		$permalink = get_permalink();

		return "{$permalink}{$var_key}/{$offer_id}/";
	}

	/**
	 * https://developer.yoast.com/features/schema/api/
	 * Works, but complexity unknown, so deactivated using wpseo_json_ld_output
	 *
	 * @param [type] $pieces
	 * @param [type] $context
	 * @return void
	 */
	// public function schemaGraph(array $data)
	// {

	// 	$seo_url = $this->seoUrl();

	// 	if (!empty($seo_url)) {
	// 		$data[0]['@id'] = $seo_url;
	// 		$data[0]['url'] = $seo_url;
	// 		$data[0]['breadcrumb']['@id'] = str_replace(get_permalink(), $seo_url, $data[0]['breadcrumb']['@id']);

	// 		if (empty($data[0]['potentialAction'] ?? [])) {
	// 			return $data;
	// 		}

	// 		foreach (array_keys($data[0]['potentialAction']) as $key) {
	// 			$data[0]['potentialAction'][$key]['target'][0] = str_replace(get_permalink(), $seo_url, $data[0]['potentialAction'][$key]['target'][0]);
	// 		}
	// 	}

	// 	return $data;
	// }
}
