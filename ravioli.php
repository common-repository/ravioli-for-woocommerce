<?php 
/** 
 * Ravioli for WooCommerce
 * 
 * @package Ravioli for WooCommerce
 * @author Ravioli for WooCommerce
 * @copyright 2024 Ravioli Logistik UG (haftungsbeschränkt) 
 * @license GPL3
 * 
 * @wordpress-plugin 
 * Plugin Name: Ravioli for WooCommerce
 * Description: Let your customers choose if they want to get their order shipped in reusable Ravioli packaging with this official Ravioli plugin. Requires WooCommerce.
 * Version: 1.5.4
 * Author: Ravioli
 * Author URI: https://getravioli.de
 * Text Domain: ravioli 
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'RAVIOLI_VERSION', '1.5.4' );


require plugin_dir_path( __FILE__ ) . 'includes/class-ravioli.php';

function run_ravioli() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
  }

	$plugin = new Ravioli();
	$plugin->run();

}


function register_activation() {
	// this is run after the user activates the plugin
	// do some basic security checks
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
            
  $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
  check_admin_referer( "activate-plugin_{$plugin}" );

	// this is a workaround since we can't run any real code here because of the immediate
	// redirect wordpress does
	// see class-ravioli-admin.php -> load_plugin(), which is ultimately run with this option
	add_option( 'Activated_Plugin', 'ravioli-for-woocommerce' );

}

register_activation_hook( __FILE__ , 'register_activation' );
add_action( 'plugins_loaded', 'run_ravioli', 10 );
?>