=== SUP: Posts Duplicate ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://example.com/
Tags: duplicate, clone, copy, duplicate post, productivity
Requires at least: 4.5
Tested up to: 6.4.2
Requires PHP: 7.4
Stable tag: 0.1.0
License: Apache-2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0.txt

This plugin allows any users that have the capability to edit posts to duplicate posts of any type, including custom post types.

== Description ==

This plugin allows any users that have the capability to edit posts to duplicate posts of any type, including custom post types.

How to use:

1. In 'Edit Posts' or 'Edit Pages', you can hover above the post/page title to see the 'Duplicate' link and click it to duplicate that post/page.
2. While viewing your post/page as a logged-in user with the capability to edit that post/page, you can click on 'Duplicate' link in the admin bar.
3. In the post/page edit screen, you can find the 'Duplicate' link in the 'Publish' meta box.

_If you want to immediately edit the post you have duplicated, you can click the link 'Duplicate & Edit' (Only in 'Edit Posts'/ 'Edit Pages')._

== Installation ==

Installation is standard and straight forward.

1. Unzip the plugin and upload the `sup-posts-duplicate` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Frequently Asked Questions ==

= Will it duplicate any metadata? =

Yes, it will duplicate all the data except author, post date, and post status,
This generally means that any plugin that you use to add custom fields to a post will be copied to the new post.

= Can any user duplicate a post? =
No, only users with the capability to edit that post can duplicate it.

= Can I duplicate a post of a custom post type? =
Yes, you can duplicate any post type that is registered with WordPress.

= Can I duplicate a post from a Gutenberg editor? =
No, you can't duplicate a post from a Gutenberg editor. You can duplicate a post from a classic editor.
We are working on this feature.

== Screenshots ==

1. The admin bar.
2. The post edit screen.
3. The post list screen.

== Changelog ==

= 0.1.0 =
* Initial test release.

