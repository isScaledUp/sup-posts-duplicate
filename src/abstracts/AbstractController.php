<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\abstracts;

use SUPPostsDuplicate\helpers\AdminNotice;
use SUPPostsDuplicate\helpers\Config;
use SUPPostsDuplicate\helpers\Loader;

abstract class AbstractController
{
	protected Loader $loader;

	public function __construct(Loader $loader)
	{
		$this->loader = $loader;
	}

	public final function init()
	{
		$this->register_hooks($this->loader);
		$entities = $this->register_entities();
		foreach ($entities as $entity) {
			if ($entity instanceof AbstractEntity) {
				$ent = new $entity();
				$ent->register_hooks($this->loader);
			} else {
				$support_url = esc_url_raw(Config::get('PluginURI'));
				$name = esc_html(Config::get('Name'));
				AdminNotice::error(
					sprintf('%s PC-003 Error, Please contact the plugin developer at %s',
						"<b>$name:</b>",
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
	 * @param Loader $loader The loader that will be used to register the hooks.
	 * @return void
	 */
	public function register_hooks(Loader $loader): void
	{
		/**
		 * This is a placeholder function to be overridden by the child class or not used.
		 */
	}
}
