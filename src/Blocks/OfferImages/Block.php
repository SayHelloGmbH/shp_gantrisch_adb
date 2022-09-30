<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferImages;

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

		$offer_images = $this->model->getOfferImages((int) $offer_id);

		if (empty($offer_images)) {
			return '';
		}

		$image_size = $attributes['image_size'] ?? 'small';
		$offer_title = $this->model->getOfferTitle((int) $offer_id);

		// Use of viewScript in block.json allows us to enqueue the script as we want to.
		// Here, we want to enqueue it in the footer so that we can do DOM manipulation
		// and enqueue it with the file mod time as a version number
		if (count($offer_images) > 1) {
			$viewScript = 'src/Blocks/OfferImages/assets/dist/scripts/viewScript.js';
			wp_enqueue_script($block->block_type->view_script, shp_gantrisch_adb_get_instance()->url . $viewScript, ['jquery', 'swiper'], filemtime(shp_gantrisch_adb_get_instance()->path . $viewScript), true);
		}

		$classNameBase = wp_get_block_default_classname($block->name);
		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);

		ob_start();
?>
		<div class="<?php echo $class_names; ?>">
			<div class="swiper-container">
				<div class="swiper-wrapper">
					<?php foreach ($offer_images as $image) { ?>
						<div class="swiper-slide">
							<figure class="<?php echo $classNameBase; ?>__figure">
								<?php
								printf(
									'<img src="%s" alt="%s" loading="lazy" class="%s__image" />',
									$image->$image_size,
									$offer_title,
									$classNameBase
								)
								?>
							</figure>
						</div>
					<?php } ?>
				</div>
				<?php if (count($offer_images) > 1) { ?>
					<div class="swiper-pagination"></div>
				<?php } ?>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
