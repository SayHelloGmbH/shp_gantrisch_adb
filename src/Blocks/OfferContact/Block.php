<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferContact;

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

		$classNameBase = wp_get_block_default_classname($block->name);

		$contact = $this->model->getOfferContact((int) $offer_id);

		if (empty($contact)) {
			return '';
		}

		$partner_label = $this->model->getOfferPartnerText((int) $offer_id);

		ob_start();

		$classNameBase = wp_get_block_default_classname($block->name);
		$block_controller = new BlockController();
		$class_names = $block_controller->classNames($block);
?>
		<div class="<?php echo $class_names; ?>">

			<?php if (!empty($partner_label)) { ?>
				<div class="<?php echo $classNameBase; ?>__partnerlabel"><?php echo $partner_label; ?></div>
			<?php } ?>

			<div class="<?php echo $classNameBase; ?>__contact"><?php echo $contact; ?></div>
		</div>
<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
