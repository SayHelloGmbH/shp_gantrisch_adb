<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferKeywords;

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
		$offer_keywords = $offer_model->getKeywords();

		if (empty($offer_keywords)) {
			return '';
		}

		$keywords = [];

		foreach ($offer_keywords as $keyword) {
			$keywords[] = sprintf('<span class="%1$s__entry">%2$s</span>', $block['shp']['classNameBase'], $keyword);
		}

		if (empty($keywords)) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		ob_start();

?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__entries"><?php echo implode('', $keywords); ?></div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
