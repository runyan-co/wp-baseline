<?php

namespace RunyanCo\WpBaseline\Modules;

class DisableGutenburgEditor extends Module
{
	/**
	 * Initialize this module
	 *
	 * @return void
	 */
	public function load()
	{
		$this->disableGutenburg();
	}

	/**
	 * Disables the default Gutenburg editor which comes with WordPress >= v5.*
	 *
	 * @param  bool  $disabledByDefault
	 *
	 * @return void
	 */
	protected function disableGutenburg(bool $disabledByDefault = true)
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
}
