<?php
declare(strict_types=1);
namespace SUPPostsDuplicate;

use SUPPostsDuplicate\abstracts\AbstractEntity;

/**
 * Class that will be used to register the localization.
 *
 * @since 0.1.0
 * @package SUPPostsDuplicate
 */
class Localization extends AbstractEntity
{
	public function register_hooks(): void
	{
		$this->loader->add_action('plugins_loaded', $this, 'load_textdomain');
	}

	/**
	 * Load the plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain(): void
	{
		load_plugin_textdomain('sup-posts-duplicate', false, __SPD_PATH__ . 'languages');
	}

	/**
	 * Add a script translation.
	 *
	 * @param string $script_name The script name.
	 * @return bool
	 */
	public static function set_script_translation(string $script_name): bool
	{
		return wp_set_script_translations($script_name, 'sup-posts-duplicate');
	}
}
