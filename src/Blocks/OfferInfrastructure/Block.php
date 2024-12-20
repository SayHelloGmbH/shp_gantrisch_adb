<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferInfrastructure;

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
		$offer_infrastructure = $offer_model->getInfrastructure();

		if (empty($offer_infrastructure)) {
			return '';
		}

		$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
		$block_controller->extend($block);

		ob_start();

?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">

			<?php if (!empty($attributes['title'] ?? '')) { ?>
				<h2 class="<?php echo $block['shp']['classNameBase']; ?>__subtitle--required"><?php echo esc_html($attributes['title']); ?></h2>
			<?php } ?>

			<?php echo wpautop($offer_infrastructure); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
