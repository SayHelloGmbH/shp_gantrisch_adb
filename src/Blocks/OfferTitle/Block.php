<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTitle;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
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

		$offer_model = shp_gantrisch_adb_get_instance()->Model->Offer;
		dump($offer_model->getTitle(), 1, 1);

		$offer_title = shp_gantrisch_adb_get_instance()->Model->Offer->getTitle();

		if (!$offer_title) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		ob_start();

?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<h1 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo $offer_title; ?></h1>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
