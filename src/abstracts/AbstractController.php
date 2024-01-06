<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\AdminNotice;
use SUPPostsDuplicate\helpers\Config;
use SUPPostsDuplicate\helpers\Loader;

/**
 * Abstract class for controllers,
 * A controller is a class that will be used to group entities and register them.
 * A controller is capable of registering hooks and entities.
 *
 * @since 0.1.0
 * @package SUPPostsDuplicate\abstracts
 */
abstract class AbstractController
{
	/**
	 * @var Loader $loader The loader that will be used to register the hooks.
	 */
	protected Loader $loader;

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Initializes the controller, registering all the entities and running the loader.
	 * @return void
	 */
	public final function init()
	{
		$this->register_hooks();
		$entities = $this->register_entities();
		foreach ($entities as $entity) {
			$instance = new $entity($this->loader);
			if ($instance instanceof AbstractEntity) {
				$instance->init();
			} else {
				$support_url = esc_url_raw(Config::get('PluginURI'));
				$name = esc_html(Config::get('Name'));
				AdminNotice::error(
					sprintf('%s PC-004 Error (%s), Please contact the plugin developer at %s',
						"<b>$name:</b>",
						get_class($instance),
						"<a href='$support_url' target='_blank'>$support_url</a>"
					)
				);
			}
		}
	}

	/**
	 * Register all the hooks to be used by the plugin.
	 *
	 * @return void
	 */
	abstract function register_entities(): array;

	/**
	 * Register all the hooks to be used by the plugin.
	 *
	 * @return void
	 */
	public function register_hooks(): void
	{
		/**
		 * This is a placeholder function to be overridden by the child class or not used.
		 */
	}
}
