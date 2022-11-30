<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSingle;

use WP_Block;

class Block
{

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

		$offer = shp_gantrisch_adb_get_instance()->Model->Offer->getOffer();

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		if (!$offer) {
			ob_start();
?>
			<div class="<?php echo $block['shp']['class_names']; ?>">
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
		<div class="<?php echo $block['shp']['class_names']; ?>">
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
