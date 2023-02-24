<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferImages;

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

		$offer_images = shp_gantrisch_adb_get_instance()->Model->Offer->getImages();

		if (empty($offer_images)) {
			return '';
		}

		$image_size = $attributes['image_size'] ?? 'small';
		$offer_title = shp_gantrisch_adb_get_instance()->Model->Offer->getTitle();

		if (!$offer_title) {
			$offer_title = '';
		}

		shp_gantrisch_adb_get_instance()->Controller->Block->extend($block);

		// Use of viewScript in block.json allows us to enqueue the script as we want to.
		// Here, we want to enqueue it in the footer so that we can do DOM manipulation
		// and enqueue it with the file mod time as a version number
		if (count($offer_images) > 1) {
			$viewScript = 'src/Blocks/OfferImages/assets/dist/scripts/viewScript.js';
			wp_enqueue_script($block['shp']['classNameBase'], shp_gantrisch_adb_get_instance()->url . $viewScript, ['jquery', 'swiper'], filemtime(shp_gantrisch_adb_get_instance()->path . $viewScript), true);
		}

		ob_start();
?>
		<div class="<?php echo $block['shp']['class_names']; ?>">
			<div class="swiper-container">
				<div class="swiper-wrapper">
					<?php foreach ($offer_images as $image) { ?>
						<div class="swiper-slide">
							<figure class="<?php echo $block['shp']['classNameBase']; ?>__figure">
								<?php
								printf(
									'<img src="%1$s" alt="%2$s" loading="lazy" class="%3$s__image" />%4$s',
									$image->$image_size,
									$offer_title,
									$block['shp']['classNameBase'],
									!empty($image->copyright ?? '') ? sprintf(
										'<figcaption class="%1$s__figcaption">© %2$s</figcaption>',
										$block['shp']['classNameBase'],
										strip_tags($image->copyright)
									) : ''
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
