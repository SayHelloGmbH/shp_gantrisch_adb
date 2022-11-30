<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTime;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

$block_controller = new BlockController();
$block_controller->extend($block);

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

$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

$offer_id = $offer_model->getRequestedOfferID();

if (empty($offer_id)) {
	return '';
}

$time_required = $offer_model->getTimeRequired((int) $offer_id);

if (empty($time_required)) {
	return;
}

$block_controller = new BlockController();
$block_controller->extend($block);
$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="<?php echo $block['shp']['class_names']; ?>">
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
