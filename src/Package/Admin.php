<?php

namespace SayHello\ShpGantrischAdb\Package;

use WP_REST_Response;
use WP_REST_Server;
use WP_CLI;

class Admin
{
	public function run()
	{
		add_action('admin_menu', [$this, 'adminMenu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
		add_action('rest_api_init', [$this, 'registerRestRoutes']);

		$this->addCLICommands();
	}

	public function addCLICommands()
	{
		if (defined('WP_CLI') && WP_CLI) {
			WP_CLI::add_command('shp adb update', function () {

				$mt = microtime(true);
				WP_CLI::log('Starting ADB update…');
				$response = $this->updateFromApi();

				if ($response->get_status() !== 200) {
					WP_CLI::error($response->get_data());
					exit;
				}

				WP_CLI::log('ADB update done in ' . (microtime(true) - $mt) . ' seconds. 😁');
			});
		}
	}

	/**
	 * Adds a menu page in WordPress Admin
	 *
	 * @return void
	 */
	public function adminMenu()
	{
		add_submenu_page(
			'tools.php',
			_x('Administration Angebotsdatenbank', 'Admin page title', 'shp_gantrisch_adb'),
			_x('Angebotsdatenbank', 'Admin menu link text', 'shp_gantrisch_adb'),
			'manage_options',
			'shp_gantrisch_adb_settings',
			[$this, 'adminSettingsPage']
		);
	}

	/**
	 * Renders the admin settings page
	 *
	 * @return void
	 */
	public function adminSettingsPage()
	{

		$classNameBase = 'shp_gantrisch_adb';
?>
		<div class="wrap wrap-<?php echo $classNameBase; ?>">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<div class="<?php echo $classNameBase; ?>__wrapper">
				<h2><?php _e('ADB-Daten aktualisieren', 'shp_gantrisch_adb'); ?></h2>
				<p><?php _e('Klicken Sie auf diesen Button, um die aktuelle Daten aus der ADB-Schnittstelle zu laden und in die eigene Datenbank zu speichern.', 'shp_gantrisch_adb'); ?></p>
				<p><?php _e('Der Vorgang könnte einige Sekunden dauern, bitte haben Sie Geduld.', 'shp_gantrisch_adb'); ?></p>
				<div class="<?php echo $classNameBase; ?>__button-wrapper">
					<button class="button button-primary <?php echo $classNameBase; ?>__button" disabled data-shp_gantrisch_adb-doupdate data-text-wait="<?php _e('In Arbeit…', 'shp_gantrisch_adb'); ?>"><?php _e('Datenbank aus der Schnittstelle aktualisieren', 'shp_gantrisch_adb'); ?></button>
					<div data-shp_gantrisch_adb-api-response class="<?php echo $classNameBase; ?>__message is--hidden"></div>
				</div>
			</div>
	<?php
	}

	public function enqueueAdminScripts()
	{

		$screen = get_current_screen();
		if ($screen->id !== 'tools_page_shp_gantrisch_adb_settings') {
			return;
		}

		$min = defined('WP_DEBUG') && WP_DEBUG ? false : true;
		wp_enqueue_script('shp_gantrisch_adb-admin', shp_gantrisch_adb_get_instance()->url . 'assets/scripts/admin' . ($min ? '.min' : '') . '.js', ['jquery'], filemtime(shp_gantrisch_adb_get_instance()->path . 'assets/scripts/admin' . ($min ? '.min' : '') . '.js'), true);
		wp_localize_script('shp_gantrisch_adb-admin', 'shp_gantrisch_adb', [
			'debug' => defined('WP_DEBUG') && WP_DEBUG,
			'url' => get_rest_url(),
			'nonce' => wp_create_nonce('wp_rest'),
			'version' => filemtime(shp_gantrisch_adb_get_instance()->path . 'assets/scripts/admin' . ($min ? '.min' : '') . '.js')
		]);
	}

	public function registerRestRoutes()
	{
		register_rest_route('shp_gantrisch_adb', 'update-from-api', [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'updateFromApi'],
			'permission_callback' => function () {
				return current_user_can('manage_options');
			}
		]);
	}

	public function updateFromApi()
	{

		set_time_limit(600);

		$plugin_url = shp_gantrisch_adb_get_instance()->url;
		$response = wp_remote_get("{$plugin_url}/vendor/parks_api/scripts/cron.php", ['sslverify' => false, 'timeout' => 60]);

		if (is_wp_error($response)) {
			return new WP_REST_Response($response->get_error_message(), 500);
		}

		$status_code = wp_remote_retrieve_response_code($response);

		if ($status_code !== 200) {
			return new WP_REST_Response(sprintf(__('Die Daten konnten nicht von der ADB-Schnittstelle bezogen werden. (ADB-Server-Antwort HTTP %s.)', 'shp_gantrisch_adb'), $status_code), 500);
		}

		return new WP_REST_Response('👌', 200);
	}
}
