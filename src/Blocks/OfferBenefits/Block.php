<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferBenefits;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use WP_Block;

class Block
{

	private $query_var = 'adb_offer_id';
	private $model = null;

	public function run()
	{
		$this->model = shp_gantrisch_adb_get_instance()->Model->Offer;
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

		$benefits = $this->model->getOfferBenefits((int) $offer_id);

		if (empty($benefits)) {
			return '';
		}

		ob_start();

		$block_controller = new BlockController();
		$class_names = array_merge([$classNameBase], $block_controller->basicClasses($attributes));
?>
		<div class="<?php echo implode(' ', $class_names); ?>">
			<div class="<?php echo $classNameBase; ?>__content">
				<?php
				echo nl2br($benefits);
				?>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
