<?php
/**
 * Responsible for displaying notices in the plugin.
 *
 * @since 2.12.15
 * @package ESTT
 */

namespace ESTT;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Manages notices.
 *
 * @since 2.12.15
 */
class Notices {
	/**
	 * Class constructor.
	 *
	 * @since 2.12.15
	 */
	public function __construct() {
		/**
		 * Detect plugin. For frontend only.
		 */
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( \is_plugin_active( plugin_basename( SIMPLEFORM_PLUGIN_FILE ) ) ) {
			$this->reviewNoticeByCondition();
		}

		$this->version_check();
	}

	/**
	 * Running version check.
	 *
	 * @since 2.12.15
	 */
	public function version_check() {
		if ( ESTT()->helpers->version_check() ) {
			if ( is_plugin_active( plugin_basename( SIMPLEFORM_PLUGIN_FILE ) ) ) {
				deactivate_plugins( plugin_basename( SIMPLEFORM_PLUGIN_FILE ) );
				add_action( 'admin_notices', [ $this, 'show_notice' ] );
			}
		}
	}

	/**
	 * Loads review notice based on condition.
	 *
	 * @since 2.12.15
	 */
	public function reviewNoticeByCondition() {
		if ( time() >= intval( get_option( 'deafaultNoticeInterval' ) ) ) {
			if ( false === get_option( 'simpleformReviewNotice' ) ) {
				add_action( 'admin_notices', [ $this, 'showReviewNotice' ] );
			}
		}
	}


	/**
	 * Display plugin error notice.
	 *
	 * @return void
	 */
	public function show_notice() {
		printf(
			'<div class="notice notice-error is-dismissible"><h3><strong>%s </strong></h3><p>%s</p></div>',
			esc_html__( 'Plugin', 'simpleform' ),
			esc_html__( 'cannot be activated - requires at least PHP 5.4. Plugin automatically deactivated.', 'simpleform' )
		);
	}

	/**
	 * Display plugin review notice.
	 *
	 * @return void
	 */
	public function showReviewNotice() {
		load_template( SIMPLEFORM_BASE_PATH . 'app/templates/parts/review_notice.php' );
	}

}