<?php

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

use Advanced_Sidebar_Menu\Menus\Category;
use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Category as CategoryModel;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use WP_Block;

class Block
{

	private $block_controller = null;
	private $offer_controller = null;

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
		if (!$this->block_controller) {
			$this->block_controller = new BlockController();
		}

		$this->block_controller->extend($block);

		$offer_model = new OfferModel();

		if (!empty($attributes['category'] ?? '')) {
			$data = $offer_model->getByCategory((int) $attributes['category'], true);
		} else {
			$data = $offer_model->getAll(true);
		}

		if (empty($data)) {
			return '';
		}

		if (!$this->offer_controller) {
			$this->offer_controller = new OfferController();
		}

		$offer_model = new OfferModel();

		if (!empty($attributes['category'] ?? '')) {
			$data = $offer_model->getByCategory((int) $attributes['category'], true);
			$category_model = new CategoryModel();
			$category_name = $category_model->getTitle($attributes['category']);
		} else {
			$data = $offer_model->getAll(true);
			$category_name = '';
		}

		ob_start();
?>
		<div class="<?php echo $block->shp->class_names; ?>" data-category="<?php echo $category_name; ?>">
			<ul class="<?php echo $block->shp->classNameBase; ?>__entries">
				<?php
				foreach ($data as $offer) {
				?>
					<li class="<?php echo $block->shp->classNameBase; ?>__entry <?php echo $block->shp->classNameBase; ?>__entry--<?php echo $offer['id']; ?>">
						<a href="<?php echo $this->offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
					</li>
				<?php
				}
				?>
			</ul>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
