<?php

namespace SayHello\ShpGantrischAdb\Package;

/**
 * Functionality for getting data from the
 * Angebotsdatenbank from Swiss Parks
 *
 * @author Say Hello GmbH <hello@sayhello.ch>
 */

class Fetch
{

	private $crontask = '';
	private $prefix = '';

	public function run()
	{
		register_activation_hook(shp_gantrisch_adb_get_instance()->file, [$this, 'activation']);
		register_deactivation_hook(shp_gantrisch_adb_get_instance()->file, [$this, 'deactivation']);

		$this->prefix = shp_gantrisch_adb_get_instance()->prefix;
		$this->crontask = "{$this->prefix}_update_from_api";

		add_action('acf/init', [$this, 'optionsPage']);
		add_action('acf/init', [$this, 'optionsFields']);

		add_action($this->crontask, [$this, 'updateFromApi']);
	}

	public function activation()
	{
		if (!wp_next_scheduled($this->crontask)) {
			wp_schedule_event(strtotime('01:00:00'), 'daily', $this->crontask);
		}
	}

	public function deactivation()
	{
		wp_unschedule_event(time(), $this->crontask);
		wp_clear_scheduled_hook($this->crontask);
	}

	public function updateFromApi()
	{
		$url = shp_gantrisch_adb_get_instance()->url;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "{$url}vendor/parks_api/scripts/cron.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_exec($ch);
		curl_close($ch);
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
}
