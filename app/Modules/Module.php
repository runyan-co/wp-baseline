<?php

namespace RunyanCo\WpBaseline\Modules;

use ReflectionClass;
use RunyanCo\WpBaseline\Utilities;

class Module
{
	use Utilities;

	/**
	 * Returns the module name.
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public function getName()
	{
		$reflection = new ReflectionClass($this);

		$moduleName = $this->titleCase($reflection->getShortName());

		return (string) ($moduleName ?? '');
	}

	/**
	 * Hooks a single callback to multiple tags
	 *
	 * @param       $tags
	 * @param       $function
	 * @param  int  $priority
	 * @param  int  $accepted_args
	 *
	 * @return void
	 */
	public function addFilters($tags, $function, $priority = 10, $accepted_args = 1)
	{
		foreach ((array) $tags as $tag) {
			add_filter($tag, $function, $priority, $accepted_args);
		}
	}
}
