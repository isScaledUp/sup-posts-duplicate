<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\admin;

use SUPPostsDuplicate\abstracts\AbstractEntity;

class OptionPage extends AbstractEntity
{
	function register_hooks($loader): void
	{
		$loader->add_action('admin_menu', $this, 'add_admin_menu', 15);
		$loader->add_action('admin_init', $this, 'settings_init');
	}

	function add_admin_menu(): void
	{
		if (!function_exists('get_plugin_page_hookname')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		//
		$parent_plugin_page = menu_page_url('sup-content-management');
		if (!$parent_plugin_page) {
			add_menu_page(
				__('SUP Content Management', 'delta-posts-duplicate'),
				__('SUP Content Management', 'delta-posts-duplicate'),
				'manage_options',
				'sup-content-management',
				[$this, 'sup_options_page']
			);
		}
		add_submenu_page(
			'sup-content-management',
			__('Duplicate Post Settings', 'delta-posts-duplicate'),
			__('Duplicate Post Settings', 'delta-posts-duplicate'),
			'manage_options',
			'delta_options_page',
			[$this, 'options_page_html']
		);
	}

	public function sup_options_page()
	{

	}

	public function options_page_html()
	{

	}

	function settings_init(): void
	{
		register_setting('pluginPage', 'delta_posts_duplicate_settings');

		add_settings_section(
			'delta_posts_duplicate_pluginPage_section',
			__('Your section description', 'delta-posts-duplicate'),
			[$this, 'settings_section_callback'],
			'pluginPage'
		);

		add_settings_field(
			'delta_posts_duplicate_text_field_0',
			__('Settings field description', 'delta-posts-duplicate'),
			[$this, 'text_field_0_render'],
			'pluginPage',
			'delta_posts_duplicate_pluginPage_section'
		);
	}

}
