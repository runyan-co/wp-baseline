<?php

namespace RunyanCo\WpBaseline\Modules;

class MoveJsToFooter extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		add_action('wp_enqueue_scripts', function () {
			$this->moveJsToFooter();
		});
	}

	/**
	 * Moves all scripts to wp_footer action
	 *
	 * @return void
	 */
	public function moveJsToFooter()
	{
		remove_action('wp_head', 'wp_print_scripts');
		remove_action('wp_head', 'wp_print_head_scripts', 9);
		remove_action('wp_head', 'wp_enqueue_scripts', 1);
	}
}
