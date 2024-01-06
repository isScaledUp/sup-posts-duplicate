<?php
/**
 * Plugin Name:     	SUP: Posts Duplicate
 * Plugin URI:      	https://scaledup.co.il
 * Description:     	Allows any users that have the capability to edit posts to duplicate posts of any type, including custom post types.
 * Author:          	ScaledUp
 * Author URI:      	https://scaledup.co.il
 * Text Domain:     	sup-posts-duplicate
 * Domain Path:     	/languages
 * Version:         	0.2.0
 * Requires at least: 	5.0
 * Requires PHP: 		7.4
 * Tested up to: 		6.4.2
 *
 * @package         SUP_Posts_Duplicate
 */

const __SPD_FILE__ = __FILE__;
define('__SPD_PATH__', plugin_dir_path(__SPD_FILE__));
define('__SPD_URL__', plugin_dir_url(__SPD_FILE__));

// Your code starts here.
require_once __DIR__ . '/vendor/autoload.php';
new \SUPPostsDuplicate\Plugin();
