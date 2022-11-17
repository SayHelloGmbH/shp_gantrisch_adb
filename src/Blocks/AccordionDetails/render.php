<?php

namespace SayHello\ShpGantrischAdb\Blocks\AccordionDetails;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Blocks\OfferSeason\Block as OfferSeasonBlock;

$block_controller = new BlockController();
$block_controller->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB Details accordion.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = new OfferModel();

$offer_id = $offer_model->requestedOfferID();

if (empty($offer_id)) {
	return;
}

$offer = $offer_model->getOffer((int) $offer_id);

if (!$offer) {
	return;
}

$classNameBase = $block['shp']['classNameBase'] ?? '';
$entries = [];


// SEASON
$months = $offer_model->getSeason((int) $offer_id);

if (!empty($months)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry">

		<?php if (!empty($attributes['title_season'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title"><?php echo $attributes['title_season']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content"><?php echo implode(', ', $months); ?></div>
	</div>
<?php
	$entry = ob_get_contents();
	ob_end_clean();

	$entries[] = $entry;
}


// INFRASTRUCTURE
$offer_infrastructure = $offer_model->getInfrastructure((int) $offer_id);

if (!empty($offer_infrastructure)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry">

		<?php if (!empty($attributes['title_infrastructure'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title"><?php echo $attributes['title_infrastructure']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content"><?php echo wpautop($offer_infrastructure); ?></div>
	</div>
<?php
	$entry = ob_get_contents();
	ob_end_clean();

	$entries[] = $entry;
}


// ADDITIONAL INFORMATION
$additional_information = $offer->additional_informations ?? '';

if (!empty($additional_information)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry">

		<?php if (!empty($attributes['title_additional'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title"><?php echo $attributes['title_additional']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content"><?php echo wpautop($additional_information); ?></div>
	</div>
<?php
	$entry = ob_get_contents();
	ob_end_clean();

	$entries[] = $entry;
}


// SUITABLE FOR / TARGET AUDIENCE
$target_audience = $offer_model->getTarget((int) $offer_id) ?? '';

if (!empty($target_audience)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry">

		<?php if (!empty($attributes['title_additional'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title"><?php echo $attributes['title_additional']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content"><?php echo implode('<br>', $target_audience); ?></div>
	</div>
<?php
	$entry = ob_get_contents();
	ob_end_clean();

	$entries[] = $entry;
}

if (empty($entries)) {
	return;
}

?>
<div class="<?php echo $block['shp']['class_names']; ?>" data-shp-accordion-entry>
	<div class="<?php echo $classNameBase; ?>__header">
		<h2 class="<?php echo $classNameBase; ?>__title" data-shp-accordion-entry-trigger><?php echo $attributes['title_block'] ?? 'Details'; ?></h2>
	</div>
	<div class="<?php echo $classNameBase; ?>__entries">
		<?php echo implode('', $entries); ?>
	</div>
</div>
