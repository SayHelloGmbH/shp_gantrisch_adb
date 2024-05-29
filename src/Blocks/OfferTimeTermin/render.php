<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTimeTermin;

use SayHello\ShpGantrischAdb\Package\Gutenberg as GutenbergPackage;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

$gutenberg_package = new GutenbergPackage();

if ($gutenberg_package->isContextEdit()) {
?>
	<div class="<?php echo $block['shp']['class_names']; ?>">
		<div class="c-message c-message--error">
			<p><?php _ex('Placeholder for ADB time/date.', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
			<p><?php _ex('API does not return any valid values for this element (30.11.2022).', 'Editor preview message', 'shp_gantrisch_adb'); ?></p>
		</div>
	</div>
<?php
	return;
}

$classNameBase = $block['shp']['classNameBase'] ?? '';
$offer_model = new OfferModel();
$termine = $offer_model->getTermine();

if (empty($termine)) {
	return;
}

?>
<div class="c-adb-block c-adb-block--detail <?php echo $classNameBase; ?>">

	<?php if (!empty($attributes['title'] ?? '')) { ?>
		<h3 class="<?php echo $classNameBase; ?>__title"><?php echo $attributes['title']; ?></h3>
	<?php } ?>

	<div class="<?php echo $classNameBase; ?>__content">
		<ul class="<?php echo $classNameBase; ?>__entries">
			<?php foreach ($termine as $termin) { ?>
				<li class="<?php echo $classNameBase; ?>__entry">
					<?php echo $termin; ?>
				</li>
			<?php
			} ?>
		</ul>
	</div>
</div>