<?php

namespace SayHello\ShpGantrischAdb\Model;

use SayHello\ShpGantrischAdb\Controller\API as APIController;
use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;

class Category
{
	private $language = 'de';

	/**
	 * Database table names. Parametrised in case they change later.
	 *
	 * @var array
	 */
	private $tables = [];

	public function run()
	{
		$offer_model = new OfferModel();
		$this->tables = $offer_model->getTables();
		$this->language = $offer_model->getLanguage();
	}

	/**
	 * Get the categories for use in a React select element
	 *
	 * @return array
	 */
	public function getForSelect()
	{
		$api_controller = new APIController();
		$api = $api_controller->getApi();
		return $api->model->get_category_tree();
	}

	/**
	 * Get the translated category title by category ID
	 *
	 * @param int $category_id
	 * @return string
	 */
	public function getTitle($category_id)
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT category.category_id as id, i18n.body AS name FROM {$this->tables['category']} category, {$this->tables['category_i18n']} i18n WHERE category.category_id= %s AND category.category_id = i18n.category_id AND i18n.language = %s ORDER BY category.sort ASC LIMIT 1", $category_id, $this->language);
		$results = $wpdb->get_results($sql);

		if (empty($results)) {
			return '';
		}

		return $results[0]->name ?? '';
	}
}
