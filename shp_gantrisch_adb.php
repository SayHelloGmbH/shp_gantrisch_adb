<?php
/*
 * Plugin Name:       Offer database for Naturpark Gantrisch
 * Plugin URI:        https://bitbucket.org/sayhellogmbh/shp_gantrisch_adb/
 * Description:       Plugin for the output of offers from the Swiss Parks database.
 * Author:            Say Hello GmbH
 * Version:           0.15.5
 * Author URI:        https://sayhello.ch/
 * Text Domain:       shp_gantrisch_adb
 * Domain Path:       /languages
 * Requires at least: 6.1.1
 * Requires PHP:      8.0
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://sayhello.ch/
 */


if (!function_exists('dump')) {
	function dump($var, $exit = false, $print_r = false)
	{
		echo '<pre>';

		if ($print_r) {
			print_r($var);
		} else {
			var_dump($var);
		}

		echo '</pre>';

		if ($exit) {
			exit;
		}
	}
}

spl_autoload_register(function ($class) {

	// project-specific namespace prefix
	$prefix = 'SayHello\\ShpGantrischAdb\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/src/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

function shp_gantrisch_adb_get_instance()
{
	return SayHello\ShpGantrischAdb\Plugin::getInstance(__FILE__);
}

shp_gantrisch_adb_get_instance();
