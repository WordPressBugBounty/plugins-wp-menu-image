<?php

/*
 * Plugin Name: WP Menu Image
 * Plugin URI: https://wordpress.org/plugins/wp-menu-image/
 * description: Can add custom images in menu in Wordpress
 * Version: 2.3
 * Author: Yudiz Solutions Ltd.
 * Author URI: https://www.yudiz.com/
 * Text Domain: wp-menu-image
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.4
 * Tested up to: 6.7.1
 * Requires PHP: 7.0
 * Domain Path:  /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) || ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('WMI_VERSION')) {
    define('WMI_VERSION', '2.3'); // Version of plugin
}

if (!defined('WMI_MENU_IMG')) {
    define('WMI_MENU_IMG', __FILE__); // Plugin File
}

if (!defined('WMI_MENU_IMG_DIR')) {
    define('WMI_MENU_IMG_DIR', plugin_dir_path( __FILE__ ));  // Plugin dir
}

if (!defined('WMI_MENU_IMG_URL')) {
    define('WMI_MENU_IMG_URL', plugin_dir_url( WMI_MENU_IMG_DIR ) . basename( dirname( __FILE__ ) ) . '/' );
}

if (!defined('WMI_MENU_IMG_BASENAME')) {
    define('WMI_MENU_IMG_BASENAME', plugin_basename( WMI_MENU_IMG ) );
}

if(!defined('WMI_PLUGIN_PREFIX') ) {
    define('WMI_PLUGIN_PREFIX', 'wmi'); //  Variable Prefix
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package WP Menu Image
 * @since 2.2
 */
load_plugin_textdomain( 'wp-menu-image', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

require_once( WMI_MENU_IMG_DIR . 'init/wmi-functions.php' );
