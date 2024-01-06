<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\Config;
use SUPPostsDuplicate\helpers\Loader;
use SUPPostsDuplicate\helpers\AdminNotice;

/**
 * Abstract class that will be used as the base plugin.
 *
 * @since 0.1.0
 * @package SUPPostsDuplicate\abstracts
 */
abstract class AbstractBasePlugin
{
	/**
	 * @var Loader $loader The loader that will be used to register the hooks.
	 */
	private Loader $loader;


	public function __construct()
	{
		$this->loader = new Loader();
		try {
			new Config();
			new AdminNotice($this->loader);
		} catch (\Exception $e) {
			wp_die($e->getMessage());
		}

		$this->init();
	}

	/**
	 * Initializes the plugin, registering all the components and running the loader.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private final function init()
	{
		$components = $this->register_components();
		foreach ($components as $component) {
			$instance = new $component($this->loader);
			if ($instance instanceof AbstractEntity || $instance instanceof AbstractController) {
				$instance->init();
			} else {
				$support_url = esc_url_raw(Config::get('PluginURI'));
				$name = esc_html(Config::get('Name'));
				AdminNotice::error(
					sprintf('%s PC-001 Error, Please contact the plugin developer at %s',
						"<b>$name:</b>",
						"<a href='$support_url' target='_blank'>$support_url</a>"
					)
				);
			}
		}
		$this->loader->run();
	}

	/**
	 * Register all the components to be used by the plugin.
	 *
	 * @return (AbstractEntity|AbstractController)[] The array of components to register with WordPress.
	 */
	abstract public function register_components(): array;
}
