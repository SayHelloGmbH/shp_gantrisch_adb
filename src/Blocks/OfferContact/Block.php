<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferContact;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
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

		$offer_model = new OfferModel();
		$contact = $offer_model->getContact();

		if (empty($contact['contact'] ?? '')) {
			return '';
		}

		$is_partner = $offer_model->isParkPartner();

		ob_start();

		$block_controller = shp_gantrisch_adb_get_instance()->Controller_Block;
		$block_controller->extend($block);
?>
		<div class="c-adb-block c-adb-block--detail <?php echo $block['shp']['class_names']; ?>">

			<?php if (!empty($attributes['title'] ?? '')) { ?>
				<h2 class="<?php echo $block['shp']['classNameBase']; ?>__title"><?php echo esc_html($attributes['title']); ?></h2>
			<?php } ?>

			<?php if (!empty($attributes['partner_label'] ?? '') && $is_partner) { ?>
				<div class="<?php echo $block['shp']['classNameBase']; ?>__partner_label"><?php echo esc_html($attributes['partner_label']); ?></div>
			<?php } ?>

			<div class="<?php echo $block['shp']['classNameBase']; ?>__contact"><?php echo $contact['contact']; ?></div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
