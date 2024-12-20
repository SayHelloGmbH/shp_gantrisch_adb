<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSeason;

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
		$months = $offer_model->getSeason();

		if (empty($months) || !is_array($months)) {
			return '';
		}

		ob_start();

		$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
		$block_controller->extend($block);
?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
				<ul class="c-adb-block__ul">
					<li class="c-adb-block__li">
						<?php
						echo implode('</li><li class="c-adb-block__li">', $months);
						?>
					</li>
				</ul>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
