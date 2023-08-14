<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferCategories;

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

		$offer_categories =	shp_gantrisch_adb_get_instance()->Model->Offer->getCategories();

		if (empty($offer_categories)) {
			return '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		$main_categories = [];
		$sub_categories = [];

		foreach ($offer_categories as $category) {
			$main_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--maincategory">%2$s</span>', $block['shp']['classNameBase'], $category['body']);

			if (!empty($category['categories'])) {
				foreach ($category['categories'] as $sub_category) {
					$sub_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--subcategory">%2$s</span>', $block['shp']['classNameBase'], $sub_category['body']);
				}
			}
		}

		$html = [];

		if (!empty($main_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--maincategory">%2$s</div>', $block['shp']['classNameBase'], implode('', $main_categories));
		}

		if (!empty($sub_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--subcategory">%2$s</div>', $block['shp']['classNameBase'], implode('', $sub_categories));
		}

		if (empty($html)) {
			return '';
		}

		ob_start();

?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">
			<?php echo implode('', $html); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
