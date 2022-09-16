<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSingle;

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

		$offer = $this->model->getOffer((int) $offer_id);

		if (!$offer) {
			ob_start();
?>
			<div class="<?php echo $classNameBase; ?>">
				<div class="c-message c-message--error">
					<p><?php _ex('Sorry, no matching offer found.', 'Frontend error message', 'shp_gantrisch_adb'); ?></p>
				</div>
			</div>
		<?php
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}

		ob_start();

		?>
		<div class="<?php echo $classNameBase; ?> <?php echo $align; ?>">
			<?php
			dump($offer);
			?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
