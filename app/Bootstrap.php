<?php

namespace RunyanCo\WpBaseline;

use RunyanCo\WpBaseline\Modules\CleanUp;
use RunyanCo\WpBaseline\Modules\CleanUpSearch;
use RunyanCo\WpBaseline\Modules\DisableGutenburgEditor;
use RunyanCo\WpBaseline\Modules\DisableRestApi;
use RunyanCo\WpBaseline\Modules\DisableTrackbacks;
use RunyanCo\WpBaseline\Modules\MoveJsToFooter;
use RunyanCo\WpBaseline\Modules\RebuildNavWalker;
use RunyanCo\WpBaseline\Modules\SecurityImprovements;
use RunyanCo\WpBaseline\Modules\TransformUrlsToRelative;

class Bootstrap
{
	use Utilities;

	/**
	 * @var array
	 */
	public $modules = [];

	/**
	 * @var bool
	 */
	public $modulesLoaded = false;

	/**
	 * Bootstrap constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->modules = [

			new CleanUp(),
			new DisableGutenburgEditor(),
			new SecurityImprovements(),
			new DisableRestApi(),
			new MoveJsToFooter(),
			new RebuildNavWalker(),
			new DisableTrackbacks(),
			new CleanUpSearch(),
			new TransformUrlsToRelative(),

		];
	}

	/**
	 * Load in the modules
	 *
	 * @return void
	 */
	public function loadModules()
	{
		if (! $this->modulesLoaded) {

			foreach ($this->modules as $module)  {
				$module->load();
			}

			$this->modulesLoaded = true;
		}
	}

}

