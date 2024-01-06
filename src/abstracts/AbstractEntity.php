<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\Loader;

/**
 * Abstract class that will be used as an entity to register hooks.
 *
 * @since 0.1.0
 * @package SUPPostsDuplicate\abstracts
 */
abstract class AbstractEntity
{
	protected Loader $loader;

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
	}

	public final function init()
	{
		$this->register_hooks();
	}

	/**
	 * Register all the hooks to be used by the plugin.
	 *
	 * @return void
	 */
	abstract function register_hooks(): void;
}
