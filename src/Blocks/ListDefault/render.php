<?php

/**
 * ADB List Default block render template
 * ACF block, so following data available
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use SayHello\ShpGantrischAdb\Controller\API as APIController;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$classNameBase = $block['shp']['classNameBase'] ?? '';
$show_filter = (bool) get_field('adb_show_filter');

if ((bool) (get_field('shp_adb_filterfunction_deactivate', 'options') ?? false) === true) {
	$show_filter = false;
}

$offer_model = new OfferModel;

$category_ids = $block['data']['adb_categories'] ?? [];

$filters = [];

$keywords = $block['data']['adb_keywords'] ?? '';

if (!empty($keywords)) {
	$keywords = $offer_model->prepareKeywords($keywords);
	$filters['keywords'] = $keywords;
}

if ($is_preview === true) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB List Block.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>

<?php
	return;
}

$offer_controller = new OfferController();

$load_more_text = $block['data']['load_more_text'] ?? '';
if (empty($load_more_text)) {
	$load_more_text = _x('Load more', 'List block default button text', 'shp_gantrisch_adb');
}

// Enqueued here manually so that we can load the script in the footer
$viewScript = 'src/Blocks/ListDefault/assets/dist/scripts/viewScript.js';
wp_enqueue_script($classNameBase, shp_gantrisch_adb_get_instance()->url . $viewScript, [], filemtime(shp_gantrisch_adb_get_instance()->path . $viewScript), true);
wp_localize_script($classNameBase, 'shp_gantrisch_adb_block_list_default', [
	'load_more_text' => $load_more_text,
	'initial_count' => (int) ($block['data']['initial_count'] ?? false),
]);

$api_controller = new APIController();
$api = $api_controller->getApi();

if ($show_filter) {
	wp_enqueue_script("{$classNameBase}_i18n", "https://angebote.paerke.ch/api/lib/api-17/{$api->lang_id}.js", ['jquery'], null, true);
	wp_enqueue_script("{$classNameBase}_jquery", "https://angebote.paerke.ch/api/lib/api-17/jquery.min.js", [], null, false);
	wp_enqueue_script("{$classNameBase}_jquery-ui", "https://angebote.paerke.ch/api/lib/api-17/jquery-ui.min.js", [], null, false);
	wp_enqueue_script("{$classNameBase}_parkapp", "https://angebote.paerke.ch/api/lib/api-17/ParkApp.min.js", ['jquery'], null, true);
	wp_enqueue_style("{$classNameBase}_parkapp-api", "https://angebote.paerke.ch/api/lib/api-17/api.css", [], null);
}

$count = 1;

$categories_info = is_array($category_ids) ? implode(', ', $category_ids) : 'all';

if (!is_array($category_ids)) {
	if (empty($category_ids)) {
		$category_ids = [];
	} else {
		$category_ids = [$category_ids];
	}
}

?>
<div id="<?php echo $block['id'] ?? ''; ?>" class="c-adb-block c-adb-block--list <?php echo $block['shp']['class_names']; ?>  c-adb-list" data-categories="<?php echo $categories_info; ?>" data-button-text="<?php echo esc_html($block['data']['button_text'] ?? ''); ?>" data-class-name-base="<?php echo esc_html($classNameBase); ?>">

	<?php if ($show_filter) { ?>
		<div class="<?php echo $classNameBase; ?>__filter c-adb-list__filter">
			<?php
			$api->show_offers_filter($category_ids, $filters);
			?>
		</div>
	<?php }

	$api->show_offers_map($category_ids, $filters);

	$shown_termine = [];

	$api->show_offers_list($category_ids, $filters);

	?>
</div>