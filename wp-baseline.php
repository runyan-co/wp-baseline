<?php

namespace RunyanCo\WpBaseline;

/**
 * Plugin Name:     WP Baseline
 * Description:     A few basic improvements for WordPress
 * Author:          Alex Runyan <alex@runyan.co>
 * Text Domain:     wp-baseline
 * Domain Path:     /languages
 * Version:         0.5.0
 * License:         MIT
 *
 * @package         runyan-co/wp-baseline
 * @link            https://github.com/runyan-co/wp-baseline
 * @note            Big thanks to the roots/soil package which I've used to inspire many components here
 */

if (! defined('WP_PLUGINS_PATH')) {
	define('WP_PLUGINS_PATH', dirname(__FILE__));
}

if (! defined('WP_BASELINE_PLUGIN_PATH')) {
	define('WP_BASELINE_PLUGIN_PATH', plugin_dir_path(__FILE__));
}

if (! defined('WP_BASELINE_AUTOLOADER')) {
	define('WP_BASELINE_AUTOLOADER', WP_BASELINE_PLUGIN_PATH . '/vendor/autoload.php');
}

if (! defined('WP_BASELINE_TEXT_DOMAIN')) {
	define('WP_BASELINE_TEXT_DOMAIN', 'wp-baseline');
}

if (file_exists(WP_BASELINE_AUTOLOADER)) {
	if (require WP_BASELINE_AUTOLOADER) {

		$bootstrapWpBaseline = once(function () {
			return new Bootstrap();
		});

		if (! $bootstrapWpBaseline->modulesLoaded) {
			$bootstrapWpBaseline->loadModules();
		}

	}
}
