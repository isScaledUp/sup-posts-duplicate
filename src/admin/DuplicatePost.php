<?php

namespace SUPPostsDuplicate\admin;

use SUPPostsDuplicate\abstracts\AbstractEntity;

/**
 * This class is responsible for adding the duplicate post functionality to the admin panel and executing the duplication.
 *
 * @since 0.0.1
 * @uses \DeltaPostsDuplicate\abstracts\AbstractEntity
 * @package DeltaPostsDuplicate\admin
 */
class DuplicatePost extends AbstractEntity
{
	function register_hooks($loader): void
	{
		$loader->add_filter('post_row_actions', $this, 'add_duplicate', 10, 2);
		$loader->add_filter('page_row_actions', $this, 'add_duplicate', 10, 2);
		$loader->add_action('admin_action_duplicate_post_as_draft', $this, 'duplicate_post_as_draft');
		$loader->add_action('admin_action_duplicate_post_as_draft_and_edit', $this, 'duplicate_post_as_draft_and_edit');

		// add duplicate button to inner post near edit button
		$loader->add_action('post_submitbox_misc_actions', $this, 'add_duplicate_post_button');
		$loader->add_action('admin_bar_menu', $this, 'add_duplicate_post_button_to_admin_bar', 100);

	}

	public function add_duplicate_post_button_to_admin_bar($wp_admin_bar)
	{
		global $post;

		// If current page is a single post of any post type, then add our duplicate link
		if (!is_singular() || !isset($post) || !current_user_can('edit_posts')) {
			return;
		}


		if (current_user_can('edit_posts')) {
			$args = array(
				'id' => 'duplicate_post',
				'title' => __('Duplicate Post', 'delta-posts-duplicate'),
				'href' => wp_nonce_url(admin_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID), basename(__FILE__), 'duplicate_nonce'),
				'meta' => array(
					'title' => __('Duplicate Post', 'delta-posts-duplicate'),
				),
			);
			$wp_admin_bar->add_node($args);
		}
	}

	public function add_duplicate_post_button()
	{
		global $post;
		if (current_user_can('edit_posts')) {
			?>
			<div class="misc-pub-section">
				<a class="submitduplicate duplication"
				   href="<?php echo wp_nonce_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce'); ?>"
				   title="<?php esc_html_e('Duplicate this item', 'delta-posts-duplicate'); ?>"
				   rel="permalink"><?php esc_html_e('Duplicate', 'delta-posts-duplicate'); ?></a>
			</div>
			<div class="misc-pub-section">

				<a class="submitduplicate duplication"
				   href="<?php echo wp_nonce_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID . '&edit', basename(__FILE__), 'duplicate_nonce'); ?>"
				   title="<?php esc_html_e('Duplicate this item and edit it', 'delta-posts-duplicate'); ?>"
				   rel="permalink"><?php esc_html_e('Duplicate & Edit', 'delta-posts-duplicate'); ?></a>
			</div>

			<?php
		}
	}

	public function add_duplicate($actions, $post)
	{
		if (current_user_can('edit_posts')) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="' . esc_html__('Duplicate this item', 'delta-posts-duplicate') . '" rel="permalink">' . esc_html__('Duplicate', 'delta-posts-duplicate') . '</a>';
			$actions['duplicate_and_edit'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID . '&edit', basename(__FILE__), 'duplicate_nonce') . '" title="' . esc_html__('Duplicate this item and edit it', 'delta-posts-duplicate') . '" rel="permalink">' . esc_html__('Duplicate & Edit', 'delta-posts-duplicate') . '</a>';
		}
		return $actions;
	}

	private function verify_nonce(): bool
	{
		if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))) {
			return false;
		}
		return true;
	}

	private function duplicate_post(int $post_id): int
	{
		$post = get_post($post_id);

		$new_post_author = wp_get_current_user();
		$new_post_author = $new_post_author->ID;
		if (!isset($post) || $post === null) {
			return 0;
		}

		$args = [
			'post_author' => $new_post_author,
			'post_content' => $post->post_content,
			'post_excerpt' => $post->post_excerpt,
			'post_name' => $post->post_name . _x('-copy', 'Duplicate Slug Suffix', 'delta-posts-duplicate'),
			'post_parent' => $post->post_parent,
			'post_password' => $post->post_password,
			'post_status' => 'draft',
			'post_title' => $post->post_title . ' ' . __('(Copy)', 'delta-posts-duplicate'),
			'post_type' => $post->post_type,
			'to_ping' => $post->to_ping,
			'menu_order' => $post->menu_order
		];
		$new_post_id = wp_insert_post($args);
		$taxonomies = get_object_taxonomies($post->post_type);
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
		}

		global $wpdb;
		$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos) != 0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query .= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}
		return $new_post_id;
	}

	public function duplicate_post_as_draft()
	{
		if (!$this->verify_nonce()) {
			return;
		}
		if (!isset($_GET['post']) || !isset($_GET['action'])) {
			return;
		}
		if (!current_user_can('edit_posts')) {
			return;
		}
		$post_id = absint($_GET['post']);
		$this->duplicate_post($post_id);
		$post_type = get_post_type($post_id);;
		$redirect_to = admin_url("edit.php?post_type=$post_type");
		wp_redirect($redirect_to);
		exit;
	}

	public function duplicate_post_as_draft_and_edit()
	{
		if (!$this->verify_nonce()) {
			return;
		}
		if (!isset($_GET['post']) || !isset($_GET['action'])) {
			return;
		}
		if (!current_user_can('edit_posts')) {
			return;
		}
		$post_id = absint($_GET['post']);
		$new_id = $this->duplicate_post($post_id);
		$redirect_to = admin_url("post.php?post=$new_id&action=edit");
		wp_redirect($redirect_to);
		exit;
	}

}
