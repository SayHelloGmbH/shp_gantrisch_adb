<?php

namespace SayHello\ShpGantrischAdb\Controller;

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
			'callback' => function () {
				$categories = shp_gantrisch_adb_get_instance()->Model_Category->getForSelect();
				return $categories;
			}
		]);
	}
}
