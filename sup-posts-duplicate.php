<?php
/**
 * Plugin Name:     	SUP: Posts Duplicate
 * Plugin URI:      	PLUGIN SITE HERE
 * Plugin Support URI: 	https://google.co.il
 * Description:     	Duplicate any post type with ease.
 * Author:          	ScaledUp
 * Author URI:      	UNKNOWN
 * Text Domain:     	sup-posts-duplicate
 * Domain Path:     	/languages
 * Version:         	0.1.0
 *
 * @package         SUP_Posts_Duplicate
 */

const __SPD_FILE__ = __FILE__;
define('__SPD_PATH__', plugin_dir_path(__SPD_FILE__));
define('__SPD_URL__', plugin_dir_url(__SPD_FILE__));

// Your code starts here.
require_once __DIR__ . '/vendor/autoload.php';
new \SUPPostsDuplicate\Plugin();
