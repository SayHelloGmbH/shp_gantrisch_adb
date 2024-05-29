<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferPrice;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
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
		$price = $offer_model->getPrice();

		if (empty($price)) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		ob_start();
?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
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
