<?php

namespace RunyanCo;

if (! class_exists('baseline')) {
	class Baseline
	{
		/**
		 * Uri to jQuery using CDN
		 *
		 * @var string
		 */
		const JQUERY_CDN_URI = "https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js";

		/**
		 * @var string
		 */
		const TEXT_DOMAIN = "baseline";

		/**
		 * @var bool
		 */
		private $isInitialized = false;

		/**
		 * @var string
		 */
		protected $baselineDirectory;

		/**
		 * @var string
		 */
		protected $pluginsDirectory;

		/**
		 * Items added here are parsed and printed to the browser's console
		 *
		 * @var array
		 */
		static $debugItems = [];

		/**
		 * Constructor
		 *
		 * @param  string  $directory
		 */
		public function __construct(string $directory)
		{
			$this->baselineDirectory = $directory;
			$this->pluginsDirectory = dirname($directory);
		}

		/**
		 * Primary init point for this plugin
		 *
		 * @return void
		 */
		public function initialize()
		{
			if (! $this->isInitialized) {

				$this->disableGutenburg();

				$this->cleanupDashboard();

				$this->disallowFileEditingFromBrowser();

				$this->implementSoilPluginSupport();

				$this->isInitialized = true;
			}
		}

		/**
		 * Debug tool
		 *
		 * @param  array  $debugItems
		 *
		 * @return void
		 */
		protected static function printToBrowserConsole(...$debugItems)
		{
			static::$debugItems = $debugItems;

			add_action('admin_head', function () {
				foreach (static::$debugItems as $printedAsString) {
					try { print '<script> console.log(\''. ($printedAsString ?? 'null') .'\') </script>'; }
					catch (\Exception $exception) {}
				}
			});
		}

		/**
		 * Add simple admin notices when plugin is activated and deactived
		 *
		 * @param  string  $type
		 * @param  string  $message
		 *
		 * @return void
		 */
		protected function addAdminNotice(string $type, string $message)
		{
			$this->noticeType = $type;
			$this->noticeMessage = $message;

			add_action('admin_notices', function () {
				$this->formatAdminNotice();
			});
		}

		/**
		 * Returns the formatted string (the markup of) the admin notice.
		 * Types include: error, info, warning, success
		 *
		 * @return int
		 */
		protected function formatAdminNotice()
		{
			if (isset($this->noticeType) && isset($this->noticeMessage)) {

				if (! array_search($this->noticeType, ['error', 'info', 'warning', 'success'])) {
					$this->noticeType = 'info';
				}

				return printf(
					'<div class="%1$s"><p>%2$s</p></div>',
					esc_attr('notice is-dismissible notice-'. $this->noticeType),
					esc_html(__($this->noticeMessage, self::TEXT_DOMAIN))
				);
			}

			return printf('');
		}

		/**
		 * Tighten security up a bit. More to come in this method.
		 *
		 * @return void
		 */
		protected function disallowFileEditingFromBrowser()
		{
			add_action('init', function() {
				if(! defined('DISALLOW_FILE_EDIT') ) {
					define('DISALLOW_FILE_EDIT', true );
				}
			});
		}

		/**
		 * Removes unnecessary widgets and admin bar elements for a cleaner look and improved functioning
		 *
		 * @link https://codex.wordpress.org/Class_Reference/WP_Admin_Bar/add_menu
		 * @link https://github.com/vincentorback/clean-wordpress-admin/blob/master/admin-bar.php
		 *
		 * @return void
		 */
		protected function cleanupDashboard()
		{
			add_action('wp_before_admin_bar_render', function () {

				global $wp_admin_bar;

				$wp_admin_bar->remove_menu('wp-logo');        // Remove the WordPress logo
				$wp_admin_bar->remove_menu('about');          // Remove the about WordPress link
				$wp_admin_bar->remove_menu('wporg');          // Remove the about WordPress link
				$wp_admin_bar->remove_menu('documentation');  // Remove the WordPress documentation link
				$wp_admin_bar->remove_menu('support-forums'); // Remove the support forums link
				$wp_admin_bar->remove_menu('feedback');       // Remove the feedback link
				$wp_admin_bar->remove_menu('updates');        // Remove the updates link
				$wp_admin_bar->remove_menu('comments');       // Remove the comments link

			}, 999);

			add_action('admin_init', function() {

				remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
				remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
				remove_meta_box('dashboard_primary', 'dashboard', 'side');
				remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
				remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
				remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
				remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
				remove_meta_box('dashboard_activity', 'dashboard', 'normal');

				add_action('admin_head', function() {
					echo '<script src="'. self::JQUERY_CDN_URI .'"></script>';
				});

			});
		}

		/**
		 * Disables the default Gutenburg editor which comes with WordPress >= v5.*
		 *
		 * @param  bool  $disabledByDefault
		 *
		 * @return void
		 */
		protected function disableGutenburg(bool $disabledByDefault = false)
		{
			if ($disabledByDefault) {
				add_action('admin_init', function () {
					wp_deregister_style('wp-block-library');
				});
			}

			add_filter('gutenberg_can_edit_post_type', function( $can_edit, $post_type ) {
				return false;
			});
		}

		/**
		 * Checks for an already installed version of Soil
		 *
		 * @return mixed
		 */
		protected function checkIfSoilIsActive()
		{
			return in_array(($this->pluginsDirectory . '/soil/soil.php'), apply_filters('active_plugins', get_option('active_plugins')));
		}

		/**
		 * Enable features from Soil when plugin is activated
		 *
		 * @link https://roots.io/plugins/soil/
		 *
		 * @return void
		 */
		protected function implementSoilPluginSupport()
		{
			if ($this->checkIfSoilIsActive()) {
				add_action('after_setup_theme', function () {
					add_theme_support('soil-clean-up');
					add_theme_support('soil-disable-rest-api');
					add_theme_support('soil-nice-search');
					add_theme_support('soil-relative-urls');
					add_theme_support('soil-js-to-footer');
					add_theme_support('soil-google-analytics', 'UA-142130107-1');
					add_theme_support('soil-disable-asset-versioning');
					add_theme_support('soil-disable-trackbacks');
					add_theme_support('soil-nav-walker');
				});
			}
		}
	}
}
