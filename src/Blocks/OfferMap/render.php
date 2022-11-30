<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTimeTermin;

use SayHello\ShpGantrischAdb\Controller\API as APIController;
use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

$block_controller = new BlockController();
$block_controller->extend($block);

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

$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;

$offer_id = $offer_model->getRequestedOfferID();

if (empty($offer_id)) {
	return;
}

$offer = $offer_model->getOffer($offer_id);

if (!$offer) {
	return;
}

$api_controller = new APIController();
$api = $api_controller->getApi();

dump($offer->park_id);

// Set selected park
$api->_set_selected_park($offer->park_id);

// Load view
echo $api->_load_maps_api();
return;

?>
<div class="<?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content"><?php echo wpautop($institution); ?></div>
</div>
