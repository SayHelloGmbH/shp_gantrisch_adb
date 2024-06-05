<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTimeTermin;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
$block_controller->extend($block);

$gutenberg_package = shp_gantrisch_adb_get_instance()->Package_Gutenberg;

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

$offer_model = new OfferModel();
$offer = $offer_model->getOffer();

if (!$offer) {
	return;
}

$route_class = '';
if (!empty($offer->route_url ?? '')) {
	$route_class = "{$block['shp']['classNameBase']}__iframe--route";
}

?>

<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
		<iframe id="adb_iframe" class="<?php echo $block['shp']['classNameBase']; ?>__iframe <?php echo $route_class; ?>" data-src="https://angebote.paerke.ch/de/export/iframe/detail/0a54fe6d6aaa9a5956937700ef1201de31eabd49/<?php echo $offer->offer_id; ?>?map_only=true"></iframe>
	</div>
</div>

<script>
	const iframe = document.getElementById('adb_iframe');
	iframe.addEventListener('load', () => {
		let script = document.createElement('script');
		script.addEventListener('load', () => {
			iFrameResize({}, '#adb_iframe');
		});
		document.head.appendChild(script);
		script.setAttribute(
			'src',
			'https://angebote.paerke.ch/js/iframe-resizer/iframeResizer.min.js'
		);

	});
	iframe.setAttribute('src', iframe.getAttribute('data-src'));
	iframe.classList.add('is--loaded');
</script>