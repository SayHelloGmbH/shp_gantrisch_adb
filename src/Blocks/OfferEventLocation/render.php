<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferEventLocation;

$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
$block_controller->extend($block);

$gutenberg_package = shp_gantrisch_adb_get_instance()->Package_Gutenberg;

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--error">
			<p><?php _ex('Placeholder for ADB event location.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = shp_gantrisch_adb_get_instance()->Model_Offer;
$offer = $offer_model->getOffer();

if (!$offer) {
	return;
}

$institution = $offer->institution ?? '';

if (empty($institution)) {
	return;
}

?>
<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">

	<?php if (!empty($attributes['title'] ?? '')) { ?>
		<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo strip_tags($attributes['title']); ?></h2>
	<?php } ?>

	<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
		<?php echo wpautop(make_clickable($institution)); ?>
	</div>
</div>
