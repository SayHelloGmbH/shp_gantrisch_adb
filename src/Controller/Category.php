<?php

namespace SayHello\ShpGantrischAdb\Controller;

use SayHello\ShpGantrischAdb\Model\Offer as Model;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Handles category stuff for the Angebotsdatenbank
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Category
{

	public function run()
	{
		add_action('rest_api_init', [$this, 'restRoutes']);
	}

	public function restRoutes()
	{

		register_rest_route('shp_gantrisch_adb', 'categories_for_select', [
			'methods'  => WP_REST_Server::READABLE,
			'permission_callback' => function () {
				return true;
			},
			'callback' => function (WP_REST_Request $request) {
				$model = new Model();
				$categories = $model->getCategoriesForSelect();
				return $categories;
			}
		]);
	}
}
