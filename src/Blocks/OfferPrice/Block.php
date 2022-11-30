<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferPrice;

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

		$price = shp_gantrisch_adb_get_instance()->Model->Offer->getPrice();

		if (empty($price)) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		ob_start();
?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
				<?php
				echo nl2br($price);
				?>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
