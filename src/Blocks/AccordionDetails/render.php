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
			<p><?php _ex('Placeholder for ADB Details accordion.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$offer_model = new OfferModel();

$offer = $offer_model->getOffer();

if (!$offer || empty($offer->details || '')) {
	return;
}

$classNameBase = $block['shp']['classNameBase'] ?? '';
$entries = [];

// Text field, not calculated Termine
// Since 6.5.2024
if (!empty($offer->details)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--termin">

		<?php if (!empty($attributes['title_termin'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--termin"><?php echo $attributes['title_termin']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--termin">
			<div class="shb-accordion__termine">
				<?php echo nl2br($offer->details); ?>
			</div>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// LEISTUNGEN
$leistungen = $offer_model->getBenefits();

if (!empty($leistungen)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--leistungen">

		<?php if (!empty($attributes['title_leistungen'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title  <?php echo $classNameBase; ?>__entry-title--leistungen"><?php echo $attributes['title_leistungen']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--leistungen"><?php echo wpautop($leistungen); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// PRICE
$price = $offer_model->getPrice();

if (!empty($price)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--price">

		<?php if (!empty($attributes['title_price'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--price"><?php echo $attributes['title_price']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--price"><?php echo wpautop($price); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// PLACE
$place = $offer_model->getPlace();

if (!empty($place)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--place">

		<?php if (!empty($attributes['title_place'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--place"><?php echo $attributes['title_place']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--place"><?php echo wpautop($place); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// ÖFFNUNGSZEITEN
$opening = $offer_model->getOpeningTimes();

if (!empty($opening)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--opening">

		<?php if (!empty($attributes['title_opening'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--opening"><?php echo $attributes['title_opening']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--opening"><?php echo wpautop($opening); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// SEASON
$months = $offer_model->getSeason();

if (!empty($months)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--season">

		<?php if (!empty($attributes['title_season'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--season"><?php echo $attributes['title_season']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--season"><?php echo implode(', ', $months); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}



// INFRASTRUCTURE
$offer_infrastructure = $offer_model->getInfrastructure();

if (!empty($offer_infrastructure)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--infrastructure">

		<?php if (!empty($attributes['title_infrastructure'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--infrastructure"><?php echo $attributes['title_infrastructure']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--infrastructure"><?php echo wpautop($offer_infrastructure); ?></div>
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
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--additionalinfo">

		<?php if (!empty($attributes['title_additional'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--additionalinfo"><?php echo $attributes['title_additional']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--additionalinfo"><?php echo wpautop($additional_information); ?></div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}


// SUITABLE FOR / TARGET AUDIENCE
$target_audience = $offer_model->getTarget() ?? '';

if (!empty($target_audience)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--suitability">

		<?php if (!empty($attributes['title_suitability'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--suitability"><?php echo $attributes['title_suitability']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--suitability">
			<ul class="c-adb-block__ul">
				<li class="c-adb-block__li"><?php echo implode('</li><li class="c-adb-block__li">', $target_audience); ?></li>
			</ul>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

if (empty($entries)) {
	return;
}

?>
<div class="c-adb-block c-adb-block--detail shb-accordion <?php echo $block['shp']['class_names']; ?>" data-shp-accordion-entry>
	<div class="shb-accordion__header <?php echo $classNameBase; ?>__header">
		<h2 class="shb-accordion__title <?php echo $classNameBase; ?>__title" data-shp-accordion-entry-trigger><?php echo $attributes['title_block'] ?? 'Details'; ?></h2>
	</div>
	<div class="shb-accordion__entries <?php echo $classNameBase; ?>__entries" data-shp-accordion-entry-content>
		<?php echo implode('', $entries); ?>
	</div>
</div>