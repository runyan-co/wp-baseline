<?php

namespace RunyanCo\WpBaseline\Modules;

class DisableTrackbacks extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		add_filter('xmlrpc_methods', function ($input) {
			$this->filterXmlrpcMethod($input);
		}, 10, 1);

		add_filter('wp_headers', function ($input) {
			$this->filterHeaders($input);
		}, 10, 1);

		add_filter('rewrite_rules_array', function ($input) {
			$this->filterRewrites($input);
		});

		add_filter('bloginfo_url', function ($output, $show) {
			$this->killPingbackUrl($output, $show);
		}, 10, 2);

		add_action('xmlrpc_call', function ($input) {
			$this->killXmlrpc($input);
		});
	}

	/**
	 * Disable pingback XMLRPC method
	 *
	 * @param $methods
	 *
	 * @return mixed
	 */
	public function filterXmlrpcMethod($methods)
	{
		array_pop($methods['pingback.ping'] );

		return $methods;
	}

	/**
	 * Remove pingback header
	 *
	 * @param $headers
	 *
	 * @return mixed
	 */
	public function filterHeaders($headers)
	{
		if (isset($headers['X-Pingback'])) {
			array_pop($headers['X-Pingback']);
		}

		return $headers;
	}

	/**
	 * Kill trackback rewrite rule
	 *
	 * @param $rules
	 *
	 * @return mixed
	 */
	public function filterRewrites($rules)
	{
		foreach ($rules as $rule => $rewrite) {
			if (preg_match('/trackback\/\?\$$/i', $rule)) {
				array_pop($rules[$rule]);
			}
		}

		return $rules;
	}

	/**
	 * Kill bloginfo('pingback_url')
	 *
	 * @param $output
	 * @param $show
	 *
	 * @return string
	 */
	public function killPingbackUrl($output, $show)
	{
		if ($show === 'pingback_url') {
			$output = '';
		}

		return $output;
	}

	/**
	 * Disable XMLRPC call
	 *
	 * @param $action
	 */
	public function killXmlrpc($action)
	{
		if ($action === 'pingback.ping') {
			wp_die('Pingbacks are not supported', 'Not Allowed!', ['response' => 403]);
		}
	}

}

