<?php

namespace RunyanCo\WpBaseline\Modules;

class CleanUp extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		add_action('wp_before_admin_bar_render', function () {
			$this->cleanAdminBar();
		}, 99);

		add_action('init', function () {
			$this->cleanupHead();
		});

		add_action('wp_before_admin_bar_render', function () {
			$this->cleanAdminBar();
		});

		add_filter('language_attributes', function () {
			$this->cleanupLanguageAttributes();
		});

		add_action('admin_head', function () {
			$this->cleanupWidgets();
		});

		/**
		 * @todo Style cleaning method causing plugin conflicts (namely with Query Monitor)
		 */
//		add_filter('style_loader_tag', function ($input) {
//			$this->cleanStyleTag($input);
//		});

		/**
		 * @todo Script cleaning method causing plugin conflicts (namely with Query Monitor)
		 */
//		add_filter('script_loader_tag', function ($input) {
//			$this->cleanScriptTag($input);
//		});

		add_filter('get_avatar', function ($input) {
			$this->removeSelfClosingTags($input);
		});

		add_filter('body_class', function ($input) {
			$this->adjustBodyClasses($input);
		});

		add_filter('embed_oembed_html', function ($input) {
			$this->embedWrap($input);
		});

		add_filter('comment_id_fields', function ($input) {
			$this->removeSelfClosingTags($input);
		});

		add_filter('post_thumbnail_html', function ($input) {
			$this->removeSelfClosingTags($input);
		});

		add_filter('get_bloginfo_rss', function ($input) {
			$this->removeDefaultDescription($input);
		});

		add_filter('the_generator', function () {
			return false;
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
	public function cleanupWidgets()
	{
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
		remove_meta_box('dashboard_primary', 'dashboard', 'side');
		remove_meta_box('dashboard_secondary', 'dashboard', 'normal');
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
	}

	/**
	 * Add and remove body_class() classes
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function adjustBodyClasses($classes)
	{
		// Add post/page slug if not present
		if ((is_single() || is_page()) && ! is_front_page()) {
			if (!in_array(basename(get_permalink()), $classes)) {
				$classes[] = basename(get_permalink());
			}
		}

		// Remove unnecessary classes
		$home_id_class = 'page-id-' . get_option('page_on_front');

		$remove_classes = [
			'page-template-default',
			$home_id_class
		];

		$classes = array_diff($classes, $remove_classes);

		return $classes;
	}

	/**
	 * Wrap embedded media as suggested by Readability
	 *
	 * @link https://gist.github.com/965956
	 * @link http://www.readability.com/publishers/guidelines#publisher
	 *
	 * @param $cache
	 *
	 * @return string
	 */
	public function embedWrap($cache)
	{
		return '<div class="entry-content-asset">' . $cache . '</div>';
	}

	/**
	 * Remove unnecessary self-closing tags
	 *
	 * @param $input
	 *
	 * @return string|string[]
	 */
	public function removeSelfClosingTags(string $input)
	{
		return str_replace(' />', '>', $input);
	}

	/**
	 * Don't return the default description in the RSS feed if it hasn't been changed
	 *
	 * @param $bloginfo
	 *
	 * @return string
	 */
	public function removeDefaultDescription(string $bloginfo)
	{
		return ($bloginfo === 'Just another WordPress site') ? '' : $bloginfo;
	}

	/**
	 * Clean up language_attributes() used in <html> tag
	 * Remove dir="ltr"
	 *
	 * @return mixed|string|void
	 */
	public function cleanupLanguageAttributes()
	{
		$attributes = [];

		if (is_rtl()) {
			$attributes[] = 'dir="rtl"';
		}

		$lang = get_bloginfo('language');

		if ($lang) {
			$attributes[] = "lang=\"$lang\"";
		}

		$output = implode(' ', $attributes);
		$output = apply_filters('soil/language_attributes', $output);

		return $output;
	}

	/**
	 * Removes unneeded menu items from the admin bar
	 *
	 * @return void
	 */
	public function cleanAdminBar()
	{
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu('wp-logo');        // Remove the WordPress logo
		$wp_admin_bar->remove_menu('about');          // Remove the about WordPress link
		$wp_admin_bar->remove_menu('wporg');          // Remove the about WordPress link
		$wp_admin_bar->remove_menu('documentation');  // Remove the WordPress documentation link
		$wp_admin_bar->remove_menu('support-forums'); // Remove the support forums link
		$wp_admin_bar->remove_menu('feedback');       // Remove the feedback link
		$wp_admin_bar->remove_menu('updates');        // Remove the updates link
		$wp_admin_bar->remove_menu('comments');       // Remove the comments link
	}

	/**
	 * Clean up output of stylesheet <link> tags
	 *
	 * @param $input
	 *
	 * @return string
	 */
	public function cleanStyleTag($input)
	{
		preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input,$matches);

		if (empty($matches[2])) {
			return $input;
		}

		// Only display media if it is meaningful
		$media = ($matches[3][0] !== '' && $matches[3][0] !== 'all')
			? ' media="' . $matches[3][0] . '"'
			: '';

		return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
	}

	/**
	 * Clean up output of <script> tags
	 *
	 * @param $input
	 *
	 * @return string|string[]
	 */
	public function cleanScriptTag($input)
	{
		$input = str_replace("type='text/javascript' ", '', $input);

		$input = \preg_replace_callback(
			'/document.write\(\s*\'(.+)\'\s*\)/is',
			function ($m) {
				return str_replace($m[1], addcslashes($m[1], '"'), $m[0]);
			},
			$input
		);

		return str_replace("'", '"', $input);
	}

	/**
	 * Clean up wp_head()
	 *
	 * Remove unnecessary <link>'s
	 * Remove inline CSS and JS from WP emoji support
	 * Remove inline CSS used by Recent Comments widget
	 * Remove inline CSS used by posts with galleries
	 * Remove self-closing tag
	 *
	 * @return void
	 */
	public function cleanupHead()
	{
		add_action('wp_head', 'ob_start', 1, 0);

		add_action('wp_head', function () {
			$pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
			echo preg_replace($pattern, '', ob_get_clean());
		}, 3, 0);

		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head', 10);
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		remove_action('wp_head', 'wp_oembed_add_host_js');
		remove_action('wp_head', 'rest_output_link_wp_head', 10);
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

		add_filter('use_default_gallery_style', '__return_false');
		add_filter('emoji_svg_url', '__return_false');
		add_filter('show_recent_comments_widget_style', '__return_false');
	}
}
