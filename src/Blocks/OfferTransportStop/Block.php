<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTransportStop;

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

		$stop_name = $this->model->getOfferTransportStop((int) $offer_id, 'start');

		if (empty($stop_name)) {
			return '';
		}

		$classNameBase = wp_get_block_default_classname($block->name);
		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);

		$link = $this->model->getSBBTimetableURL();

		if (!empty($link) && strpos($link, '%s') !== false) {
			$link = sprintf($link, $stop_name);
		}

		ob_start();
?>
		<div class="<?php echo $class_names; ?>">
			<div class="<?php echo $classNameBase; ?>__content">

				<?php if (!empty($attributes['title'] ?? '')) { ?>
					<h2 class="<?php echo $classNameBase; ?>__title"><?php echo esc_html($attributes['title']); ?></h2>
				<?php } ?>

				<p class="<?php echo $classNameBase; ?>__link-wrapper">
					<a class="<?php echo $classNameBase; ?>__link" href="<?php echo $link; ?>">
						<?php
						$label = $stop_name;

						if (!empty($attributes['link_text'] ?? '') && strpos($attributes['link_text'], '%s') !== false) {
							$label = sprintf($attributes['link_text'], $label);
						}

						echo esc_html($label);
						?>
					</a>
				</p>

			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
