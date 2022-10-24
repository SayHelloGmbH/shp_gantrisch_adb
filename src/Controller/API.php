<?php

namespace SayHello\ShpGantrischAdb\Controller;

use ParksAPI;

/**
 * Handles generic Api stuff
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class API
{

	private $api = null;

	public function getApi()
	{
		if (!$this->api) {
			$this->api = new ParksAPI('de');
		}

		return $this->api;
	}
}
