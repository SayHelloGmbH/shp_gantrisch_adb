<?php

namespace SayHello\ShpGantrischAdb\Blocks\AccordionRoute;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Package\Helpers as HelpersPackage;

$helpers = new HelpersPackage();

$difficulties = [
	'',
	_x('Leicht', 'ADB route difficulty', 'shp_gantrisch_adb'),
	_x('Mittel', 'ADB route difficulty', 'shp_gantrisch_adb'),
	_x('Schwer', 'ADB route difficulty', 'shp_gantrisch_adb'),
];

$block_controller = new BlockController();
$block_controller->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--info">
			<p><?php _ex('Placeholder for ADB Route Information accordion.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
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

// routelength
$routelength = $offer->route_length ?? '';

if ((int) $routelength > 0) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--routelength">

		<?php if (!empty($attributes['title_routelength'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--routelength"><?php echo $attributes['title_routelength']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--routelength">
			<p><?php printf(_x('%s Km.', 'ADB route length', 'shp_gantrisch_adb'), $helpers->formatAmount($routelength, 1)); ?></p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// ascent
$ascent = $offer->altitude_ascent ?? '';

if ((int) $ascent > 0) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--ascent">

		<?php if (!empty($attributes['title_ascent'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--ascent"><?php echo $attributes['title_ascent']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--ascent">
			<p><?php echo $ascent; ?> m</p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// descent
$descent = $offer->altitude_descent ?? '';

if ((int) $descent > 0) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--descent">

		<?php if (!empty($attributes['title_descent'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--descent"><?php echo $attributes['title_descent']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--descent">
			<p><?php echo $descent; ?> m</p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// unpaved
$untarred_route_length = $offer->untarred_route_length ?? '';

if ((int) $untarred_route_length > 0) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--untarred_route_length">

		<?php if (!empty($attributes['title_unpaved'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--untarred_route_length"><?php echo $attributes['title_unpaved']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--untarred_route_length">
			<p><?php printf(_x('%s Km.', 'Anteil ungeteerter Wegstrecke', 'shp_gantrisch_adb'), $helpers->formatAmount($untarred_route_length, 1)); ?></p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// heightdifference
$altitude_differential = $offer->altitude_differential ?? '';

if ((int)$altitude_differential > 0) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--altitude_differential">

		<?php if (!empty($attributes['title_heightdifference'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--altitude_differential"><?php echo $attributes['title_heightdifference']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--altitude_differential">
			<p><?php echo $altitude_differential; ?> m</p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// time_required
$time_required = $offer->time_required_minutes ?? '';

if ((int) $time_required > 0) {

	$time_required_hours = intdiv($time_required, 60);
	$time_required_minutes = $time_required % 60;

	if ($time_required_minutes < 1) {
		$time_required = sprintf(
			_nx(
				'%1$s Stunde',
				'%1$s Stunden',
				$time_required_hours,
				'ADB time required',
				'shp-gantrisch_adb'
			),
			$time_required_hours
		);
	} else {
		if ($time_required_minutes > 1) {
			$time_required = sprintf(
				_nx(
					'%1$s Stunde %2$s Minuten',
					'%1$s Stunden %2$s Minuten',
					$time_required_hours,
					'ADB time required',
					'shp-gantrisch_adb'
				),
				$time_required_hours,
				$time_required_minutes
			);
		} else {
			$time_required = sprintf(
				_nx(
					'%1$s Stunde %2$s Minute',
					'%1$s Stunden %2$s Minute',
					$time_required_hours,
					'ADB time required',
					'shp-gantrisch_adb'
				),
				$time_required_hours,
				$time_required_minutes
			);
		}
	}

	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--time_required">

		<?php if (!empty($attributes['title_time'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--time_required"><?php echo $attributes['title_time']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--time_required">
			<p><?php echo $time_required; ?></p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// level_technics
$level_technics = $offer->level_technics ?? '';

if (!empty($level_technics)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--level_technics">

		<?php if (!empty($attributes['title_difficulty_technical'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--level_technics"><?php echo $attributes['title_difficulty_technical']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--level_technics">
			<p><?php echo $difficulties[(int) $level_technics] ?? 'n/a'; ?></p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// level_condition
$level_condition = $offer->level_condition ?? '';

if ((int) $level_condition) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--level_condition">

		<?php if (!empty($attributes['title_difficulty_condition'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--level_condition"><?php echo $attributes['title_difficulty_condition']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--level_condition">
			<p><?php echo $difficulties[(int) $level_condition] ?? 'n/a'; ?></p>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// material_rent
$material_rent = $offer->material_rent ?? '';

if (!empty($material_rent)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--material_rent">

		<?php if (!empty($attributes['title_equipmentrental'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--material_rent"><?php echo $attributes['title_equipmentrental']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--material_rent">
			<?php echo wpautop($material_rent); ?>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// safety_instructions
$safety_instructions = $offer->safety_instructions ?? '';

if (!empty($safety_instructions)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--safety_instructions">

		<?php if (!empty($attributes['title_safety'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--safety_instructions"><?php echo $attributes['title_safety']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--safety_instructions">
			<?php echo wpautop($safety_instructions); ?>
		</div>
	</div>
<?php
	$entries[] = ob_get_contents();
	ob_end_clean();
}

// signalization
$signalization = $offer->signalization ?? '';

if (!empty($signalization)) {
	ob_start();
?>
	<div class="shb-accordion__entry <?php echo $classNameBase; ?>__entry <?php echo $classNameBase; ?>__entry--signalization">

		<?php if (!empty($attributes['title_signals'] ?? '')) { ?>
			<h3 class="shb-accordion__entry-title <?php echo $classNameBase; ?>__entry-title <?php echo $classNameBase; ?>__entry-title--signalization"><?php echo $attributes['title_signals']; ?></h3>
		<?php } ?>

		<div class="shb-accordion__entry-content <?php echo $classNameBase; ?>__entry-content <?php echo $classNameBase; ?>__entry-content--signalization">
			<p><?php echo $signalization; ?></p>
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
<div class="shb-accordion <?php echo $block['shp']['class_names']; ?>" data-shp-accordion-entryX>
	<div class="shb-accordion__header <?php echo $classNameBase; ?>__header">
		<h2 class="shb-accordion__title <?php echo $classNameBase; ?>__title" data-shp-accordion-entry-trigger><?php echo $attributes['title_block'] ?? 'NO TITLE'; ?></h2>
	</div>
	<div class="shb-accordion__entries <?php echo $classNameBase; ?>__entries" data-shp-accordion-entry-content>
		<?php echo implode('', $entries); ?>
	</div>
</div>
