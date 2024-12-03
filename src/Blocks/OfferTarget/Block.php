<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTarget;

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
		$target_audience = $offer_model->getTarget();

		if (empty($target_audience)) {
			return '';
		}

		$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
		$block_controller->extend($block);

		ob_start();
?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">

			<?php if (!empty($attributes['title'] ?? '')) { ?>
				<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo esc_html($attributes['title']); ?></h2>
			<?php } ?>

			<ul class="c-adb-block__ul">
				<li class="c-adb-block__li"><?php echo implode('</li><li class="c-adb-block__li">', $target_audience); ?></li>
			</ul>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
