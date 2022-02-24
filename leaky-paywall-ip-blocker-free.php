<?php

/**
 * Plugin Name: Leaky Paywall - IP Blocker Free
 * Plugin URI: https://www.zeen101.com
 * Description: Stop users from resetting their article count in an Incognito window
 * Version: 1.0.0
 * Author: Andrey K.
 * Author URI: https://bllue-portfolio.000webhostapp.com/
 * Text Domain: lp-ip-blocker
 * License: GPL2
 */

define( 'ANDREY_LEAKY_IP_BLOCK_PATH', dirname( __FILE__ ) );

// Require table class
require_once( ANDREY_LEAKY_IP_BLOCK_PATH . '/table.php' );

$leaky_paywall_ip_blocker_table = new Leaky_Paywall_Ip_Blocker_Table();

register_activation_hook(__FILE__, ['Leaky_Paywall_Ip_Blocker_Table', 'on_activate']);
register_deactivation_hook(__FILE__, ['Leaky_Paywall_Ip_Blocker_Table', 'on_deactivate']);
register_uninstall_hook(__FILE__, ['Leaky_Paywall_Ip_Blocker_Table', 'on_uninstall']);

// 
/**
 * Instantiate Pigeon Pack class, require helper files
 *
 * @since 1.0.0
 */
function leaky_paywall_ip_blocker_plugins_loaded()
{
	global $leaky_paywall_ip_blocker_table;

	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
	if (is_plugin_active('issuem-leaky-paywall/issuem-leaky-paywall.php') || is_plugin_active('leaky-paywall/leaky-paywall.php')) {

		require_once( ANDREY_LEAKY_IP_BLOCK_PATH . '/class.php' );

		// Instantiate the Pigeon Pack class
		if (class_exists('Leaky_Paywall_Ip_Blocker')) {

			global $leaky_paywall_ip_blocker;

			$leaky_paywall_ip_blocker = new Leaky_Paywall_Ip_Blocker($leaky_paywall_ip_blocker_table);

			//Internationalization
			load_plugin_textdomain('lp-ip-blocker', false, dirname( plugin_basename(__FILE__) ) . '/i18n/');

			require_once( ANDREY_LEAKY_IP_BLOCK_PATH . '/include/admin/settings.php' );
		}

	} else {

		add_action('admin_notices', 'leaky_paywall_ip_blocker_requirement_nag');
	}
}
add_action('plugins_loaded', 'leaky_paywall_ip_blocker_plugins_loaded', 4815162389); //wait for the plugins to be loaded before init

function leaky_paywall_ip_blocker_requirement_nag()
{
?>
	<div id="leaky-paywall-requirement-nag" class="update-nag">
		<?php _e('You must have the Leaky Paywall plugin activated to use the Leaky Paywall IP Blocker plugin.'); ?>
	</div>
<?php
}
