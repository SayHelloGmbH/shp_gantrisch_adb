<?php

namespace SayHello\ShpGantrischAdb\Blocks\OfferRouteLength;

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
