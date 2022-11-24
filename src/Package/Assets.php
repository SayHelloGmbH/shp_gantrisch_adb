<?php

namespace SayHello\ShpGantrischAdb\Package;

/**
 * Assets (CSS, JavaScript etc)
 *
 * @author Mark Howells-Mead <mark@sayhello.ch>
 */
class Assets
{

	public $theme_url = '';
	public $theme_path = '';

	public function run()
	{
		add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
	}

	public function registerAssets()
	{

		$js_deps = [];
		$min = defined('WP_DEBUG') && WP_DEBUG ? false : true;

		wp_enqueue_script('shp_gantrisch_adb-ui', shp_gantrisch_adb_get_instance()->url . 'assets/scripts/ui' . ($min ? '.min' : '') . '.js', $js_deps, filemtime(shp_gantrisch_adb_get_instance()->path . 'assets/scripts/ui' . ($min ? '.min' : '') . '.js'), true);
		wp_localize_script('shp_gantrisch_adb-ui', 'shp_gantrisch_adb', [
			'debug' => defined('WP_DEBUG') && WP_DEBUG,
			'url' => untrailingslashit(shp_gantrisch_adb_get_instance()->url),
			'version' => filemtime(shp_gantrisch_adb_get_instance()->path . 'assets/scripts/ui' . ($min ? '.min' : '') . '.js')
		]);

		wp_enqueue_style('shp_gantrisch_adb-ui', shp_gantrisch_adb_get_instance()->url . 'assets/styles/ui' . ($min ? '.min' : '') . '.css', [], filemtime(shp_gantrisch_adb_get_instance()->path . 'assets/styles/ui' . ($min ? '.min' : '') . '.css'));
	}
}
