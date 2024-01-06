<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\helpers;

use Exception;

/**
 * Helper class to display admin notices.
 *
 * @package SUPPostsDuplicate\helpers
 * @since 0.1.0
 */
class AdminNotice
{
	/**
	 * The instance of the AdminNotice class.
	 * @var AdminNotice $instance
	 */
	private static AdminNotice $instance;

	/**
	 * Array of notices to be displayed.
	 * @var array $notices
	 */
	private array $notices = [];

	/**
	 * @throws Exception If the Loader is already instantiated.
	 */
	public function __construct(Loader $loader)
	{
		if (isset(self::$instance)) {
			throw new Exception('AdminNotice has already been instantiated.');
		}
		$loader->add_action('admin_notices', $this, 'admin_notices');

		self::$instance = $this;
	}

	public function admin_notices(): void
	{
		foreach ($this->notices as $notice) {
			?>
			<div class="notice notice-<?php echo $notice['type']; ?> is-dismissible">
				<p><?php echo $notice['message']; ?></p>
			</div>
			<?php
		}
	}

	public static function get_instance(): AdminNotice
	{
		return self::$instance;
	}

	private function add(string $message, string $type = 'success'): void
	{
		// Check if admin_notices has already been called.
		if (did_action('admin_notices')) {
			throw new \Exception('Admin notices have already been called.');
		}
		$this->notices[] = [
			'message' => $message,
			'type' => $type,
		];
	}

	/**
	 * Add a success admin notice.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function success(string $message): void
	{
		self::get_instance()->add($message, 'success');
	}

	/**
	 * Add an error admin notice.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function error(string $message): void
	{
		self::get_instance()->add($message, 'error');
	}

	/**
	 * Add a warning admin notice.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function warning(string $message): void
	{
		self::get_instance()->add($message, 'warning');
	}

	/**
	 * Add an info admin notice.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function info(string $message): void
	{
		self::get_instance()->add($message, 'info');
	}


}
