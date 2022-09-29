<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSubscriptionText;

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

		$offer_subscription_text = $this->model->getOfferSubscriptionText((int) $offer_id, $attributes);

		if (empty($offer_subscription_text)) {
			return '';
		}

		ob_start();

		$class_names = [$classNameBase];

		if (!empty($attributes['align'] ?? '')) {
			$class_names[] = "align{$attributes['align']}";
		}

		if (!empty($attributes['backgroundColor'] ?? '')) {
			$class_names[] = "has-background";
			$class_names[] = "has-{$attributes['backgroundColor']}-background-color";
		}

		if (!empty($attributes['textColor'] ?? '')) {
			$class_names[] = "has-text-color";
			$class_names[] = "has-{$attributes['textColor']}-color";
		}
?>
		<div class="<?php echo implode(' ', $class_names); ?>">
			<div class="<?php echo $classNameBase; ?>__content">

				<?php if (!empty($attributes['title'] ?? '')) { ?>
					<h2 class="<?php echo $classNameBase; ?>__title"><?php echo esc_html($attributes['title']); ?></h2>
				<?php } ?>

				<p><?php echo $offer_subscription_text; ?></p>
			</div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
