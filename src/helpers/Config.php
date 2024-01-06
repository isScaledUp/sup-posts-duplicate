<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\helpers;

use Exception;

/**
 * Class Config is a helper class that allows to access the plugin data.
 * @package SUPPostsDuplicate\helpers
 * @since 0.1.0
 */
class Config
{
	/**
	 * The instance of the Config class.
	 * @var Config $instance
	 */
	static Config $instance;

	/**
	 * The plugin data.
	 * @var array $data The plugin data.
	 */
	private array $data = [];

	/**
	 * @throws Exception If the Loader is already instantiated.
	 */
	public function __construct()
	{
		if (isset(self::$instance)) {
			throw new Exception('Config already instantiated');
		}

		self::$instance = $this;

		if (!function_exists('get_plugin_data')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->data = get_plugin_data(__SPD_FILE__);
	}

	/**
	 * Get a value from the plugin data, by key.
	 *
	 * @param string $key The key to get.
	 * @return mixed
	 * @see https://developer.wordpress.org/reference/functions/get_plugin_data/#return Available keys.
	 */
	public static function get(string $key)
	{
		return self::$instance->data[$key];
	}

}
