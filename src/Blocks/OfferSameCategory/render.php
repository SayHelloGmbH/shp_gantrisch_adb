<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSameCategory;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

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

$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

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
$offers = $offer_model->getAll($category_ids, null, 4, true);

if (empty($offers)) {
	return '';
}

$offer_controller = shp_gantrisch_adb_get_instance()->Controller->Offer;
$api = shp_gantrisch_adb_get_instance()->Controller->API->getApi();
$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="<?php echo $block['shp']['class_names']; ?> c-adb-list">
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

				$is_hint = (bool) ($offer['is_hint'] ?? false);
				$is_hint_class = $is_hint ? "{$classNameBase}__entry--is-park-partner c-adb-list__entry--is-hint" : '';

				$park_partner = !$is_hint && (bool) ($offer['is_park_partner'] ?? false);
				$park_partner_class = $park_partner ? "{$classNameBase}__entry--is-park-partner c-adb-list__entry--is-park-partner" : '';

			?>
				<li class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> c-adb-list__entry <?php echo $park_partner_class . $is_hint_class; ?>">

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

						<?php if (!empty($offer['institution_location'])) { ?>
							<div class="<?php echo $classNameBase; ?>__entry-location c-adb-list__entry-location">
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
