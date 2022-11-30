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
	}

	public function enqueueBlockAssets()
	{
		$script_key = 'shp_gantrisch_adb-gutenberg-script';

		// Load the main JS file for the editor
		$script_asset_path = shp_gantrisch_adb_get_instance()->path . 'assets/gutenberg/blocks.asset.php';
		$script_asset = file_exists($script_asset_path) ? require($script_asset_path) : ['dependencies' => [], 'version' => filemtime($script_asset_path)];
		wp_enqueue_script(
			$script_key,
			shp_gantrisch_adb_get_instance()->url . 'assets/gutenberg/blocks' . ($this->min ? '.min' : '') . '.js',
			$script_asset['dependencies'],
			filemtime($script_asset_path)
		);

		// Translations are bound to the script which has been loaded in the editor.
		wp_set_script_translations($script_key, 'shp_gantrisch_adb', dirname(plugin_basename(shp_gantrisch_adb_get_instance()->file)) . '/languages');
	}

	public function isContextEdit()
	{
		return array_key_exists('context', $_GET) && $_GET['context'] === 'edit';
	}
}
