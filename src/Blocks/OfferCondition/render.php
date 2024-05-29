<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferCondition;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Package\Helpers as HelpersPackage;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

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

$offer_model = new OfferModel();
$offer = $offer_model->getOffer();

if (!$offer) {
	return;
}

$routecondition = $offer->route_condition ?? '';

if (empty($routecondition)) {
	return;
}

$block_controller = new BlockController();
$block_controller->extend($block);

$classNameBase = $block['shp']['classNameBase'] ?? '';
$helpers = new HelpersPackage();

?>

<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">

	<div class="<?php echo $classNameBase; ?>__content">
		<?php if (!empty($attributes['prefix'] ?? '')) { ?>
			<div class="<?php echo $classNameBase; ?>__prefix"><?php echo strip_tags($attributes['prefix']); ?></div>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__value">
			<p><?php echo $routecondition; ?></p>
		</div>
	</div>
</div>