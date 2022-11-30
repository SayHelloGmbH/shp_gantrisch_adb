<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTarget;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
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

		$target_audience = shp_gantrisch_adb_get_instance()->Model->Offer->getTarget();

		if (empty($target_audience)) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		ob_start();
?>
		<div class="<?php echo $block['shp']['class_names']; ?>">

			<?php if (!empty($attributes['title'] ?? '')) { ?>
				<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo esc_html($attributes['title']); ?></h2>
			<?php } ?>

			<?php echo implode('<br>', $target_audience); ?>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
