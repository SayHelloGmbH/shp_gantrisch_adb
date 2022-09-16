<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTitle;

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
		$align = $attributes['align'] ?? '';
		if (!empty($align)) {
			$align = "align{$align}";
		}

		$offer_title = $this->model->getOfferTitle((int) $offer_id);

		if (empty($offer_title)) {
			return '';
		}

		ob_start();

?>
		<div class="<?php echo $classNameBase; ?> <?php echo $align; ?>">
			<h1 class="<?php echo $classNameBase; ?>__title"><?php echo $offer_title; ?></h1>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
