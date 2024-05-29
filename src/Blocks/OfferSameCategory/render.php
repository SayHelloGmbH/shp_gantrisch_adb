<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSameCategory;

use SayHello\ShpGantrischAdb\Controller\API as APIController;
use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

$block_controller = new BlockController();
$block_controller->extend($block);

if (shp_gantrisch_adb_get_instance()->Package->Gutenberg->isContextEdit() === true) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder - offers in the same category as the current offer.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>

<?php
	return;
}

$offer_model = new OfferModel();

// Get the categories of the current requested offer
$offer_categories =	$offer_model->getCategories();

if (empty($offer_categories)) {
	return '';
}

// Use top-level categories only. Convert them to ID numbers.
$category_ids = array_map(function ($entry) {
	$category_id = str_replace('category', '', $entry);
	return (int) $category_id;
}, array_keys($offer_categories));

// Remove null or empty entries
$category_ids = array_filter($category_ids);

if (empty($category_ids)) {
	return '';
}

// Get all offers. The method pre-sorts the results.
$offers = $offer_model->getAll(category_ids: $category_ids, keywords: null, number_required: 4, exclude_current: true, custom_sort: false);

if (empty($offers)) {
	return '';
}

$offer_controller = new OfferController();

$api_controller = new APIController();
$api = $api_controller->getApi();
$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?> c-adb-list">
	<div class="<?php echo $classNameBase; ?>__inner c-adb-list__inner h-stack">

		<?php
		$title = esc_html($attributes['title'] ?? '');
		if (!empty($title)) { ?>
			<h2 class="<?php echo $classNameBase; ?>__title c-adb-list__title"><?php echo $title; ?></h2>
		<?php } ?>

		<ul class="<?php echo $classNameBase; ?>__entries c-adb-list__entries">
			<?php

			foreach ($offers as $offer) {

				$offer = (array) $offer;

				$images = $offer_model->getImages($offer['offer_id']);
				$selected_size = $attributes['image_size'] ?? 'large';

				if (!empty($images) && isset($images[0]->{$selected_size}) && filter_var($images[0]->{$selected_size}, FILTER_VALIDATE_URL) !== false) {
					$image_html = sprintf(
						'<figure class="%1$s__entry-figure c-adb-list__entry-figure"><img class="%1$s__entry-image c-adb-list__entry-image" src="%2$s" alt="%3$s" loading="lazy"></figure>',
						$classNameBase,
						$images[0]->{$selected_size},
						esc_html($offer['title'])
					);
				} else {
					$image_html = sprintf(
						'<div class="%1$s__entry-figure %1$s__entry-figure--empty"></div>',
						$classNameBase
					);
				}

				// The hint/parkpartner/parkevent labels and classes are added
				// in the render_block hook applied using the SayHello\ShpGantrischAdb\Blocks\ListDefault\Block class.

			?>
				<li id="offer_<?php echo $offer['offer_id']; ?>" class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> c-adb-list__entry">


					<div class="<?php echo $classNameBase; ?>__entry-header c-adb-list__entry-header">

						<div class="<?php echo $classNameBase; ?>__entry-title c-adb-list__entry-title">
							<a href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
						</div>

						<?php if (!empty($offer['institution_location'])) { ?>
							<div class="<?php echo $classNameBase; ?>__entry-location c-adb-list__entry-location institution_location">
								<p><?php echo esc_html($offer['institution_location']); ?></p>
							</div>
						<?php
						}
						?>
					</div>

					<?php echo $image_html; ?>

					<?php
					$button_text = esc_html($attributes['button_text'] ?? '');
					if (!empty($button_text)) { ?>
						<div class="<?php echo $classNameBase; ?>__entry-buttonwrapper c-adb-list__entry-buttonwrapper">
							<a class="<?php echo $classNameBase; ?>__entry-button c-adb-list__entry-button" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo $button_text; ?></a>
						</div>
					<?php } ?>

					<a class="<?php echo $classNameBase; ?>__entry-floodlink c-adb-list__entry-floodlink" href="<?php echo $offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
				</li>
			<?php
			}
			?>
		</ul>
	</div>
</div>