<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferEventLocation;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$gutenberg_package = new GutenbergPackage();

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

$offer = shp_gantrisch_adb_get_instance()->Model->Offer->getOffer();

if (!$offer) {
	return;
}

$institution = $offer->institution ?? '';

if (empty($institution)) {
	return;
}

?>
<div class="<?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content"><?php echo wpautop($institution); ?></div>
</div>
