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
			$offer_model = shp_gantrisch_adb_get_instance()->Model_Offer;
			$language = $offer_model->getLanguage();
			$this->api = new ParksAPI($language);
		}

		return $this->api;
	}
}
