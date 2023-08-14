<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTime;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$gutenberg_package = new GutenbergPackage();

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

$time_required = shp_gantrisch_adb_get_instance()->Model->Offer->getTimeRequired();

if (empty($time_required)) {
	return;
}

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);
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