<?php
/**
 * Managing database operations for the plugin.
 *
 * @since 3.0.0
 * @package ESTT
 */

namespace ESTT;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Manages plugin database operations.
 *
 * @since 3.0.0
 */
final class Database {

	/**
	 * Contains plugins database migrations.
	 *
	 * @var \ESTT\Database\Migration
	 */
	public $migration;

	/**
	 * Contains tables related database operations.
	 *
	 * @var \ESTT\Database\Table
	 */
	public $table;

	/**
	 * Class constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->migration = new \ESTT\Database\Migration();
		$this->table     = new \ESTT\Database\Table();
	}
}