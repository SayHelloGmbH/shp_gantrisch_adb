<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferExcerpt;

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
		$offer_id = $offer_model->requestedOfferID();

		if (empty($offer_id)) {
			return '';
		}

		$offer_excerpt = $offer_model->getExcerpt((int) $offer_id);

		if (empty($offer_excerpt) || !is_string($offer_excerpt)) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		ob_start();

?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<?php echo $offer_excerpt; ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
