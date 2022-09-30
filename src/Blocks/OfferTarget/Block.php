<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTarget;

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

		$target_audience = $this->model->getOfferTarget((int) $offer_id);

		if (empty($target_audience)) {
			return '';
		}

		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);

		ob_start();
?>
		<div class="<?php echo $class_names; ?>">
			<?php echo nl2br($target_audience); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
