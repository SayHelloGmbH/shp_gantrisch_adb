<?php

namespace SayHello\ShpGantrischAdb;

class Plugin
{
	private static $instance;
	public $name = '';
	public $prefix = '';
	public $version = '';
	public $file = '';
	public $path = '';
	public $url = '';

	/**
	 * Loads and initializes the provided classes.
	 *
	 * @param $classes
	 */
	private function loadClasses($classes)
	{
		foreach ($classes as $class) {
			$class_parts = explode('\\', $class);
			$class_short = end($class_parts);
			$class_set   = $class_parts[count($class_parts) - 2];

			if (!isset(shp_gantrisch_adb_get_instance()->{$class_set}) || !is_object(shp_gantrisch_adb_get_instance()->{$class_set})) {
				shp_gantrisch_adb_get_instance()->{$class_set} = new \stdClass();
			}

			if (property_exists(shp_gantrisch_adb_get_instance()->{$class_set}, $class_short)) {
				wp_die(sprintf(__('A problem has ocurred in the Theme. Only one PHP class named “%1$s” may be assigned to the “%2$s” object in the Theme.', 'sht'), $class_short, $class_set), 500);
			}

			shp_gantrisch_adb_get_instance()->{$class_set}->{$class_short} = new $class();

			if (method_exists(shp_gantrisch_adb_get_instance()->{$class_set}->{$class_short}, 'run')) {
				shp_gantrisch_adb_get_instance()->{$class_set}->{$class_short}->run();
			}
		}
	}

	/**
	 * Creates an instance if one isn't already available,
	 * then return the current instance.
	 *
	 * @param  string $file The file from which the class is being instantiated.
	 * @return object       The class instance.
	 */
	public static function getInstance($file)
	{
		if (!isset(self::$instance) && !(self::$instance instanceof Plugin)) {
			self::$instance = new Plugin;

			if (!function_exists('get_plugin_data')) {
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}

			$data = get_plugin_data($file);

			self::$instance->name = $data['Name'];
			self::$instance->prefix = 'shp_gantrisch_adb';
			self::$instance->version = $data['Version'];
			self::$instance->file = $file;
			self::$instance->path = plugin_dir_path($file);
			self::$instance->url = plugin_dir_url($file);

			self::$instance->run();
		}
		return self::$instance;
	}

	/**
	 * Execution function which is called after the class has been initialized.
	 * This contains hook and filter assignments, etc.
	 */
	private function run()
	{
		register_activation_hook(shp_gantrisch_adb_get_instance()->file, [$this, 'activation']);
		register_deactivation_hook(shp_gantrisch_adb_get_instance()->file, [$this, 'deactivation']);

		// Load individual pattern classes which contain
		// grouped functionality. E.g. everything to do with a post type.
		// LOADING ORDER IS CRITICAL
		$this->loadClasses(
			[
				Model\Offer::class,
				Model\Category::class,

				Controller\API::class,
				Controller\Block::class,
				Controller\Category::class,
				Controller\Offer::class,

				Package\Assets::class,
				Package\Gutenberg::class,
				Package\Fetch::class,
				Package\Rewrites::class,

				Plugin\ACF::class,
				Plugin\Yoast::class,

				Blocks\AccordionDetails\Block::class,
				Blocks\AccordionRoute\Block::class,
				Blocks\AccordionTravel\Block::class,
				Blocks\ListDefault\Block::class,
				// Blocks\OfferBenefits\Block::class, // In details accordion 17.11.2022
				Blocks\OfferCategories\Block::class,
				Blocks\OfferCondition\Block::class,
				Blocks\OfferContact\Block::class,
				Blocks\OfferDescriptionLong\Block::class,
				Blocks\OfferDocuments\Block::class,
				Blocks\OfferExcerpt\Block::class,
				Blocks\OfferEventLocation\Block::class,
				Blocks\OfferImages\Block::class,
				Blocks\OfferLinks\Block::class,
				// Blocks\OfferMap\Block::class, // In development 30.11.2022
				// Blocks\OfferInfrastructure\Block::class, // In details accordion 17.11.2022
				// Blocks\OfferPrice\Block::class, // In details accordion 17.11.2022
				// Blocks\OfferKeywords\Block::class, // Not for output on the site - Raphael 22.9.2022
				// Blocks\OfferSeason\Block::class, // In details accordion 17.11.2022
				Blocks\OfferRouteLength\Block::class,
				Blocks\OfferSameCategory\Block::class,
				Blocks\OfferSubscription\Block::class,
				// Blocks\OfferTarget\Block::class, // In details accordion 17.11.2022
				Blocks\OfferTransportStop\Block::class,
				Blocks\OfferTime\Block::class,
				Blocks\OfferTimeTermin\Block::class,
				Blocks\OfferTitle\Block::class,
			]
		);

		add_action('init', [$this, 'autoloadAPI']);
		add_action('plugins_loaded', [$this, 'loadPluginTextdomain']);
		add_action('after_setup_theme', [$this, 'themeSupports']);
	}

	public function autoloadAPI()
	{
		$dir = dirname(shp_gantrisch_adb_get_instance()->file);
		require_once("{$dir}/vendor/parks_api/autoload.php");
	}

	public function activation()
	{
		flush_rewrite_rules(true);
	}

	public function deactivation()
	{
		flush_rewrite_rules(false);
	}

	/**
	 * Load translation files from the indicated directory.
	 */
	public function loadPluginTextdomain()
	{
		load_plugin_textdomain('shp_gantrisch_adb', false, dirname(plugin_basename(shp_gantrisch_adb_get_instance()->file)) . '/languages');
	}

	public function themeSupports()
	{
		add_theme_support('title-tag');
	}
}
