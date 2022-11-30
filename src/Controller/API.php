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

		if ($this->api instanceof ParksAPI) {
			return $this->api;
		}

		if (class_exists('ParksAPI')) {
			$language = shp_gantrisch_adb_get_instance()->Model->Offer->getLanguage();
			$this->api = new ParksAPI($language);
		}

		return $this->api;
	}
}
