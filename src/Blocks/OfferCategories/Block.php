<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferCategories;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use WP_Block;

class Block
{

	private $model = null;

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

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$offer_id = $this->model->requestedOfferID();

		if (empty($offer_id)) {
			return '';
		}

		$offer_categories = $this->model->getOfferCategories((int) $offer_id);

		if (empty($offer_categories)) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		$main_categories = [];
		$sub_categories = [];

		foreach ($offer_categories as $category) {
			$main_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--maincategory">%2$s</span>', $block->shp->classNameBase, $category['body']);

			if (!empty($category['categories'])) {
				foreach ($category['categories'] as $sub_category) {
					$sub_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--subcategory">%2$s</span>', $block->shp->classNameBase, $sub_category['body']);
				}
			}
		}

		$html = [];

		if (!empty($main_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--maincategory">%2$s</div>', $block->shp->classNameBase, implode('', $main_categories));
		}

		if (!empty($sub_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--subcategory">%2$s</div>', $block->shp->classNameBase, implode('', $sub_categories));
		}

		if (empty($html)) {
			return '';
		}

		ob_start();

?>
		<div class="<?php echo $block->shp->class_names; ?>">
			<?php echo implode('', $html); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
