<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferCategories;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use WP_Block;

class Block
{

	private $query_var = 'adb_offer_id';
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

		$offer_id = preg_replace('/[^0-9]/', '', get_query_var($this->query_var));

		if (empty($offer_id)) {
			return '';
		}

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$offer_categories = $this->model->getOfferCategories((int) $offer_id);

		if (empty($offer_categories)) {
			return '';
		}

		$classNameBase = wp_get_block_default_classname($block->name);

		$main_categories = [];
		$sub_categories = [];

		foreach ($offer_categories as $category) {
			$main_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--maincategory">%2$s</span>', $classNameBase, $category['body']);

			if (!empty($category['categories'])) {
				foreach ($category['categories'] as $sub_category) {
					$sub_categories[] = sprintf('<span class="%1$s__entry %1$s__entry--subcategory">%2$s</span>', $classNameBase, $sub_category['body']);
				}
			}
		}

		$html = [];

		if (!empty($main_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--maincategory">%2$s</div>', $classNameBase, implode('', $main_categories));
		}

		if (!empty($sub_categories)) {
			$html[] = sprintf('<div class="%1$s__entries %1$s__entries--subcategory">%2$s</div>', $classNameBase, implode('', $sub_categories));
		}

		if (empty($html)) {
			return '';
		}

		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);

		ob_start();

?>
		<div class="<?php echo $class_names; ?>">
			<?php echo implode('', $html); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
