<?php

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

use SayHello\ShpGantrischAdb\Controller\Block as BlockController;
use SayHello\ShpGantrischAdb\Controller\Offer as OfferController;
use SayHello\ShpGantrischAdb\Model\Category as CategoryModel;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use WP_Block;

class Block
{

	private $block_controller = null;
	private $offer_controller = null;
	private $offer_model = null;

	public function run()
	{
		add_action('init', [$this, 'register']);
	}

	public function getOfferModel()
	{
		if (!$this->offer_model) {
			$this->offer_model = new OfferModel();
		}

		return $this->offer_model;
	}

	public function register()
	{
		register_block_type(dirname(__FILE__) . '/block.json', [
			'render_callback' => [$this, 'render']
		]);
	}

	public function render(array $attributes, string $content, WP_Block $block)
	{
		if (!$this->block_controller) {
			$this->block_controller = new BlockController();
		}

		$this->block_controller->extend($block);

		$data = $this->getOfferModel()->getAll((int) $attributes['category'] ?? false);

		if (empty($data)) {
			return '';
		}

		if (!$this->offer_controller) {
			$this->offer_controller = new OfferController();
		}

		if (!empty($attributes['category'] ?? '')) {
			$category_model = new CategoryModel();
			$category_name = $category_model->getTitle($attributes['category']);
		} else {
			$category_name = '';
		}

		// Random order as requested by client
		shuffle($data);

		$load_more_text = $attributes['load_more_text'] ?? '';
		if (empty($load_more_text)) {
			$load_more_text = _x('Load more', 'List block default button text', 'shp_gantrisch_adb');
		}

		$viewScript = 'src/Blocks/ListDefault/assets/dist/scripts/viewScript.js';
		wp_enqueue_script($block->block_type->view_script, shp_gantrisch_adb_get_instance()->url . $viewScript, [], filemtime(shp_gantrisch_adb_get_instance()->path . $viewScript), true);
		wp_localize_script($block->block_type->view_script, 'shp_gantrisch_adb_block_list_default', [
			'load_more_text' => $load_more_text,
			'initial_count' => (int) ($attributes['initial_count'] ?? false),
		]);

		ob_start();
?>
		<div class="<?php echo $block->shp->class_names; ?>" data-category="<?php echo $category_name; ?>">
			<ul class="<?php echo $block->shp->classNameBase; ?>__entries">
				<?php
				foreach ($data as $offer) {

					$offer = (array) $offer;

					$button_text = esc_html($attributes['button_text'] ?? '');

					if (!$this->offer_model) {
						$this->offer_model = new OfferModel();
					}

					$images = $this->getOfferModel()->getImages($offer['offer_id']);
					$selected_size = $attributes['image_size'] ?? 'small';

					if (!empty($images) && isset($images[0]->{$selected_size}) && filter_var($images[0]->{$selected_size}, FILTER_VALIDATE_URL) !== false) {
						$image_html = sprintf(
							'<figure class="%1$s__entry-figure"><img class="%1$s__entry-image" src="%2$s" alt="%3$s" loading="lazy"></figure>',
							$block->shp->classNameBase,
							$images[0]->{$selected_size},
							esc_html($offer['title'])
						);
					} else {
						$image_html = sprintf(
							'<div class="%1$s__entry-figure %1$s__entry-figure--empty"></div>',
							$block->shp->classNameBase
						);
					}
				?>
					<li class="<?php echo $block->shp->classNameBase; ?>__entry <?php echo $block->shp->classNameBase; ?>__entry--<?php echo $offer['offer_id']; ?> is--hidden">

						<div class="<?php echo $block->shp->classNameBase; ?>__entry-header">
							<div class="<?php echo $block->shp->classNameBase; ?>__entry-title">
								<a href="<?php echo $this->offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
							</div>

							<?php if (!empty($offer['location_details'])) { ?>
								<div class="<?php echo $block->shp->classNameBase; ?>__entry-location">
									<p><?php echo esc_html($offer['location_details']); ?></p>
								</div>
							<?php
							}
							?>
						</div>

						<?php echo $image_html; ?>

						<?php if (!empty($button_text)) { ?>
							<div class="<?php echo $block->shp->classNameBase; ?>__entry-buttonwrapper">
								<a class="<?php echo $block->shp->classNameBase; ?>__entry-button" href="<?php echo $this->offer_controller->singleUrl($offer); ?>"><?php echo $button_text; ?></a>
							</div>
						<?php } ?>

						<a class="<?php echo $block->shp->classNameBase; ?>__entry-floodlink" href="<?php echo $this->offer_controller->singleUrl($offer); ?>"><?php echo esc_html($offer['title']); ?></a>
					</li>
				<?php
				}
				?>
			</ul>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
