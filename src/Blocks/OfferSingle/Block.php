<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSingle;

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

		$block_controller = new BlockController();
		$block_controller->extend($block);

		$offer = $this->model->getOffer((int) $offer_id);

		if (!$offer) {
			ob_start();
?>
			<div class="<?php echo $block->shp->class_names; ?>">
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
		<div class="<?php echo $block->shp->class_names; ?>">
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
