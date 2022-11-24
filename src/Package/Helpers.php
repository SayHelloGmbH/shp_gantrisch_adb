<?php

namespace SayHello\ShpGantrischAdb\Package;

/**
 * Helpers
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Helpers
{
	public function formatAmount(string|float|int $value, int $decimals = 2): string
	{
		if (floatval(intval($value)) === floatval($value)) {
			// The number is an integer. Remove all the decimals
			return (string) intval($value);
		}

		return number_format($value, $decimals);
	}
}
