<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferDescriptionLong;

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

		$offer_description_long = shp_gantrisch_adb_get_instance()->Model->Offer->getDescriptionLong();

		if (empty($offer_description_long)) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		ob_start();

?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<?php echo $offer_description_long; ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
