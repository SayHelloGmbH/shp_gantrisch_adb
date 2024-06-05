<?php

namespace SayHello\ShpGantrischAdb\Blocks\AccordionDetails;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
$block_controller->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB Travel accordion.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
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

$classNameBase = $block['shp']['classNameBase'] ?? '';
$entries = [];

// START
$start_place_info = $offer->start_place_info ?? '';

if (!empty($start_place_info)) {

	$start_place_height = $offer->start_place_altitude ?? '';

	if (!empty($start_place_height)) {
		$start_place_info = sprintf(_x('%1$s (Höhe: %2$s m)', 'Startpoint and height', '', 'shp_gantrisch_adb'), $start_place_info, $start_place_height);
	}

	ob_start();
?>
	<div class="c-adb-block c-adb-block--detail shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--start_place_info">

		<?php if (!empty($attributes['title_start'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--start_place_info"><?php echo $attributes['title_start']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--start_place_info">
			<?php echo wpautop($start_place_info); ?>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// ÖV START
$public_transport_start = $offer->public_transport_start ?? '';

if (!empty($public_transport_start)) {

	$start_place_height = $offer->start_place_altitude ?? '';

	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--public_transport_start">

		<?php if (!empty($attributes['title_start_stop'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--public_transport_start"><?php echo $attributes['title_start_stop']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--public_transport_start">
			<?php echo wpautop($public_transport_start); ?>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// DESTINATION
$goal_place_info = $offer->goal_place_info ?? '';

if (!empty($goal_place_info)) {

	$goal_place_height = $offer->goal_place_altitude ?? '';

	if (!empty($goal_place_height)) {
		$goal_place_info = sprintf(_x('%1$s (Höhe: %2$s m)', 'Goal point and height', '', 'shp_gantrisch_adb'), $goal_place_info, $goal_place_height);
	}

	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--goal_place_info">

		<?php if (!empty($attributes['title_goal'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--goal_place_info"><?php echo $attributes['title_goal']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--goal_place_info">
			<?php echo wpautop($goal_place_info); ?>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// ÖV GOAL
$public_transport_stop = $offer->public_transport_stop ?? '';

if (!empty($public_transport_stop)) {

	$start_place_height = $offer->start_place_altitude ?? '';

	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--public_transport_stop">

		<?php if (!empty($attributes['title_goal_stop'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--public_transport_stop"><?php echo $attributes['title_goal_stop']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--public_transport_stop">
			<?php echo wpautop($public_transport_stop); ?>
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
<div class="shb-accordion <?php echo $block['shp']['class_names']; ?>" data-shp-accordion-entry>
	<div class="shb-accordion__header <?php echo $classNameBase; ?>__header">
		<h2 class="shb-accordion__title <?php echo $classNameBase; ?>__title" data-shp-accordion-entry-trigger><?php echo $attributes['title_block'] ?? 'Details'; ?></h2>
	</div>
	<div class="shb-accordion__entries <?php echo $classNameBase; ?>__entries" data-shp-accordion-entry-content>
		<?php echo implode('', $entries); ?>
	</div>
</div>