<?php
declare(strict_types=1);

namespace SUPPostsDuplicate\admin;

use SUPPostsDuplicate\abstracts\AbstractEntity;

/**
 * Class OptionPage is responsible for the plugin's option page.
 *
 * @since 0.1.0
 * @uses \SUPPostsDuplicate\abstracts\AbstractEntity
 * @package SUPPostsDuplicate\admin
 */
class OptionPage extends AbstractEntity
{
	function register_hooks($loader): void
	{
		$loader->add_action('admin_menu', $this, 'add_admin_menu', 15);
		$loader->add_action('admin_init', $this, 'settings_init');
	}

	function add_admin_menu(): void
	{
		$parent_plugin_page = menu_page_url('sup-content-management');
		if (!$parent_plugin_page) {
			add_menu_page(
				__('SUP Content Management', 'sup-posts-duplicate'),
				__('SUP Content Management', 'sup-posts-duplicate'),
				'manage_options',
				'sup-content-management',
				[$this, 'sup_options_page']
			);
		}
		add_submenu_page(
			'sup-content-management',
			__('Duplicate Post Settings', 'sup-posts-duplicate'),
			__('Duplicate Post Settings', 'sup-posts-duplicate'),
			'manage_options',
			'sup_options_page',
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
		register_setting('pluginPage', 'sup_posts_duplicate_settings');

		add_settings_section(
			'sup_posts_duplicate_pluginPage_section',
			__('Your section description', 'sup-posts-duplicate'),
			[$this, 'settings_section_callback'],
			'pluginPage'
		);

		add_settings_field(
			'sup_posts_duplicate_text_field_0',
			__('Settings field description', 'sup-posts-duplicate'),
			[$this, 'text_field_0_render'],
			'pluginPage',
			'sup_posts_duplicate_pluginPage_section'
		);
	}

}
