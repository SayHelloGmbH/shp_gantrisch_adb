<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTransportStop;

use WP_Block;

class Block
{

	public function run()
	{
		add_action('init', [$this, 'register']);
	}

	public function register()
	{
		register_block_type(dirname(__FILE__) . '/block.json', [
			'render_callback' => [$this, 'render']
		]);
	}

	public function render(array $attributes, string $content, WP_Block $block)
	{

		$offer_model = shp_gantrisch_adb_get_instance()->Model_Offer;
		$offer_id = $offer_model->getRequestedOfferID();

		if (empty($offer_id)) {
			return '';
		}

		$stop_name = $offer_model->getTransportStop('start');

		if (empty($stop_name)) {
			return '';
		}

		$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
		$block_controller->extend($block);

		$link = $offer_model->getSBBTimetableURL();

		if (!empty($link) && strpos($link, '%s') !== false) {
			$link = sprintf($link, $stop_name);
		}

		ob_start();
?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?> c-offer-detail-box">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content h-stack">

				<?php if (!empty($attributes['title'] ?? '')) { ?>
					<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title c-offer-detail-box__title"><?php echo esc_html($attributes['title']); ?></h2>
				<?php } ?>

				<div class="<?php echo $block['shp']['classNameBase']; ?>__link-wrapper h-stack c-offer-detail-box__entry">
					<a target="_blank" class="<?php echo $block['shp']['classNameBase']; ?>__link" href="<?php echo $link; ?>">
						<?php
						$label = $stop_name;

						if (!empty($attributes['link_text'] ?? '') && strpos($attributes['link_text'], '%s') !== false) {
							$label = sprintf($attributes['link_text'], $label);
						}

						echo esc_html($label);
						?>
					</a>
				</div>

			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
