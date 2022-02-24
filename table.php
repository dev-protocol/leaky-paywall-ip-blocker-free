<?php

/**
 * This class registers the table for storing the IP's
 *
 * @since 1.0.1
 */


class Leaky_Paywall_Ip_Blocker_Table
{

	/**
	 * WordPress database manager
	 * @var Object
	 */
	public $wpdb;

	public $table_name;

	/**
	 * Class constructor, puts things in motion
	 *
	 * @since 1.0.0
	 */
	function __construct()
	{

		global $wpdb;
		$this->wpdb = $wpdb;

		// Set table name
		$this->table_name = $wpdb->prefix . 'lp_ip_blocker';
	}

	/**
	 * Runs on plugin activation
	 * 
	 * @since  1.0.1
	 */
	public static function on_activate()
	{

		// Create database
		self::create_db();

		// Delete trasient
		delete_transient('lp_ip_blocks');
	}

	/**
	 * Runs on plugin deactivation
	 * 
	 * @since  1.0.1
	 */
	public static function on_deactivate()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'lp_ip_blocker';
		$wpdb->query("DROP TABLE IF EXISTS " . $table_name);
	}

	/**
	 * Runs on plugin uninstall
	 * 
	 * @since  1.0.1
	 */
	public static function on_uninstall()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'lp_ip_blocker';
		$wpdb->query("DROP TABLE IF EXISTS " . $table_name);
	}

	public function is_ipv4($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	public function is_ipv6($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	/**
	 * Save IP in the database
	 * @param  string $ip Plain IP
	 * @return void
	 */
	public function save_ip($ip)
	{

		$sql = '';
		if ($this->is_ipv4($ip)) {
			$sql = $this->wpdb->prepare(
				"
					INSERT INTO $this->table_name
					(ip, protocol)
					VALUES (INET_ATON('%s'), %d)
				",
				$ip,
				'4'
			);
		} elseif ($this->is_ipv6($ip)) {
			$sql = $this->wpdb->prepare(
				"
					INSERT INTO $this->table_name
					(ip, protocol)
					VALUES (INET6_ATON('%s'), %d)
				",
				$ip,
				'6'
			);
		}

		$this->wpdb->query($sql, 'OBJECT');
	}

	/**
	 * Find if IP exists in database
	 * @param  string $ip The plain IP
	 * @return bool
	 */
	public function find_ip($ip)
	{

		if ($this->is_ipv4($ip)) {
			$sql = $this->wpdb->prepare(
				"
					SELECT ip
					FROM $this->table_name
					WHERE ip = INET_ATON('%s')
				",
				$ip
			);
		} elseif ($this->is_ipv6($ip)) {

			$sql = $this->wpdb->prepare(
				"
					SELECT ip
					FROM $this->table_name
					WHERE ip = INET6_ATON('%s')
				",
				$ip
			);
		}


		return !empty($this->wpdb->get_results($sql, 'OBJECT'));
	}

	public static function get_total()
	{

		global $wpdb;
		$table_name = $wpdb->prefix . 'lp_ip_blocker';

		$total = $wpdb->get_var(
			"
			SELECT COUNT(*)
			FROM $table_name
				"
		);

		return $total;
	}

	public static function clear_ips()
	{

		global $wpdb;
		$table_name = $wpdb->prefix . 'lp_ip_blocker';

		$sql = $wpdb->prepare(
			"
					DELETE FROM $table_name
				"
		);

		$wpdb->query($sql, 'OBJECT');
	}

	public static function create_db()
	{
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$charset_collate = $wpdb->get_charset_collate();

		// Table structure
		$columns = "
			ID BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
			ip VARCHAR(255),
			protocol INT(1),
			date datetime DEFAULT CURRENT_TIMESTAMP,
			INDEX (ip, protocol, date)
		";

		// Create table name
		$table_name = $wpdb->prefix . 'lp_ip_blocker';

		$sql = "CREATE TABLE $table_name ( " . $columns . " ) $charset_collate;";

		maybe_create_table($table_name, $sql);
	}
}
