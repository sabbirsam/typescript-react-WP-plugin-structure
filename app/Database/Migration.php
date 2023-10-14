<?php
/**
 * Managing database operations for tables.
 *
 * @since 3.0.0
 * @package ESTT
 */

namespace ESTT\Database;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Manages plugin database operations.
 *
 * @since 3.0.0
 */
class Migration {

	/**
	 * Create plugins required database table for tables.
	 *
	 * @param int $network_wide The network wide site id.
	 * @since 2.12.15
	 */
	public function run( $network_wide ) {
		global $wpdb;
		if ( is_multisite() && $network_wide ) {
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->create_tables();
				restore_current_blog();
			}
		} else {
			$this->create_tables();
		}
	}

	/**
	 * Create plugins required database table for tables.
	 *
	 * @since 2.12.15
	 */
	public function create_tables() {
		global $wpdb;

		$collate = $wpdb->get_charset_collate();
		$table   = $wpdb->prefix . 'simple_form_tables';

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $table . ' (
			`id` INT(255) NOT NULL AUTO_INCREMENT,
            `form_name` VARCHAR(255) DEFAULT NULL,
            `form_fields` LONGTEXT,
			`time` datetime DEFAULT \'0000-00-00 00:00:00\' NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB ' . $collate . '';

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

}