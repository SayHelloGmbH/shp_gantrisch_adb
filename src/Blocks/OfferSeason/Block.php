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

		$months = shp_gantrisch_adb_get_instance()->Model->Offer->getSeason();

		if (empty($months) || !is_array($months)) {
			return '';
		}

		ob_start();

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);
?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
				<?php
				echo implode('<br>', $months);
				?>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
