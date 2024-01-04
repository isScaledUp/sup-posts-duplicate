<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\Loader;

abstract class AbstractEntity
{
	protected Loader $loader;

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
	}

	public final function init()
	{
		$this->register_hooks($this->loader);
	}

	/**
	 * Register all the hooks to be used by the plugin.
	 *
	 * @param Loader $loader The loader that will be used to register the hooks.
	 * @return void
	 */
	abstract function register_hooks(Loader $loader): void;
}
