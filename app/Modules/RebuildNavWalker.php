<?php

namespace RunyanCo\WpBaseline\Modules;

class RebuildNavWalker extends Module
{
	public function load()
	{
		add_filter('wp_nav_menu_args', function ($input) {
			$this->filterMenuArguments($input);
		});
	}

	/**
	 * Clean up wp_nav_menu_args
	 *
	 * - Remove the container
	 * - Remove the id="" on nav menu items
	 *
	 * @param  $args
	 *
	 * @return array
	 */
	public function filterMenuArguments($args = '')
	{
		$nav_menu_args = [];
		$nav_menu_args['container'] = false;

		if (! $args['items_wrap']) {
			$nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
		}

		if (! $args['walker']) {
			$nav_menu_args['walker'] = new ExtendedNavWalker();
		}

		return array_merge($args, $nav_menu_args);
	}
}
