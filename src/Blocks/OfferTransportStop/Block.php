<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferTransportStop;

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

		$stop_name = $this->model->getTransportStop((int) $offer_id, 'start');

		if (empty($stop_name)) {
			return '';
		}

		$block_controller = new BlockController();
		$block_controller->extend($block);

		$link = $this->model->getSBBTimetableURL();

		if (!empty($link) && strpos($link, '%s') !== false) {
			$link = sprintf($link, $stop_name);
		}

		ob_start();
?>
		<div class="<?php echo $block['shp']['class_names']; ?> c-offer-detail-box">
			<div class="<?php echo $block['shp']['classNameBase']; ?>__content h-stack">

				<?php if (!empty($attributes['title'] ?? '')) { ?>
					<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title c-offer-detail-box__title"><?php echo esc_html($attributes['title']); ?></h2>
				<?php } ?>

				<div class="<?php echo $block['shp']['classNameBase']; ?>__link-wrapper h-stack c-offer-detail-box__entry">
					<a target="_blank" class="<?php echo $block['shp']['classNameBase']; ?>__link" href="<?php echo $link; ?>">
						<?php
						$label = $stop_name;

						if (!empty($attributes['link_text'] ?? '') && strpos($attributes['link_text'], '%s') !== false) {
							$label = sprintf($attributes['link_text'], $label);
						}

						echo esc_html($label);
						?>
					</a>
				</div>

			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
