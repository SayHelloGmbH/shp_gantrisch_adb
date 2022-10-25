<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSubscription;

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

		$data = $this->model->getSubscription((int) $offer_id, $attributes);

		if (!is_array($data)) {
			return '';
		}

		ob_start();

		$block_controller = new BlockController();
		$block_controller->extend($block);
?>
		<div class="<?php echo $block->shp->class_names; ?>">
			<div class="<?php echo $block->shp->classNameBase; ?>__content">

				<?php if (!empty($attributes['title_sub_required'] ?? '')) { ?>
					<h2 class="<?php echo $block->shp->classNameBase; ?>__subtitle--required"><?php echo esc_html($attributes['title_sub_required']); ?></h2>
				<?php } ?>

				<?php if (!empty($attributes['message'] ?? '')) {
					echo wpautop($attributes['message']);
				} ?>

				<?php if (!empty($data['contact'] ?? '')) { ?>

					<?php if (!empty($attributes['title_sub_at'] ?? '')) { ?>
						<h3 class="<?php echo $block->shp->classNameBase; ?>__subtitle--at"><?php echo esc_html($attributes['title_sub_at']); ?></h3>
					<?php } ?>

				<?php
					echo wpautop(make_clickable($data['contact']));
				} ?>

				<?php

				if (!empty($attributes['button_text'] ?? '') && !empty($data['link'] ?? '')) {
					$link = null;
					$title = $this->model->getTitle($offer_id);

					if (is_wp_error($title)) {
						$title = $title->get_error_message();
					}

					if (filter_var($data['link'], FILTER_VALIDATE_EMAIL)) {
						$link = "mailto:{$data['link']}?subject={$title}";
					} else if (filter_var($data['link'], FILTER_VALIDATE_URL)) {
						$link = $data['link'];
					}

					if ($link) {
				?>
						<p class="wp-block-button <?php echo $block->shp->classNameBase; ?>__button-wrapper"><a class="wp-block-button__link <?php echo $block->shp->classNameBase; ?>__button-link" href="<?php echo $link; ?>"><?php echo esc_html($attributes['button_text']); ?></a></p>
				<?php }
				} ?>


			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
