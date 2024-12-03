<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferLinks;

$gutenberg_package = shp_gantrisch_adb_get_instance()->Package_Gutenberg;

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB offer links.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = shp_gantrisch_adb_get_instance()->Model_Offer;
$offer = $offer_model->getOffer();

if (!$offer || empty($offer->hyperlinks ?? [])) {
	return '';
}

$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
$block_controller->extend($block);
$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?> h-stack c-offer-detail-box">
	<div class="<?php echo $classNameBase; ?>__header h-stack c-offer-detail-box__header">
		<h2 class="<?php echo $classNameBase; ?>__title c-offer-detail-box__title"><?php echo $attributes['title'] ?? 'Links'; ?></h2>
	</div>
	<div class="<?php echo $classNameBase; ?>__content h-stack c-offer-detail-box__content">
		<ul class="c-adb-block__ul <?php echo $classNameBase; ?>__entries h-stack c-offer-detail-box__entries">
			<?php foreach ($offer->hyperlinks as $link) {
			?>
				<li class="c-adb-block__li <?php echo $classNameBase; ?>__entry c-offer-detail-box__entry">
					<a href="<?php echo $link->url ?? '#'; ?>" target="_blank"><?php echo $link->title ?? 'Untitled link'; ?></a>
				</li>
			<?php
			} ?>
		</ul>
	</div>
</div>
