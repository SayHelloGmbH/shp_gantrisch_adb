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
use SayHello\ShpGantrischAdb\Controller\API as APIController;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$classNameBase = $block['shp']['classNameBase'] ?? '';
$show_filter = (bool) get_field('adb_show_filter');

$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

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
			<p><?php _ex('Placeholder for ADB List Block.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>

<?php
	return;
}

$offers = $offer_model->getAll($category_ids, $keywords);

if (empty($offers) && !$show_filter) {
	return '';
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

?>
<div class="<?php echo $block['shp']['class_names']; ?>  c-adb-list" data-categories="<?php echo $categories_info; ?>" data-button-text="<?php echo esc_html($block['data']['button_text'] ?? ''); ?>" data-class-name-base="<?php echo esc_html($classNameBase); ?>">

	<?php if ($show_filter) { ?>
		<div class="<?php echo $classNameBase; ?>__filter c-adb-list__filter">
			<?php
			$api->show_offers_filter($category_ids, $filters);
			?>
		</div>
	<?php }

	$shown_termine = [];

	$api->show_offers_list($category_ids, $filters);

	if (false) :

	?>

		<ul class="<?php echo $classNameBase; ?>__entries c-adb-list__entries">
			<?php
			foreach ($offers as $offer) {

				$offer = (array) $offer;

				$button_text = esc_html($block['data']['button_text'] ?? '');

				$images = $offer_model->getImages($offer['offer_id']);

				$image_html = '';

				if (!empty($images)) {

					$srcset = [];

					// Image sizes are hard-coded and based on the JPGs returned on 15.12.2022
					// Using getimagesize for the real sizes overloads the server.

					// if (filter_var($images[0]->small ?? '', FILTER_VALIDATE_URL) !== false) {
					// 	$srcset[] = "{$images[0]->small} 180w";
					// }

					if (filter_var($images[0]->medium ?? '', FILTER_VALIDATE_URL) !== false) {
						$srcset[] = "{$images[0]->medium}?size=medium 300w";
					}

					if (filter_var($images[0]->large ?? '', FILTER_VALIDATE_URL) !== false) {
						$srcset[] = "{$images[0]->large}?size=large 800w";
					}

					// Original images can be absolutely massive
					// if (filter_var($images[0]->original ?? '', FILTER_VALIDATE_URL) !== false) {
					// 	$srcset[] = "{$images[0]->original} 2560w";
					// }

					if (!empty($srcset)) {

						$srcset = implode(', ', $srcset);

						$image_html = sprintf(
							'<figure class="%1$s__entry-figure c-adb-list__entry-figure"><img class="%1$s__entry-image c-adb-list__entry-image" src="%2$s?size=default" srcset="%3$s" alt="%4$s" loading="%5$s"></figure>',
							$classNameBase,
							$images[0]->medium ?? '',
							$srcset,
							esc_html($offer['title']),
							$count > (int) ($block['data']['initial_count'] ?? false) ? 'lazy' : 'eager'
						);
					}
				}

				if (empty($image_html)) {
					$image_html = sprintf(
						'<div class="%1$s__entry-figure %1$s__entry-figure--empty c-adb-list__entry-figure c-adb-list__entry-figure--empty"></div>',
						$classNameBase
					);
				}

				$is_hint = (bool) ($offer['is_hint'] ?? false);
				$is_hint_class = $is_hint ? "{$classNameBase}__entry--is-park-partner c-adb-list__entry--is-hint" : '';

				$park_partner = !$is_hint && (bool) ($offer['is_park_partner'] ?? false);
				$park_partner_class = $park_partner ? "{$classNameBase}__entry--is-park-partner c-adb-list__entry--is-park-partner" : '';
			?>
				<li class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> c-adb-list__entry <?php echo $park_partner_class . $is_hint_class; ?> is--hidden">

					<?php if ($is_hint) { ?>
						<div class="<?php echo $classNameBase; ?>__entry-hintlabel c-adb-list__entry-hintlabel c-adb-list__entry-postit">
							<?php _ex('Tipp', 'More offers label', 'shp_gantrisch_adb'); ?>
						</div>
					<?php } else if ($park_partner) { ?>
						<div class="<?php echo $classNameBase; ?>__entry-partnerlabel c-adb-list__entry-partnerlabel c-adb-list__entry-postit">
							<?php _ex('Parkpartner', 'More offers label', 'shp_gantrisch_adb'); ?>
						</div>
					<?php } ?>

					<div class="<?php echo $classNameBase; ?>__entry-header c-adb-list__entry-header">
						<div class="<?php echo $classNameBase; ?>__entry-title c-adb-list__entry-title">
							<a href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
						</div>

						<?php

						$termin_key = "{$offer['offer_id']}-{$offer['date_from']}";
						if (!in_array($termin_key, $shown_termine) && (strtotime($offer['date_from']) || strtotime($offer['date_to']))) {

							$date_from = parks_mysql2date($offer['date_from'], TRUE);
							$date_to = parks_mysql2date($offer['date_to'], TRUE);

							$termin_dates = parks_show_date([
								'date_from' => parks_mysql2form($date_from),
								'date_to' => parks_mysql2form($date_to)
							]);

							if (!empty($termin_dates)) {
						?>
								<div class="<?php echo $classNameBase; ?>__entry-termin c-adb-list__entry-termin c-adb-list__entry-meta">
									<p><?php echo $termin_dates; ?></p>
								</div>
						<?php
								$shown_termine[] = $termin_key;
							}
						}
						?>

						<?php if (!empty($offer['institution_location'])) { ?>
							<div class="<?php echo $classNameBase; ?>__entry-location c-adb-list__entry-location c-adb-list__entry-meta">
								<p><?php echo esc_html($offer['institution_location']); ?></p>
							</div>
						<?php
						} ?>
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
	<?php
	endif; // Hide self-build UL
	?>
</div>
