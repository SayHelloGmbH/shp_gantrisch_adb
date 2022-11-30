<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferExcerpt;

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

		$offer_excerpt = shp_gantrisch_adb_get_instance()->Model->Offer->getExcerpt();

		if (empty($offer_excerpt) || !is_string($offer_excerpt)) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

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
