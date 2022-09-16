<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSingle;

use WP_Block;

class Block
{

	private $query_var = 'adb_offer_id';

	public function run()
	{
		add_action('init', [$this, 'register']);
		add_action('template_redirect', [$this, 'handleInvalidSingle']);
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
		$align = $attributes['align'] ?? '';
		if (!empty($align)) {
			$align = "align{$align}";
		}

		ob_start();
?>
		<div class="<?php echo $classNameBase; ?> <?php echo $align; ?>">

		</div>


<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Handle requests for an invalid single request
	 * e.g. request with no ID or with an invalid ID
	 *
	 * @return void
	 */
	public function handleInvalidSingle()
	{
		$single_id = preg_replace('/[^0-9]/', '', get_query_var($this->query_var));

		if (!(int) $single_id && has_block('shp/gantrisch-adb-offer-single')) {
			header("HTTP/1.1 404 Not Found");
		}
	}
}
