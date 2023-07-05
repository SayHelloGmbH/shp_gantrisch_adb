<?php

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use DOMDocument;
use DOMNodeList;
use DOMXPath;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);
		add_action('acf/init', [$this, 'registerFields']);
		add_action('render_block_acf/shp-adb-list-default', [$this, 'renderBlock'], 10, 2);
	}

	public function register()
	{
		register_block_type(__DIR__);
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :

			acf_add_local_field_group(array(
				'key' => 'group_6357d9b8a7fc5',
				'title' => 'ACF Block: ADB List',
				'fields' => array(
					array(
						'key' => 'field_6357d9b9fc60d',
						'label' => 'Button Text',
						'name' => 'button_text',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Entdecken',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_6357fad3546d8',
						'label' => 'Load more text',
						'name' => 'load_more_text',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Mehr Angebote laden',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_6357fa9d418ff',
						'label' => 'Initial number of entries',
						'name' => 'initial_count',
						'aria-label' => '',
						'type' => 'number',
						'instructions' => 'How many entries should be shown before the "load more” button?',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 12,
						'min' => 1,
						'max' => 120,
						'placeholder' => '',
						'step' => '',
						'prepend' => '',
						'append' => '',
					),
					[
						'key' => 'field_636a3e8d88e00',
						'label' => _x('Show filter', 'ACF field label', 'shp_gantrisch_adb'),
						'name' => 'adb_show_filter',
						'type' => 'true_false',
						'instructions' => _x('Show or hide the list filter function for site visitors.', 'ACF field label', 'shp_gantrisch_adb'),
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => _x('Show', 'ACF field label', 'shp_gantrisch_adb'),
						'ui_off_text' => _x('Hide', 'ACF field label', 'shp_gantrisch_adb'),
					],
					[
						'key' => 'field_635fe86408687',
						'label' => _x('Keywords', 'ACF field label', 'shp_gantrisch_adb'),
						'name' => 'adb_keywords',
						'type' => 'textarea',
						'instructions' => _x('Individual words (separated by spaces) will be regarded as individual keywords. A single keyword containing spaces is not supported. (This is a restriction imposed by the API.) Displays offers with keyword “A” OR keyword “B”, AND the selected category.', 'ACF field label', 'shp_gantrisch_adb'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => 4,
						'new_lines' => '',
					],
					array(
						'key' => 'field_6357fafbea9ab',
						'label' => 'Categories',
						'name' => 'adb_categories',
						'aria-label' => '',
						'type' => 'checkbox',
						'instructions' => 'Filter list by category.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(),
						'default_value' => array(),
						'return_format' => 'value',
						'allow_custom' => 0,
						'layout' => 'vertical',
						'toggle' => 0,
						'save_custom' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'block',
							'operator' => '==',
							'value' => 'acf/shp-adb-list-default',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 0,
			));

		endif;
	}

	/**
	 * Modify the link hrefs of the list entries.
	 *
	 * @param string $html
	 * @return string
	 */
	public function renderBlock($html, $block)
	{

		if (empty($html)) {
			return $html;
		}

		libxml_use_internal_errors(true);
		$document = new DOMDocument();
		$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

		$xpath = new DOMXPath($document);
		$entries = $xpath->query("//article[contains(concat(' ',normalize-space(@class),' '),'listing_entry')]");

		if (!$entries instanceof DOMNodeList || $entries->length === 0) {
			return $html;
		}

		$controller = new OfferController();
		$model = new OfferModel();

		// get default css class name from $block['blockName']
		$classNameBase = wp_get_block_default_classname($block['blockName']);

		foreach ($entries as $entry) {
			// get id attribute from $entry: remove offer_ prefix
			$offer_id = (int) substr($entry->getAttribute('id'), 6);
			if (!$offer_id) {
				continue;
			}

			$url = $controller->singleUrl($offer_id);

			// get all descendants with an href attribute
			$links = $xpath->query('.//@href', $entry);
			foreach ($links as $link) {
				// replace href attribute with url to single offer page
				$link->nodeValue = $url;
			}

			$offer = $model->getOffer($offer_id);

			if (!$offer) {
				continue;
			}

			$is_partner = $offer->institution_is_park_partner ?? false || $offer->contact_is_park_partner || $offer->is_park_partner_event || $offer->is_park_partner;
			$is_hint = (bool) ($offer->is_hint ?? false);

			if ($is_partner) {
				if ($is_partner && !$is_hint) {
					// Hint elements are automatically added by the ADB's own HTML output in the list.
					$hint_element = $document->createElement('div');
					$hint_element->setAttribute('class', "{$classNameBase}__entry-partnerlabel c-adb-list__entry-partnerlabel c-adb-list__entry-postit");
					$hint_element->nodeValue = _x('Parkpartner', 'More offers label', 'shp_gantrisch_adb');
					$entry->setAttribute('data-hint', 'true');
					$entry->insertBefore($hint_element, $entry->firstChild);
				}
			}
		}

		$tip_tags = $xpath->query("//*[contains(concat(' ',normalize-space(@class),' '),'tipp')]");

		if ($tip_tags->length) {
			foreach ($tip_tags as $tip_tag) {
				$tip_tag->setAttribute('class', "{$classNameBase}__entry-hintlabel c-adb-list__entry-hintlabel c-adb-list__entry-postit");
			}
		}

		$body = $document->saveHtml($document->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}
}
