<?php

namespace SayHello\ShpGantrischAdb\Model;

use SayHello\ShpGantrischAdb\Model\Offer as OfferModel;
use WP_Error;

class Category
{
	private $language = 'de';

	/**
	 * Database table names. Parametrised in case they change later.
	 *
	 * @var array
	 */
	private $tables = [];

	public function __construct()
	{
		//$this->cache = !defined('WP_DEBUG') || !WP_DEBUG; // Buggy 30.9.2022 mhm
		$this->cache = false;
		$this->date_format = get_option('date_format');

		$this->offer_model = new OfferModel();
		$this->tables = $this->offer_model->getTables();
		$this->language = $this->offer_model->getLanguage();
	}

	public function run()
	{
	}

	private function getAllHierarchical()
	{

		global $wpdb;
		$sql = $wpdb->prepare("SELECT category.category_id as id, category.parent_id as parent_id, i18n.body AS name FROM {$this->tables['category']} category, {$this->tables['category_i18n']} i18n WHERE category.category_id = i18n.category_id AND i18n.language = %s ORDER BY category.sort ASC", $this->language);
		$results = $wpdb->get_results($sql, ARRAY_A);

		$categories = [];

		foreach ($results as $result) {
			if ($result['parent_id'] === '0') {
				$categories["category_{$result['id']}"] = $result;
				$categories["category_{$result['id']}"]['level'] = 1;
				$categories["category_{$result['id']}"]['children'] = [];
				foreach ($results as $level2_result) {
					if ($level2_result['parent_id'] === $result['id']) {
						$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"] = [];
						$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['id'] = $level2_result['id'];
						$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['name'] = html_entity_decode($level2_result['name']);
						$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['level'] = 2;
						$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children'] = [];

						foreach ($results as $level3_result) {
							if ($level3_result['parent_id'] === $level2_result['id']) {
								$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"] = [];
								$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['id'] = $level3_result['id'];
								$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['name'] = html_entity_decode($level3_result['name']);
								$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['level'] = 3;
								$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children'] = [];

								foreach ($results as $level4_result) {
									if ($level4_result['parent_id'] === $level3_result['id']) {
										$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children']["category_{$level4_result['id']}"] = [];
										$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children']["category_{$level4_result['id']}"]['id'] = $level4_result['id'];
										$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children']["category_{$level4_result['id']}"]['name'] = html_entity_decode($level4_result['name']);
										$categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children']["category_{$level4_result['id']}"]['level'] = 4;
										// $categories["category_{$result['id']}"]['children']["category_{$level2_result['id']}"]['children']["category_{$level3_result['id']}"]['children']["category_{$level3_result['id']}"]['children'] = [];
									}
								}
							}
						}
					}
				}
			}
		}

		return $categories;
	}

	/**
	 * Get the categories for use in a React select element
	 *
	 * @return array
	 */
	public function getForSelect()
	{
		return $this->getAllHierarchical();
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
