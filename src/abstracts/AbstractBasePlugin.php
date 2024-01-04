<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\Config;
use SUPPostsDuplicate\helpers\Loader;
use SUPPostsDuplicate\helpers\AdminNotice;

abstract class AbstractBasePlugin
{
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


		// Expose the notice helper to the rest of the plugin files.

		$this->init();
	}

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
