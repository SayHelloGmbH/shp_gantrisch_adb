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

	public function enqueueRemoteAssets()
	{
		$api = $this->getApi();

		wp_enqueue_style("parks_offers_parkapp-api", "https://angebote.paerke.ch/api/lib/api-17/api.css", [], null);
		wp_enqueue_script("parks_offers_i18n", "https://angebote.paerke.ch/api/lib/api-17/{$api->lang_id}.js", ['jquery'], null, true);
		// wp_enqueue_script("parks_offers_jquery", "https://angebote.paerke.ch/api/lib/api-17/jquery.min.js", [], null, false);
		// wp_enqueue_script("parks_offers_jquery-ui", "https://angebote.paerke.ch/api/lib/api-17/jquery-ui.min.js", [], null, false);
		wp_enqueue_script("parks_offers_parkapp", "https://angebote.paerke.ch/api/lib/api-17/ParkApp.min.js", ['jquery'], null, true);
	}
}
