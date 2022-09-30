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
}
