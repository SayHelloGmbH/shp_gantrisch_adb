<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTime;

$gutenberg_package = shp_gantrisch_adb_get_instance()->Package_Gutenberg;

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB time required.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = shp_gantrisch_adb_get_instance()->Model_Offer;
$time_required = $offer_model->getTimeRequired();

if (empty($time_required)) {
	return;
}

$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
$block_controller->extend($block);
$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $classNameBase; ?>__content">
		<?php if (!empty($attributes['prefix'])) { ?>
			<div class="<?php echo $classNameBase; ?>__prefix">
				<?php echo strip_tags($attributes['prefix']); ?>
			</div>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__value">
			<?php echo $time_required; ?>
		</div>
	</div>
</div>
