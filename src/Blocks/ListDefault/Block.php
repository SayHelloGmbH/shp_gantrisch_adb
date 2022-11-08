<?php

namespace SayHello\ShpGantrischAdb\Blocks\ListDefault;

class Block
{
	public function run()
	{
		add_action('init', [$this, 'register']);
		add_action('acf/init', [$this, 'registerFields']);
	}

	public function register()
	{
		register_block_type(dirname(__FILE__) . '/block.json');
	}

	public function registerFields()
	{
		if (function_exists('acf_add_local_field_group')) :

			acf_add_local_field_group(array(
				'key' => 'group_6357d9b8a7fc5',
				'title' => 'ACF Block: ADB List',
				'fields' => array(
					array(
						'key' => 'field_6357db5a75ddb',
						'label' => 'Image size',
						'name' => 'image_size',
						'aria-label' => '',
						'type' => 'select',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'small' => 'Small',
							'medium' => 'Medium',
							'large' => 'Large',
							'full' => 'Full',
						),
						'default_value' => 'medium',
						'return_format' => 'value',
						'multiple' => 0,
						'allow_null' => 0,
						'ui' => 0,
						'ajax' => 0,
						'placeholder' => '',
					),
					array(
						'key' => 'field_6357d9b9fc60d',
						'label' => 'Button Text',
						'name' => 'button_text',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Entdecken',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_6357fad3546d8',
						'label' => 'Load more text',
						'name' => 'load_more_text',
						'aria-label' => '',
						'type' => 'text',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 'Mehr Angebote laden',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_6357fa9d418ff',
						'label' => 'Initial number of entries',
						'name' => 'initial_count',
						'aria-label' => '',
						'type' => 'number',
						'instructions' => 'How many entries should be shown before the "load more” button?',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 12,
						'min' => 1,
						'max' => 120,
						'placeholder' => '',
						'step' => '',
						'prepend' => '',
						'append' => '',
					),
					[
						'key' => 'field_636a3e8d88e00',
						'label' => _x('Show filter', 'ACF field label', 'shp_gantrisch_adb'),
						'name' => 'adb_show_filter',
						'type' => 'true_false',
						'instructions' => _x('Show or hide the list filter function for site visitors.', 'ACF field label', 'shp_gantrisch_adb'),
						'default_value' => 1,
						'ui' => 1,
						'ui_on_text' => _x('Show', 'ACF field label', 'shp_gantrisch_adb'),
						'ui_off_text' => _x('Hide', 'ACF field label', 'shp_gantrisch_adb'),
					],
					[
						'key' => 'field_635fe86408687',
						'label' => _x('Keywords', 'ACF field label', 'shp_gantrisch_adb'),
						'name' => 'adb_keywords',
						'type' => 'textarea',
						'instructions' => _x('Individual words (separated by spaces) will be regarded as individual keywords. A single keyword containing spaces is not supported. (This is a restriction imposed by the API.) Displays offers with keyword “A” OR keyword “B”, AND the selected category.', 'ACF field label', 'shp_gantrisch_adb'),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => [
							'width' => '',
							'class' => '',
							'id' => '',
						],
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => 4,
						'new_lines' => '',
					],
					array(
						'key' => 'field_6357fafbea9ab',
						'label' => 'Categories',
						'name' => 'adb_categories',
						'aria-label' => '',
						'type' => 'checkbox',
						'instructions' => 'Filter list by category.',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(),
						'default_value' => array(),
						'return_format' => 'value',
						'allow_custom' => 0,
						'layout' => 'vertical',
						'toggle' => 0,
						'save_custom' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'block',
							'operator' => '==',
							'value' => 'all',
						),
					),
					array(
						array(
							'param' => 'block',
							'operator' => '==',
							'value' => 'all',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
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
}
