<?php

namespace SUPPostsDuplicate\helpers;

class Config
{
	static Config $instance;
	private array $data = [];

	public function __construct()
	{
		if(isset(self::$instance)) {
			throw new \Exception('Config already instantiated');
		}

		self::$instance = $this;

		if(!function_exists('get_plugin_data')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		// Get plugin data.
		// This is not the file though, it's the plugin name.
		$this->data = get_plugin_data(__SPD_FILE__);
	}

	private static function get_instance(): Config
	{
		return self::$instance;
	}

	public static function get(string $key)
	{
		return self::get_instance()->data[$key];
	}

}
