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
		register_block_type(__DIR__);
	}
}
