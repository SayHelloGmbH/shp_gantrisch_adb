<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSameCategory;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

if ($is_preview === true) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder - offers in the same category as the current offer.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>

<?php
	return;
}

$classNameBase = $block['shp']['classNameBase'] ?? '';
$show_filter = false; // Temporary hard-coding
//$show_filter = (bool) get_field('adb_show_filter');

$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

$offer_categories =	$offer_model->getCategories();

if (empty($offer_categories)) {
	return '';
}

// Convert array keys to
$category_ids = array_map(function ($entry) {
	$category_id = str_replace('category', '', $entry);
	return (int) $category_id;
}, array_keys($offer_categories));

// Remove null or empty entries
$category_ids = array_filter($category_ids);

if (empty($category_ids)) {
	return '';
}

// If filter is visible, don't constrain category selection
if ($show_filter) {
	$category_ids = [];
}

$filters = [];

$offers = $offer_model->getAll($category_ids, $keywords);

if (empty($offers)) {
	return '';
}

$offer_controller = shp_gantrisch_adb_get_instance()->Controller->Offer;

// Random order as requested by client
shuffle($offers);

if (count($offers) > 4) {
	$offers = array_slice($offers, 0, 4);
}

// Enqueued here manually so that we can load the script in the footer
// $viewScript = 'src/Blocks/ListDefault/assets/dist/scripts/viewScript.js';
// wp_enqueue_script($classNameBase, shp_gantrisch_adb_get_instance()->url . $viewScript, [], filemtime(shp_gantrisch_adb_get_instance()->path . $viewScript), true);
// wp_localize_script($classNameBase, 'shp_gantrisch_adb_block_list_default', [
// 	'load_more_text' => $load_more_text,
// 	'initial_count' => (int) ($block['data']['initial_count'] ?? false),
// ]);

$api = shp_gantrisch_adb_get_instance()->Controller->API->getApi();

if ($show_filter) {
	wp_enqueue_script("{$classNameBase}_i18n", "https://angebote.paerke.ch/api/lib/api-17/{$api->lang_id}.js", ['jquery'], null, true);
	wp_enqueue_script("{$classNameBase}_jquery", "https://angebote.paerke.ch/api/lib/api-17/jquery.min.js", [], null, false);
	wp_enqueue_script("{$classNameBase}_jquery-ui", "https://angebote.paerke.ch/api/lib/api-17/jquery-ui.min.js", [], null, false);
	wp_enqueue_script("{$classNameBase}_parkapp", "https://angebote.paerke.ch/api/lib/api-17/ParkApp.min.js", ['jquery'], null, true);
	wp_enqueue_style("{$classNameBase}_parkapp-api", "https://angebote.paerke.ch/api/lib/api-17/api.css", [], null);
}

$count = 1;

$categories_info = is_array($category_ids) ? implode(', ', $category_ids) : 'all';

?>
<div class="<?php echo $block['shp']['class_names']; ?> c-adb-list" data-categories="<?php echo $categories_info; ?>">

	<?php if ($show_filter) { ?>
		<div class="<?php echo $classNameBase; ?>__filter">
			<?php
			//$api->show_offers_filter($category_ids, $filters);
			?>
		</div>
	<?php }

	//$api->show_offers_list($category_ids, $filters);
	//$api->show_offers_pagination();
	?>

	<ul class="<?php echo $classNameBase; ?>__entries c-adb-list__entries">
		<?php
		foreach ($offers as $offer) {

			$offer = (array) $offer;

			$button_text = esc_html($block['data']['button_text'] ?? '');

			$images = $offer_model->getImages($offer['offer_id']);
			$selected_size = $block['data']['image_size'] ?? 'small';

			if (!empty($images) && isset($images[0]->{$selected_size}) && filter_var($images[0]->{$selected_size}, FILTER_VALIDATE_URL) !== false) {
				$image_html = sprintf(
					'<figure class="%1$s__entry-figure"><img class="%1$s__entry-image c-adb-list__entry-image" src="%2$s" alt="%3$s" loading="%4$s"></figure>',
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
			<li class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> c-adb-list__entry">

				<div class="<?php echo $classNameBase; ?>__entry-header c-adb-list__entry-header">
					<div class="<?php echo $classNameBase; ?>__entry-title c-adb-list__entry-title">
						<a href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
					</div>

					<?php if (!empty($offer['institution_location'])) { ?>
						<div class="<?php echo $classNameBase; ?>__entry-location c-adb-list__entry-location">
							<p><?php echo esc_html($offer['institution_location']); ?></p>
						</div>
					<?php
					}
					?>
				</div>

				<?php echo $image_html; ?>

				<?php if (!empty($button_text)) { ?>
					<div class="<?php echo $classNameBase; ?>__entry-buttonwrapper c-adb-list__entry-buttonwrapper">
						<a class="<?php echo $classNameBase; ?>__entry-button c-adb-list__entry-button" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo $button_text; ?></a>
					</div>
				<?php } ?>

				<a class="<?php echo $classNameBase; ?>__entry-floodlink c-adb-list__entry-floodlink" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
			</li>
		<?php
			$count++;
		}
		?>
	</ul>
</div>
