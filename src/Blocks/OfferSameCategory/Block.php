<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferSameCategory;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);

		/**
		 * Block filters to revise the HTML structure are applied
		 * through SayHello\ShpGantrischAdb\Blocks\ListDefault\Block
		 */
	}

	public function register()
	{
		register_block_type(__DIR__);
	}
}
