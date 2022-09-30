<?php

namespace SayHello\ShpGantrischAdb\Controller;

/**
 * Handles generic block stuff
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Block
{
	public function run()
	{
		add_action('template_redirect', [$this, 'handleInvalidSingle']);
	}

	public function basicClasses(array $attributes)
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
}
