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

		// All blocks. Block type check is in the method.
		add_action('render_block', [$this, 'modifyHTML'], 10, 3);

		// Only the main list
		add_action('render_block_acf/shp-adb-list-default', [$this, 'sortEntries'], 20);

		// All blocks. Block type check is in the method.
		add_action('render_block', [$this, 'contentOrder'], 30, 2);
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
	 * Various HTML modifications on the list entries.
	 *
	 * @param string $html
	 * @param array $block
	 * @param WP_Block $instance
	 * @return string
	 */
	public function modifyHTML($html, $block, $instance)
	{

		if (empty($html)) {
			return $html;
		}

		if ($block['blockName'] !== 'acf/shp-adb-list-default' && $block['blockName'] !== 'shp/adb-offer-same-category') {
			return $html;
		}

		libxml_use_internal_errors(true);
		$document = new DOMDocument();
		$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

		$xpath = new DOMXPath($document);
		$entries = $xpath->query("//*[contains(concat(' ',normalize-space(@class),' '),'listing_entry') or contains(concat(' ',normalize-space(@class),' '),'c-adb-list__entry')][not(contains(concat(' ',normalize-space(@class),' '),'c-adb-list__entry-'))]");

		if (!$entries instanceof DOMNodeList || $entries->length === 0) {
			return $html;
		}

		// Make sure our own class name is set on the entry
		foreach ($entries as $entry) {
			$class_names = explode(' ', $entry->getAttribute('class'));

			if (in_array('c-adb-list__entry', $class_names)) {
				continue;
			}

			// remove empty class names
			$class_names = array_filter($class_names);

			$entry->setAttribute('class', implode(' ', array_merge(['c-adb-list__entry'], $class_names)));
		}

		$controller = new OfferController();
		$model = new OfferModel();

		// get default css class name from $block['blockName']
		$classNameBase = wp_get_block_default_classname($block['blockName']);

		$entries_wrap = $xpath->query(".//*[contains(concat(' ',normalize-space(@class),' '),'entries_wrap')]");
		if ($entries_wrap->length) {
			foreach ($entries_wrap as $entries_wrap_entry) {
				$class_names = explode(' ', $entries_wrap_entry->getAttribute('class'));

				if (in_array('c-adb-list__entries', $class_names)) {
					continue;
				}

				// Remove empty entries
				$class_names = array_filter($class_names);

				$entries_wrap_entry->setAttribute('class', implode(' ', array_merge(['c-adb-list__entries'], $class_names)));
			}
		}

		$entries = $xpath->query("//*[contains(concat(' ',normalize-space(@class),' '),'c-adb-list__entry')]");

		foreach ($entries as $entry) {

			// get id attribute from $entry: remove offer_ prefix
			$offer_id = (int) substr($entry->getAttribute('id'), 6);
			if (!$offer_id) {
				continue;
			}

			$offer = $model->getOffer($offer_id);

			if (!$offer) {
				continue;
			}

			$url = $controller->singleUrl($offer_id);

			$button_link = $xpath->query(".//*[contains(concat(' ',normalize-space(@class),' '),'c-adb-list__entry-button')]", $entry);

			if (!$button_link->length) {
				$link = $document->createElement('a');
				$link->textContent = $block['attrs']['data']['button_text'] ?? $instance->attributes['button_text'] ?? __('Mehr', 'shp-gantrisch-adb');
				$link->setAttribute('class', "{$classNameBase}__entry-button c-adb-list__entry-button");
				$link->setAttribute('href', $url);

				$wrapper = $document->createElement('div');
				$wrapper->setAttribute('class', "{$classNameBase}__entry-buttonwrapper c-adb-list__entry-buttonwrapper");

				$wrapper->appendChild($link);

				$entry->appendChild($wrapper);
			}

			// get all descendants with an href attribute
			$links = $xpath->query('.//@href', $entry);
			foreach ($links as $link) {
				// replace href attribute with url to single offer page
				$link->nodeValue = $url;
			}

			$is_partner = $offer->institution_is_park_partner ?? false || $offer->contact_is_park_partner ?? false || $offer->is_park_partner_event ?? false || $offer->is_park_partner ?? false;
			$is_park_event = (bool) $offer->is_park_event ?? false;
			$is_tip = (bool) ($offer->is_hint ?? false);

			// Remove all pre-existing labels
			$tip_tags = $xpath->query(".//*[contains(concat(' ',normalize-space(@class),' '),'tipp')]", $entry);

			if ($tip_tags->length) {
				foreach ($tip_tags as $tip_tag) {
					$tip_tag->parentNode->removeChild($tip_tag);
				}
			}

			if ($is_partner || $is_park_event || $is_tip) {
				$postit_wrapper = $document->createElement('div');
				$postit_wrapper->setAttribute('class', "{$classNameBase}__entry-postit-wrapper c-adb-list__entry-postit-wrapper");
				$entry->insertBefore($postit_wrapper, $entry->firstChild);

				if ($is_tip) {
					$entry->setAttribute('data-hint', 'true');
					$tip_label = $document->createElement('div');
					$tip_label->setAttribute('class', "{$classNameBase}__entry-tiplabel c-adb-list__entry-tiplabel c-adb-list__entry-postit c-adb-list__entry-postit--tipp");
					$tip_label->nodeValue = _x('Tipp', 'Tip label', 'shp_gantrisch_adb');
					$postit_wrapper->appendChild($tip_label);
				}

				if ($is_park_event) {
					$entry->setAttribute('data-parkevent', 'true');
					$event_label = $document->createElement('div');
					$event_label->setAttribute('class', "{$classNameBase}__entry-parkeventlabel c-adb-list__entry-parkeventlabel c-adb-list__entry-postit c-adb-list__entry-postit--parkevent");
					$event_label->nodeValue = _x('Naturpark', 'Park event label', 'shp_gantrisch_adb');
					$postit_wrapper->appendChild($event_label);
				}

				if ($is_partner) {
					$entry->setAttribute('data-parkpartner', 'true');
					$partner_label = $document->createElement('div');
					$partner_label->setAttribute('class', "{$classNameBase}__entry-partnerlabel c-adb-list__entry-partnerlabel c-adb-list__entry-postit c-adb-list__entry-postit--parkpartner");
					$partner_label->nodeValue = _x('Parkpartner', 'More offers label', 'shp_gantrisch_adb');
					$postit_wrapper->appendChild($partner_label);
				}
			}
		}

		$body = $document->saveHtml($document->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}

	/**
	 * Modify the order of the list entries.
	 *
	 * @param string $html
	 * @return string
	 */
	public function sortEntries($html)
	{

		if (empty($html)) {
			return $html;
		}

		libxml_use_internal_errors(true);
		$document = new DOMDocument();
		$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

		$xpath = new DOMXPath($document);
		$entries = $xpath->query("//*[contains(concat(' ',normalize-space(@class),' '),' c-adb-list__entry ')]");

		if (!$entries->length) {
			return $html;
		}

		$entries_parent = $entries->item(0)->parentNode;

		$model = new OfferModel();
		$entries_sorted = $model->sortOfferDomNodes($entries);

		// Remove existing entries
		foreach ($entries as $entry) {
			$entry->parentNode->removeChild($entry);
		}

		// Add back sorted entries
		foreach ($entries_sorted as $entry_sorted) {
			$entries_parent->appendChild($entry_sorted);
		}


		$body = $document->saveHtml($document->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}

	/**
	 * Modify the order of the list entry content.
	 *
	 * @param string $html
	 * @return string
	 */
	public function contentOrder($html, $block)
	{

		if (empty($html)) {
			return $html;
		}

		if ($block['blockName'] !== 'acf/shp-adb-list-default' && $block['blockName'] !== 'shp/adb-offer-same-category') {
			return $html;
		}

		libxml_use_internal_errors(true);
		$document = new DOMDocument();
		$document->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

		$xpath = new DOMXPath($document);
		$entries = $xpath->query("//*[contains(concat(' ',normalize-space(@class),' '),'c-adb-list__entry')]");

		foreach ($entries as $entry) {
			$pictures = $xpath->query(".//*[contains(concat(' ',normalize-space(@class),' '),'pictures')]", $entry);
			$description = $xpath->query(".//*[contains(concat(' ',normalize-space(@class),' '),'description')]", $entry);

			// Move pictures after description
			if ($pictures->length && $description->length) {
				$description->item(0)->parentNode->insertBefore($pictures->item(0), $description->item(0)->nextSibling);
			}
		}

		$body = $document->saveHtml($document->getElementsByTagName('body')->item(0));
		return str_replace(['<body>', '</body>'], '', $body);
	}
}
