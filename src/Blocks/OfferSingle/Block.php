<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSingle;

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

		$offer_id = preg_replace('/[^0-9]/', '', get_query_var('adb_offer_id'));

		if (empty($offer_id)) {
			return '';
		}

		$classNameBase = wp_get_block_default_classname($block->name);
		$align = $attributes['align'] ?? '';
		if (!empty($align)) {
			$align = "align{$align}";
		}

		ob_start();
?>
		<div class="<?php echo $classNameBase; ?> <?php echo $align; ?>">

		</div>


<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
