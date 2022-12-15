<?php

$data = shp_gantrisch_adb_get_instance()->Model->Offer->getSubscription();

if (!$data) {
	return '';
}

shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);
?>
<div class="<?php echo $block['shp']['class_names']; ?>">
	<div class="<?php echo $block['shp']['classNameBase']; ?>__content">

		<?php if (!empty($attributes['title_sub_required'] ?? '')) { ?>
			<h2 class="<?php echo $block['shp']['classNameBase']; ?>__subtitle--required"><?php echo esc_html($attributes['title_sub_required']); ?></h2>
		<?php } ?>

		<?php if (!empty($attributes['message'] ?? '')) {
			echo wpautop($attributes['message']);
		} ?>

		<?php if (!empty($data['subscription_contact'] ?? '')) { ?>

			<?php if (!empty($attributes['title_sub_at'] ?? '')) { ?>
				<h3 class="<?php echo $block['shp']['classNameBase']; ?>__subtitle--at"><?php echo esc_html($attributes['title_sub_at']); ?></h3>
			<?php } ?>

		<?php
			echo wpautop(make_clickable($data['subscription_contact']));
		} ?>

		<?php

		if (!empty($attributes['button_text'] ?? '') && !empty($data['subscription_link'] ?? '')) {
			$link = null;
			$title = shp_gantrisch_adb_get_instance()->Model->Offer->getTitle();

			if (is_wp_error($title)) {
				$title = $title->get_error_message();
			}

			if (filter_var($data['subscription_link'], FILTER_VALIDATE_EMAIL)) {
				$link = "mailto:{$data['subscription_link']}?subject={$title}";
			} else if (filter_var($data['subscription_link'], FILTER_VALIDATE_URL)) {
				$link = $data['subscription_link'];
			}

			if ($link) {
		?>
				<p class="wp-block-button <?php echo $block['shp']['classNameBase']; ?>__button-wrapper"><a class="wp-block-button__link <?php echo $block['shp']['classNameBase']; ?>__button-link" href="<?php echo $link; ?>" target="_blank"><?php echo esc_html($attributes['button_text']); ?></a></p>
		<?php }
		} ?>


	</div>
</div>
