<?php

namespace RunyanCo\WpBaseline\Modules;

class CleanUpSearch extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		add_filter('wpseo_json_ld_search_url', function ($input) {
			$this->rewrite($input);
		});

		add_action('template_redirect', function () {
			$this->redirect();
		});
	}

	/**
	 * Redirects search results from /?s=query to /search/query/, converts %20 to +
	 *
	 * @link http://txfx.net/wordpress-plugins/nice-search/
	 */
	public function redirect()
	{
		global $wp_rewrite;

		if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->get_search_permastruct()) {
			return;
		}

		$search_base = $wp_rewrite->search_base;

		if ( ! is_admin()
			&& is_search()
			&& strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false
			&& strpos($_SERVER['REQUEST_URI'], '&') === false) {

			wp_redirect(
				get_search_link()
			);

			exit();
		}
	}

	/**
	 * @param $url
	 *
	 * @return string|string[]
	 */
	public function rewrite($url)
	{
		return str_replace('/?s=', '/search/', $url);
	}
}
