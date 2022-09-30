<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferDescriptionLong;

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
		$align = $attributes['align'] ?? '';
		if (!empty($align)) {
			$align = "align{$align}";
		}

		if (!$this->model) {
			$this->model = new OfferModel();
		}

		$offer_description_long = $this->model->getOfferDescriptionLong((int) $offer_id);

		if (empty($offer_description_long)) {
			return '';
		}

		ob_start();

?>
		<div class="<?php echo $classNameBase; ?> <?php echo $align; ?>">
			<?php echo $offer_description_long; ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
