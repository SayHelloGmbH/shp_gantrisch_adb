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

$block_controller = new BlockController();
$block_controller->extend($block);

$classNameBase = $block['shp']['classNameBase'] ?? '';

$offer_model = new OfferModel();

$keywords = $block['data']['adb_keywords'] ?? '';

if (!empty($keywords)) {
	$keywords = $offer_model->prepareKeywords($keywords);
}

$category_ids = $block['data']['adb_categories'] ?? [];

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

$count = 1;

$categories_info = is_array($category_ids) ? implode(', ', $category_ids) : 'all';

?>
<div class="<?php echo $block['shp']['class_names']; ?>" data-categories="<?php echo $categories_info; ?>">
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
</div>
