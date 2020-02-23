<?php

namespace RunyanCo\WpBaseline\Modules;

class TransformUrlsToRelative extends Module
{
	/**
	 * @var array
	 */
	public static $toFilter = [
		'bloginfo_url',
		'the_permalink',
		'wp_list_pages',
		'wp_list_categories',
		'wp_get_attachment_url',
		'the_content_more_link',
		'the_tags',
		'get_pagenum_link',
		'get_comment_link',
		'month_link',
		'day_link',
		'year_link',
		'term_link',
		'the_author_posts_link',
		'script_loader_src',
		'style_loader_src',
		'theme_file_uri',
		'parent_theme_file_uri',
	];

	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		add_action('the_seo_framework_do_before_output', function () {
			remove_filter('wp_get_attachment_url', function () {
				$this->transformUrlsToRelative();
			});
		});

		add_action('the_seo_framework_do_after_output', function () {
			add_filter('wp_get_attachment_url', function () {
				$this->transformUrlsToRelative();
			});
		});
	}

	/**
	 * WordPress likes to use absolute URLs on everything - let's clean that up.
	 *
	 * @return void
	 */
	public function transformUrlsToRelative()
	{
		if ((! wp_doing_ajax()
			&& is_admin())
			|| isset($_GET['sitemap'])
			|| in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php'])) {

			return;
		}

		$filters = apply_filters('relative-url-filters', static::$toFilter);

		$this->addFilters($filters, function ($input) {
			$this->rootRelativeUrl($input);
		});

		add_filter('wp_calculate_image_srcset', function ($sources) {
			foreach ((array) $sources as $source => $src) {
				$sources[$source]['url'] = $this->rootRelativeUrl($src['url']);
			}

			return $sources;
		});

		$this->addFilters($filters, function ($input) {
			$this->rootRelativeUrl($input);
		});

		add_filter('wp_calculate_image_srcset', function ($sources) {
			foreach ((array) $sources as $source => $src) {
				$sources[$source]['url'] = $this->rootRelativeUrl($src['url']);
			}

			return $sources;
		});
	}

	/**
	 * @param $input
	 *
	 * @return string
	 */
	private function rootRelativeUrl($input)
	{
		if (is_feed()) {
			return $input;
		}

		$url = parse_url($input);

		if (!isset($url['host']) || !isset($url['path'])) {
			return $input;
		}

		$site_url = parse_url(network_home_url());

		if (!isset($url['scheme'])) {
			$url['scheme'] = $site_url['scheme'];
		}

		$hosts_match = $site_url['host'] === $url['host'];
		$schemes_match = $site_url['scheme'] === $url['scheme'];
		$ports_exist = isset($site_url['port']) && isset($url['port']);
		$ports_match = ($ports_exist) ? $site_url['port'] === $url['port'] : true;

		if ($hosts_match && $schemes_match && $ports_match) {
			return wp_make_link_relative($input);
		}

		return $input;
	}

}
