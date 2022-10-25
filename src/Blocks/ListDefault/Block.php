<?php

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);
	}

	public function register()
	{
		register_block_type(dirname(__FILE__) . '/block.json');
	}
}
