<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTimeTermin;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB map.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer = shp_gantrisch_adb_get_instance()->Model->Offer->getOffer();

if (!$offer) {
	return;
}

?>

<div class="<?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
		<iframe class="<?php echo $block['shp']['classNameBase']; ?>__iframe" src="https://angebote.paerke.ch/de/export/iframe/detail/0a54fe6d6aaa9a5956937700ef1201de31eabd49/<?php echo $offer->offer_id; ?>?map_only=true">
	</div>
</div>