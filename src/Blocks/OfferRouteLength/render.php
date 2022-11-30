<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferRouteLength;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Package\Helpers as HelpersPackage;

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

$offer = shp_gantrisch_adb_get_instance()->Model->Offer->getOffer();

$routelength = $offer->route_length ?? '';

if ((int) $routelength < 1) {
	return;
}

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);
$classNameBase = $block['shp']['classNameBase'] ?? '';
$helpers = new HelpersPackage();

?>

<div class="<?php echo $block['shp']['class_names']; ?>">

	<div class="<?php echo $classNameBase; ?>__content">
		<?php if (!empty($attributes['prefix'] ?? '')) { ?>
			<div class="<?php echo $classNameBase; ?>__prefix"><?php echo strip_tags($attributes['prefix']); ?></div>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__value">
			<p><?php printf(_x('%1$s Km.', 'ADB route length', 'shp_gantrisch_adb'), $helpers->formatAmount($routelength, 1)); ?></p>
		</div>
	</div>
</div>
