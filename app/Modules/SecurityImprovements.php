<?php

namespace RunyanCo\WpBaseline\Modules;

class SecurityImprovements extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		$this->disallowFileEditingFromBrowser();
	}

	/**
	 * Tighten security up a bit. More to come in this method.
	 *
	 * @return void
	 */
	public function disallowFileEditingFromBrowser()
	{
		add_action('init', function() {
			if(! defined('DISALLOW_FILE_EDIT') ) {
				define('DISALLOW_FILE_EDIT', true );
			}
		});
	}
}
