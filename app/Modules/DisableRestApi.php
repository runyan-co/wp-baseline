<?php

namespace RunyanCo\WpBaseline\Modules;

use WP_Error;

class DisableRestApi extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		remove_action('xmlrpc_rsd_apis', function () {
			return 'rest_output_rsd';
		});

		remove_action('template_redirect', function () {
			return 'rest_output_link_header';
		}, 11);

		remove_action('template_redirect', function () {
			return 'rest_output_link_header';
		}, 11);

		remove_action('wp_head', function () {
			return 'rest_output_link_wp_head';
		}, 10);

		add_filter('rest_authentication_errors', function ($input) {
			return $this->disableRestApi($input);
		});
	}

	/**
	 * Disable WordPress REST API
	 *
	 * @param $result
	 *
	 * @return \WP_Error
	 */
	public function disableRestApi($result)
	{
		return new WP_Error('rest_forbidden', __('REST API forbidden.', 'soil'), [
			'status' => rest_authorization_required_code()
		]);
	}
}
