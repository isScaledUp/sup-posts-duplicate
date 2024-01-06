<?php

namespace SUPPostsDuplicate\admin;

use SUPPostsDuplicate\abstracts\AbstractEntity;
use SUPPostsDuplicate\Localization;

/**
 * This class is responsible for adding the duplicate post functionality to the admin panel and executing the duplication.
 *
 * @since 0.1.0
 * @uses \SUPPostsDuplicate\abstracts\AbstractEntity
 * @package DeltaPostsDuplicate\admin
 */
class DuplicatePost extends AbstractEntity
{
	function register_hooks(): void
	{
		/**
		 * Areas where the duplicate link will be added.
		 */
		$this->loader->add_filter('post_row_actions', $this, 'add_duplicate', 10, 2); // Post list table
		$this->loader->add_filter('page_row_actions', $this, 'add_duplicate', 10, 2); // Page list table
		$this->loader->add_action('post_submitbox_misc_actions', $this, 'add_duplicate_post_button'); // Post edit page
		$this->loader->add_action('admin_bar_menu', $this, 'add_duplicate_post_button_to_admin_bar', 100); // Admin bar
		$this->loader->add_action('init', $this, 'add_duplicate_to_all_bulk', 100); // Add bulk duplicate to all post types

		/**
		 * The duplicate actions callbacks.
		 */
		$this->loader->add_action('admin_action_duplicate_post_as_draft', $this, 'duplicate_post_as_draft');
		$this->loader->add_action('admin_action_duplicate_post_as_draft_and_edit', $this, 'duplicate_post_as_draft_and_edit');

		/**
		 * Gutenberg support
		 */
		$this->loader->add_action('enqueue_block_editor_assets', $this, 'add_gutenberg_duplicate_assets');
		$this->loader->add_action('rest_api_init', $this, 'add_gutenberg_duplicate_route');

	}

	/**
	 * Add RestAPI duplicate route.
	 *
	 * @return void
	 */
	function add_gutenberg_duplicate_route()
	{
		register_rest_route('sup-posts-duplicate/v1', 'duplicate', [
			'methods' => 'POST',
			'callback' => [$this, 'handle_gutenberg_duplicate_callback'],
			'args' => [
				'post_id' => [
					'required' => true,
					'type' => 'integer',
				],
			],
			'permission_callback' => function () {
				return current_user_can('edit_posts');
			},
		]);
	}

	/**
	 * Handle duplicate RestAPI request.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	function handle_gutenberg_duplicate_callback(\WP_REST_Request $request): \WP_REST_Response
	{
		$post_id = $request->get_param('post_id');
		$new_post_id = $this->duplicate_post($post_id);
		return new \WP_REST_Response([
			'success' => true,
			'post_id' => $new_post_id,
		], 200, [
			'X-Location' => admin_url("post.php?post=$new_post_id&action=edit"),
		]);
	}

	/**
	 * Add Gutenberg duplicate assets.
	 */
	function add_gutenberg_duplicate_assets()
	{
		$asset_file = include(__SPD_PATH__ . 'assets/js/dist/index.asset.php');

		wp_enqueue_script(
			'sup-posts-duplicate-gutenberg',
			__SPD_URL__ . 'assets/js/dist/index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);
		Localization::set_script_translation('sup-posts-duplicate-gutenberg');
	}

	/**
	 * Add duplicate link to bulk actions of all public post types.
	 *
	 * @return void
	 */
	public function add_duplicate_to_all_bulk()
	{
		$post_types = get_post_types(['public' => true], 'objects');
		foreach ($post_types as $post_type) {
			// Since we are dependent on WordPress init hook, we need to add the filters when the action is called, not before.
			$this->loader->add_filter_now('bulk_actions-edit-' . $post_type->name, $this, 'add_bulk_duplicate');
			$this->loader->add_filter_now('handle_bulk_actions-edit-' . $post_type->name, $this, 'handle_bulk_duplicate', 10, 3);
		}
	}

	/**
	 * Add duplicate link to bulk actions.
	 *
	 * @param $actions array
	 * @return array
	 */
	public function add_bulk_duplicate(array $actions): array
	{
		$actions['duplicate'] = __('Duplicate', 'sup-posts-duplicate');
		return $actions;
	}

	/**
	 * Handle bulk duplicate action.
	 *
	 * @param $redirect_to string
	 * @param $doaction string
	 * @param $post_ids array
	 * @return string
	 */
	public function handle_bulk_duplicate(string $redirect_to, string $doaction, array $post_ids): string
	{
		if ($doaction !== 'duplicate') {
			return $redirect_to;
		}

		foreach ($post_ids as $post_id) {
			$this->duplicate_post($post_id);
		}

		$post_type = get_post_type($post_ids[0]);

		return admin_url("edit.php?post_type=$post_type");
	}

	/**
	 * Add duplicate link to admin bar callback.
	 *
	 * @param $wp_admin_bar \WP_Admin_Bar
	 * @return void
	 */
	public function add_duplicate_post_button_to_admin_bar(\WP_Admin_Bar $wp_admin_bar)
	{
		global $post;

		// If current page is a single post of any post type, then add our duplicate link
		if (!is_singular() || !isset($post) || !current_user_can('edit_posts')) {
			return;
		}


		if (current_user_can('edit_posts')) {
			$args = array(
				'id' => 'duplicate_post',
				'title' => __('Duplicate Post', 'sup-posts-duplicate'),
				'href' => wp_nonce_url(admin_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID), basename(__FILE__), 'duplicate_nonce'),
				'meta' => array(
					'title' => __('Duplicate Post', 'sup-posts-duplicate'),
				),
			);
			$wp_admin_bar->add_node($args);
		}
	}

	/**
	 * Add duplicate link to submit box.
	 *
	 * @return void
	 */
	public function add_duplicate_post_button()
	{
		global $post;
		if (current_user_can('edit_posts')) {
			?>
			<div class="misc-pub-section">
				<a class="submitduplicate duplication"
				   href="<?php echo wp_nonce_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce'); ?>"
				   title="<?php esc_html_e('Duplicate this item', 'sup-posts-duplicate'); ?>"
				   rel="permalink"><?php esc_html_e('Duplicate', 'sup-posts-duplicate'); ?></a>
			</div>
			<div class="misc-pub-section">

				<a class="submitduplicate duplication"
				   href="<?php echo wp_nonce_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID . '&edit', basename(__FILE__), 'duplicate_nonce'); ?>"
				   title="<?php esc_html_e('Duplicate this item and edit it', 'sup-posts-duplicate'); ?>"
				   rel="permalink"><?php esc_html_e('Duplicate & Edit', 'sup-posts-duplicate'); ?></a>
			</div>

			<?php
		}
	}

	/**
	 * Add duplicate link to post/page row actions.
	 *
	 * @param $actions array
	 * @param $post \WP_Post
	 * @return array
	 */
	public function add_duplicate(array $actions, \WP_Post $post): array
	{
		if (current_user_can('edit_posts')) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="' . esc_html__('Duplicate this item', 'sup-posts-duplicate') . '" rel="permalink">' . esc_html__('Duplicate', 'sup-posts-duplicate') . '</a>';
			$actions['duplicate_and_edit'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_post_as_draft_and_edit&post=' . $post->ID . '&edit', basename(__FILE__), 'duplicate_nonce') . '" title="' . esc_html__('Duplicate this item and edit it', 'sup-posts-duplicate') . '" rel="permalink">' . esc_html__('Duplicate & Edit', 'sup-posts-duplicate') . '</a>';
		}
		return $actions;
	}

	/**
	 * Verify the nonce.
	 * @return bool Whether the nonce is valid.
	 */
	private function verify_nonce(): bool
	{
		return !isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce']);
	}

	/**
	 * Duplicate a post.
	 *
	 * @param int $post_id The ID of the post to duplicate.
	 * @return int The ID of the new post.
	 */
	private function duplicate_post(int $post_id): int
	{
		$post = get_post($post_id);

		$new_post_author = wp_get_current_user();
		$new_post_author = $new_post_author->ID;
		if (!isset($post)) {
			return 0;
		}

		$args = [
			'post_author' => $new_post_author,
			'post_content' => $post->post_content,
			'post_excerpt' => $post->post_excerpt,
			'post_name' => $post->post_name . '-copy',
			'post_parent' => $post->post_parent,
			'post_password' => $post->post_password,
			'post_status' => 'draft',
			'post_title' => $post->post_title . ' ' . __('(Copy)', 'sup-posts-duplicate'),
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

	/**
	 * Duplicate a post action callback.
	 *
	 * @return void
	 */
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

	/**
	 * Duplicate a post and edit action callback.
	 *
	 * @return void
	 */
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
