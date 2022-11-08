<?php

/**
 * ADB List Default block render template
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

// Convert to stdClass
$block_controller = new BlockController();
$block_controller->extend($block);

$classNameBase = $block['shp']['classNameBase'] ?? '';
$show_filter = (bool) get_field('adb_show_filter');

$offer_model = new OfferModel();

$category_ids = $block['data']['adb_categories'] ?? [];

// If filter is visible, don't constrain category selection
if ($show_filter) {
	$category_ids = [];
}

$filters = [];

$keywords = $block['data']['adb_keywords'] ?? '';

if (!empty($keywords) && !$show_filter) {
	$keywords = $offer_model->prepareKeywords($keywords);
	$filters['keywords'] = $keywords;
}

if ($is_preview === true) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for List Block.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>

<?php
	return;
}

$offers = $offer_model->getAll($category_ids, $keywords);

if (empty($offers)) {
	return '';
}

$offer_controller = new OfferController();

// Random order as requested by client
shuffle($offers);

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

if ((bool) get_field('adb_show_filter')) {
	$api = shp_gantrisch_adb_get_instance()->Controller->API->getApi();
	wp_enqueue_script("{$classNameBase}_i18n", "https://angebote.paerke.ch/api/lib/api-17/{$api->lang_id}.js", [], null, true);
	wp_enqueue_script("{$classNameBase}_jquery", "https://angebote.paerke.ch/api/lib/api-17/jquery.min.js", [], null, true);
	wp_enqueue_script("{$classNameBase}_jquery-ui", "https://angebote.paerke.ch/api/lib/api-17/jquery-ui.min.js", [], null, true);
	wp_enqueue_script("{$classNameBase}_parkapp", "https://angebote.paerke.ch/api/lib/api-17/ParkApp.min.js", [], null, true);
	wp_enqueue_style("{$classNameBase}_parkapp-api", "https://angebote.paerke.ch/api/lib/api-17/api.css", [], null);
}

$count = 1;

$categories_info = is_array($category_ids) ? implode(', ', $category_ids) : 'all';

?>
<div class="<?php echo $block['shp']['class_names']; ?>" data-categories="<?php echo $categories_info; ?>">

	<?php if ((bool) get_field('adb_show_filter')) { ?>
		<div class="<?php echo $classNameBase; ?>__filter">
			<?php
			//$api->show_offers_filter($category_ids, $filter);
			$api->show_offers_filter([], $filter);
			?>
		</div>
	<?php }

	$api->show_offers_list($categories, $filter);
	$api->show_offers_pagination();


	if (false) {

	?>


		<ul class="<?php echo $classNameBase; ?>__entries">
			<?php
			foreach ($offers as $offer) {

				$offer = (array) $offer;

				$button_text = esc_html($block['data']['button_text'] ?? '');

				$images = $offer_model->getImages($offer['offer_id']);
				$selected_size = $block['data']['image_size'] ?? 'small';

				if (!empty($images) && isset($images[0]->{$selected_size}) && filter_var($images[0]->{$selected_size}, FILTER_VALIDATE_URL) !== false) {
					$image_html = sprintf(
						'<figure class="%1$s__entry-figure"><img class="%1$s__entry-image" src="%2$s" alt="%3$s" loading="%4$s"></figure>',
						$classNameBase,
						$images[0]->{$selected_size},
						esc_html($offer['title']),
						$count > (int) ($block['data']['initial_count'] ?? false) ? 'lazy' : 'eager'
					);
				} else {
					$image_html = sprintf(
						'<div class="%1$s__entry-figure %1$s__entry-figure--empty"></div>',
						$classNameBase
					);
				}
			?>
				<li class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> is--hidden">

					<div class="<?php echo $classNameBase; ?>__entry-header">
						<div class="<?php echo $classNameBase; ?>__entry-title">
							<a href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
						</div>

						<?php if (!empty($offer['location_details'])) { ?>
							<div class="<?php echo $classNameBase; ?>__entry-location">
								<p><?php echo esc_html($offer['location_details']); ?></p>
							</div>
						<?php
						}
						?>
					</div>

					<?php echo $image_html; ?>

					<?php if (!empty($button_text)) { ?>
						<div class="<?php echo $classNameBase; ?>__entry-buttonwrapper">
							<a class="<?php echo $classNameBase; ?>__entry-button" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo $button_text; ?></a>
						</div>
					<?php } ?>

					<a class="<?php echo $classNameBase; ?>__entry-floodlink" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
				</li>
			<?php
				$count++;
			}
			?>
		</ul>

	<?php } ?>
</div>
