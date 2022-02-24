<?php

/**
 * Registers zeen101's Leaky Paywall - IP Blocker class
 *
 * @package zeen101's Leaky Paywall - IP Blocker
 * @since 1.0.0
 */

/**
 * This class registers the main IP Blocker functionality
 *
 * @since 1.0.0
 */

class Leaky_Paywall_Ip_Blocker
{

	/**
	 * Custom IP's table
	 * @var object
	 * @since  1.0.1
	 */
	public $table;

	/**
	 * Class constructor, puts things in motion
	 *
	 * @since 1.0.0
	 */
	function __construct($table)
	{

		$this->table = $table;

		add_action('leaky_paywall_is_restricted_content', array($this, 'set_ip_block'));
		add_filter('leaky_paywall_current_user_can_access', array($this, 'check_ip_block'));

		add_action('admin_init', array($this, 'clear_ips'));
	}

	public function set_ip_block($post_id)
	{

		$ip_address = $this->get_ip_address();
		$ip_blocks = $this->table->find_ip($ip_address);

		// only save if it doesn't exist yet
		if (false === ($ip_blocks)) {
			$this->table->save_ip($ip_address);
		}
	}

	public function check_ip_block($access_status)
	{

		$ip_address = $this->get_ip_address();
		$ip_blocks = $this->table->find_ip($ip_address);

		if (false === ($ip_blocks)) {
			return $access_status;
		} else {

			if ($ip_blocks && !leaky_paywall_user_has_access()) {
				return false;
			}
		}

		return $access_status;
	}

	public function get_ip_address()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function clear_ips()
	{
		if (!isset($_GET['clear_all_ips'])) {
			return;
		}

		Leaky_Paywall_Ip_Blocker_Table::clear_ips();
	}
}
