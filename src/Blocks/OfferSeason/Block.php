<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSeason;

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

		$months = $this->model->getSeason((int) $offer_id);

		if (empty($months) || !is_array($months)) {
			return '';
		}

		ob_start();

		$block_controller = new BlockController();
		$block_controller->extend($block);
?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content">
				<?php
				echo implode('<br>', $months);
				?>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
