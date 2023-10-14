<?php
/**
 * Responsible for managing ajax endpoints.
 *
 * @since 2.12.15
 * @package ESTT
 */

namespace ESTT;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Responsible for handling ajax endpoints.
 *
 * @since 2.12.15
 * @package ESTT
 */
class Ajax {

	/**
	 * Contains promotional wppool products.
	 *
	 * @var \ESTT\Ajax\FetchProducts
	 */
	public $products;

	/**
	 * Contains plugins notices ajax operations.
	 *
	 * @var \ESTT\Ajax\ManageNotices
	 */
	public $notices;

	/**
	 * Contains table delete ajax operations.
	 *
	 * @var \ESTT\Ajax\UdTables
	 */
	public $ud_tables;

	/**
	 * Contains plugin tables ajax operations.
	 *
	 * @var mixed
	 */
	public $tables;

	/**
	 * Contains plugin tabs ajax operations.
	 *
	 * @var mixed
	 */
	public $tabs;

	/**
	 * Contains plugin settings ajax endpoints.
	 *
	 * @var mixed
	 */
	public $settings;

	/**
	 * Class constructor.
	 *
	 * @since 2.12.15
	 */
	public function __construct() {
		$this->notices  = new \ESTT\Ajax\Notices();
		$this->tables   = new \ESTT\Ajax\Tables();
		$this->settings = new \ESTT\Ajax\Settings();
	}
}