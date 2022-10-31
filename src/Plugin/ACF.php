<?php

namespace SayHello\ShpGantrischAdb\Plugin;

use SayHello\ShpGantrischAdb\Model\Category as CategoryModel;

class ACF
{
	public function run()
	{
		add_action('acf/init', [$this, 'optionsPage']);
		add_action('acf/init', [$this, 'optionsFields']);
		add_action('acf/load_field/name=adb_categories', [$this, 'adbCategories']);
	}

	public function optionsPage()
	{
		if (function_exists('acf_add_options_sub_page')) {
			acf_add_options_sub_page(
				[
					'page_title'  => __('Angebotsdatenbank', 'sha'),
					'menu_title'  => __('Angebotsdatenbank', 'sha'),
					'menu_slug'   => 'shp_gantrisch_adb_options',
					'parent_slug' => 'options-general.php',
					'capability'  => 'edit_theme_options',
				]
			);
		}
	}

	public function optionsFields()
	{
		if (function_exists('acf_add_local_field_group')) :

			acf_add_local_field_group(array(
				'key' => 'group_63232863a7864',
				'title' => 'Einstellungen Angebotsdatenbank',
				'fields' => array(
					array(
						'key' => 'field_632329c8da053',
						'label' => 'API Hash',
						'name' => 'shp_gantrisch_adb_api_hash',
						'type' => 'text',
						'instructions' => 'Corresponds to the part of the relevant XML path at https://angebote.paerke.ch/settings.',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_6323294348e01',
						'label' => 'Park ID',
						'name' => 'shp_gantrisch_adb_park_id',
						'type' => 'number',
						'instructions' => 'Corresponds to “My park” at https://angebote.paerke.ch/.',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_63249d2b82bbc',
						'label' => 'Single view page',
						'name' => 'shp_gantrisch_adb_single_page',
						'type' => 'post_object',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'page',
						),
						'taxonomy' => '',
						'allow_null' => 0,
						'multiple' => 0,
						'return_format' => 'id',
						'ui' => 1,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'shp_gantrisch_adb_options',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
				'show_in_rest' => 0,
			));

		endif;
	}

	public function adbCategories($field)
	{
		$category_model = new CategoryModel();
		$choices = $category_model->getForSelect();

		if (is_array($choices)) {
			$field['choices'] = $choices;
		}

		return $field;
	}
}
