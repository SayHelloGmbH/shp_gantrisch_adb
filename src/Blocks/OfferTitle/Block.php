<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTitle;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
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

		$offer_model = new OfferModel();
		$offer_title = $offer_model->getTitle();

		if (!$offer_title) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		ob_start();

?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
			<h1 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo $offer_title; ?></h1>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
