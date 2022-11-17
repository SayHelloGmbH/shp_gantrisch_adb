<?php

namespace SayHello\ShpGantrischAdb\Controller;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
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
		//if (!$this->api && class_exists('ParksAPI')) {
		if (class_exists('ParksAPI')) {
			$offer_model = new OfferModel();
			$language = $offer_model->getLanguage();
			$this->api = new ParksAPI($language);
		}

		return $this->api;
	}
}
