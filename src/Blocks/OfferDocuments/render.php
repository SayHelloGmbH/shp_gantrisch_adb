<?php

namespace SayHello\ShpGantrischAdb\Blocks\AccordionDetails;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

$block_controller = new BlockController();
$block_controller->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB offer documents.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = new OfferModel();
$offer = $offer_model->getOffer();

if (!$offer || !is_array($offer->documents ?? null)) {
	return;
}

$classNameBase = $block['shp']['classNameBase'] ?? '';

?>
<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?> h-stack c-offer-detail-box">
	<div class="<?php echo $classNameBase; ?>__header h-stack c-offer-detail-box__header">
		<h2 class="<?php echo $classNameBase; ?>__title c-offer-detail-box__title"><?php echo $attributes['title'] ?? 'Documents'; ?></h2>
	</div>
	<div class="<?php echo $classNameBase; ?>__content h-stack c-offer-detail-box__content">
		<ul class="<?php echo $classNameBase; ?>__entries h-stack c-offer-detail-box__entries">
			<?php foreach ($offer->documents as $document) {
			?>
				<li class="<?php echo $classNameBase; ?>__entry c-offer-detail-box__entry">
					<a href="<?php echo $document->url ?? '#'; ?>" target="_blank"><?php echo $document->title ?? 'Untitled document'; ?></a>
				</li>
			<?php
			} ?>
		</ul>
	</div>
</div>