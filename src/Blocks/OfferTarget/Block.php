<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTarget;

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

		$classNameBase = wp_get_block_default_classname($block->name);

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$target_audience = $this->model->getOfferTarget((int) $offer_id);

		if (empty($target_audience)) {
			return '';
		}

		ob_start();

		$block_controller = new BlockController();
		$class_names = array_merge([$classNameBase], $block_controller->basicClasses($attributes));
?>
		<div class="<?php echo implode(' ', $class_names); ?>">
			<?php echo nl2br($target_audience); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
