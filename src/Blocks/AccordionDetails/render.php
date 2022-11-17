<?php

namespace SayHello\ShpGantrischAdb\Blocks\AccordionDetails;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;

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

// TERMIN
$termin = $offer_model->getTermin($offer_id);

if (!empty($termin)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--termin">

		<?php if (!empty($attributes['title_termin'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--termin"><?php echo $attributes['title_termin']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--termin"><?php echo $termin; ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// LEISTUNGEN
$leistungen = $offer_model->getBenefits($offer_id);

if (!empty($leistungen)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--leistungen">

		<?php if (!empty($attributes['title_leistungen'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--leistungen"><?php echo $attributes['title_leistungen']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--leistungen"><?php echo wpautop($leistungen); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// PRICE
$price = $offer_model->getPrice($offer_id);

if (!empty($price)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--price">

		<?php if (!empty($attributes['title_price'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--price"><?php echo $attributes['title_price']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--price"><?php echo wpautop($price); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// PLACE


// ÖFFNUNGSZEITEN


// SEASON
$months = $offer_model->getSeason((int) $offer_id);

if (!empty($months)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--season">

		<?php if (!empty($attributes['title_season'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--season"><?php echo $attributes['title_season']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--season"><?php echo implode(', ', $months); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}



// INFRASTRUCTURE
$offer_infrastructure = $offer_model->getInfrastructure((int) $offer_id);

if (!empty($offer_infrastructure)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--infrastructure">

		<?php if (!empty($attributes['title_infrastructure'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--infrastructure"><?php echo $attributes['title_infrastructure']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--infrastructure"><?php echo wpautop($offer_infrastructure); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// ADDITIONAL INFORMATION
$additional_information = $offer->additional_informations ?? '';

if (!empty($additional_information)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--additionalinfo">

		<?php if (!empty($attributes['title_additional'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--additionalinfo"><?php echo $attributes['title_additional']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--additionalinfo"><?php echo wpautop($additional_information); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// SUITABLE FOR / TARGET AUDIENCE
$target_audience = $offer_model->getTarget((int) $offer_id) ?? '';

if (!empty($target_audience)) {
	ob_start();
?>
	<div class="<?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--suitability">

		<?php if (!empty($attributes['title_suitability'] ?? '')) { ?>
			<h3 class="<?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--suitability"><?php echo $attributes['title_suitability']; ?></h3>
		<?php } ?>

		<div class="<?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--suitability"><?php echo implode('<br>', $target_audience); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

if (empty($entries)) {
	return;
}

?>
<div class="<?php echo $block['shp']['class_names']; ?>" data-shp-accordion-entry>
	<div class="<?php echo $classNameBase; ?>__header">
		<h2 class="<?php echo $classNameBase; ?>__title" data-shp-accordion-entry-trigger><?php echo $attributes['title_block'] ?? 'Details'; ?></h2>
	</div>
	<div class="<?php echo $classNameBase; ?>__entries" data-shp-accordion-entry-content>
		<?php echo implode('', $entries); ?>
	</div>
</div>
