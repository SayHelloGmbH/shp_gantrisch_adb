<?php

namespace SayHello\ShpGantrischAdb\Controller;

use stdClass;
use WP_Block;

/**
 * Handles generic block stuff
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Block
{
	private function basicClasses(array $attributes)
	{
		$class_names = [];

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

		return $class_names;
	}

	public function classNames(WP_Block $block)
	{
		return implode(' ', array_merge([$block->shp->classNameBase], $this->basicClasses($block->attributes)));
	}

	/**
	 * Add custom rendering data to the block
	 * Pass block by reference - no return
	 *
	 * @param WP_Block $block
	 * @return void
	 */
	public function extend(WP_Block &$block)
	{
		if (!isset($block->shp)) {
			$block->shp = new stdClass;
		}

		$block->shp->classNameBase = wp_get_block_default_classname($block->name);
		$block->shp->class_names = $this->classNames($block);
	}
}
