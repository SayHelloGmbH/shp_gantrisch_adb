<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTitle;

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

		$offer_title = $this->model->getOfferTitle((int) $offer_id);

		if (empty($offer_title)) {
			return '';
		}

		$classNameBase = wp_get_block_default_classname($block->name);
		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);

		ob_start();

?>
		<div class="<?php echo $class_names; ?>">
			<h1 class="<?php echo $classNameBase; ?>__title"><?php echo $offer_title; ?></h1>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
