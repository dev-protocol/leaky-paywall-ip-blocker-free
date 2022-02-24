<?php

/**
 * Load the base class
 */
class Leaky_Paywall_Ip_Blocker_Settings
{

    function __construct()
    {

        // add submenu page
        add_action('admin_menu', array($this, 'admin_menu'), 20);
    }

    public function admin_menu()
    {

        add_submenu_page('issuem-leaky-paywall', __('IP Blocker', 'lp-ip-blocker'), __('IP Blocker', 'lp-ip-blocker'), apply_filters('manage_leaky_paywall_settings', 'manage_options'), 'leaky-paywall-ip-blocker', array($this, 'build_fields_settings_page'));
    }

    public function build_fields_settings_page()
    {

        $total = Leaky_Paywall_Ip_Blocker_Table::get_total();

?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo _e('IP Blocker', 'lp-ip-blocker'); ?></h1>

            <hr class="wp-header-end">

            <p>Total Blocked IPs: <?php echo $total; ?></p>

            <p><a class="button" href="<?php echo admin_url(); ?>admin.php?page=leaky-paywall-ip-blocker&clear_all_ips=true">Clear IPs</a></p>

    <?php
    }
}


new Leaky_Paywall_Ip_Blocker_Settings();
