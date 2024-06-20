<?php
/**
 * Plugin Name: Embed Videos For Product Image Gallery Using WooCommerce
 * Plugin URL: http://wordpress.org/plugins/embed-videos-product-image-gallery-woocommerce
 * Description:  Embed videos to product gallery alongwith images on product page of WooCommerce.
 * Version: 3.2
 * Author: ZealousWeb
 * Author URI: http://zealousweb.com
 * Developer: The Zealousweb Team
 * Developer E-Mail: opensource@zealousweb.com
 * Text Domain: embed-videos-product-image-gallery-woocommerce
 * Domain Path: /languages
 *
 * Copyright: © 2009-2020 ZealousWeb Technologies.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

 // Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions
 *
 * @package Embed Videos For Product Image Gallery Using WooCommerce
 * @since 2.4
 */

if ( !defined( 'WCEVZW_VERSION' ) ) {
	define( 'WCEVZW_VERSION', '3.1' ); // Version of plugin
}

if ( !defined( 'WCEVZW_FILE' ) ) {
	define( 'WCEVZW_FILE', __FILE__ ); // Plugin File
}

if ( !defined( 'WCEVZW_DIR' ) ) {
	define( 'WCEVZW_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if ( !defined( 'WCEVZW_PLUGIN_PATH' ) ) {
	define( 'WCEVZW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); // Plugin Path
}

if ( !defined( 'WCEVZW_URL' ) ) {
	define( 'WCEVZW_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if ( !defined( 'WCEVZW_PLUGIN_BASENAME' ) ) {
	define( 'WCEVZW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Plugin base name
}

if ( !defined( 'WCEVZW_PREFIX' ) ) {
	define( 'WCEVZW_PREFIX', 'wcevzw' ); // Plugin prefix
}

if ( !defined( 'WCEVZW_TEXT_DOMAIN' ) ) {
	define( 'WCEVZW_TEXT_DOMAIN', 'embed-videos-for-product-image-gallery-using-woocommerce' ); // Plugin text domain
}


/**
 * Deregister WooCommerce Scripts
 */
add_action( 'wp_print_scripts', 'wcevzw_my_deregister_javascript', 100 );
function wcevzw_my_deregister_javascript() {
	wp_deregister_script( 'prettyPhoto' );
	wp_deregister_script( 'prettyPhoto-init' );
}

/**
 * enqueue script and style for plugin
 */
add_action( 'wp_enqueue_scripts', 'wcevzw_embedvideos_scripts',999 );
function wcevzw_embedvideos_scripts() {
	wp_enqueue_script( WCEVZW_PREFIX . '-custom-photoswipe', WCEVZW_URL . 'assets/js/photoswipe.js', array('jquery'), WCEVZW_VERSION, true );
	wp_enqueue_style( WCEVZW_PREFIX . '-style-prefetch', WCEVZW_URL .'assets/css/photoswipe.css' );
}

/**
 * include admin and front file
 *
 */
if ( is_admin() ) {
	require_once( WCEVZW_PLUGIN_PATH . '/inc/admin/' . WCEVZW_PREFIX . '.admin.php' );
} else {
	require_once( WCEVZW_PLUGIN_PATH . '/inc/front/' . WCEVZW_PREFIX . '.front.php' );
}
