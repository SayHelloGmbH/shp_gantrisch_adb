<?php

namespace SayHello\ShpGantrischAdb\Package;

/**
 * Gutenberg Editor
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Gutenberg
{
	public $min = true;

	public function __construct()
	{
		$this->min = !(defined('WP_DEBUG') && WP_DEBUG);
	}

	public function run()
	{
		add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockAssets']);
		add_action('init', [$this, 'setScriptTranslations']);
	}

	public function enqueueBlockAssets()
	{
		$script_asset_path = shp_gantrisch_adb_get_instance()->path . 'assets/gutenberg/blocks.asset.php';
		$script_asset = file_exists($script_asset_path) ? require($script_asset_path) : ['dependencies' => [], 'version' => filemtime($script_asset_path)];
		wp_enqueue_script(
			'shp_gantrisch_adb-gutenberg-script',
			shp_gantrisch_adb_get_instance()->url . 'assets/gutenberg/blocks' . ($this->min ? '.min' : '') . '.js',
			$script_asset['dependencies'],
			filemtime($script_asset_path)
		);
	}

	/**
	 * https://github.com/SayHelloGmbH/hello-roots/wiki/Translation-in-JavaScript
	 *
	 * Make sure that the JSON files are at e.g.
	 * 'languages/sht-de_DE_formal-739d784e82179214dfd2a6c345374e30.json' or
	 * 'languages/sht-fr_FR-739d784e82179214dfd2a6c345374e30.json'
	 *
	 * mhm 28.1.2020
	 */
	public function setScriptTranslations()
	{
		wp_set_script_translations('sht-gutenberg-script', 'sht', get_template_directory() . '/languages');
	}
}
