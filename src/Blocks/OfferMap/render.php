<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTimeTermin;

use SayHello\ShpGantrischAdb\Controller\API as APIController;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--error">
			<p><?php _ex('Placeholder for ADB map.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_id = shp_gantrisch_adb_get_instance()->Model->Offer->getRequestedOfferID();

if (!$offer_id) {
	return;
}

$api_controller = new APIController();
$api = $api_controller->getApi();

ob_start();
$api->show_offers_map([], ['offers' => [(int) $offer_id]]);
$html = ob_get_contents();
ob_end_clean();

if (empty($html)) {
	return;
}

?>
<div class="<?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
		<?php echo $html; ?>
	</div>
</div>
