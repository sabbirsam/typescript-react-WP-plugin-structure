<?php
/**
 * Responsible for managing ajax endpoints.
 *
 * @since 2.12.15
 * @package ESTT
 */

namespace ESTT\Ajax;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Manage notices.
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
		add_action( 'wp_ajax_simpleform_notice_action', [ $this, 'manageNotices' ] );
		add_action( 'wp_ajax_nopriv_simpleform_notice_action', [ $this, 'manageNotices' ] );
	}

	/**
	 * Manage notices ajax endpoint response.
	 *
	 * @since 2.12.15
	 */
	public function manageNotices() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'SIMPLEFORM_notices_nonce' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid action', 'sheetstowptable' )
			]);
		}

		$action_type = sanitize_text_field( $_POST['actionType'] );
		$info_type   = sanitize_text_field( $_POST['info']['type'] );
		$info_value  = sanitize_text_field( $_POST['info']['value'] );

		if ( 'hide_notice' === $info_type ) {
			$this->hideNotice( $action_type );
		}

		if ( 'reminder' === $info_type ) {
			$this->setReminder( $action_type, $info_value );
		}
	}

	/**
	 * Hide notices.
	 *
	 * @param string $action_type The action type.
	 * @since 2.12.15
	 */
	public function hideNotice( $action_type ) {
		if ( 'review_notice' === $action_type ) {
			update_option( 'simpleformReviewNotice', true );
		}

		wp_send_json_success([
			'response_type' => 'success'
		]);
	}

	/**
	 * Set reminder to display notice.
	 *
	 * @param string $action_type The action type.
	 * @param string $info_value  The reminder value.
	 * @since 2.12.15
	 */
	public function setReminder( $action_type, $info_value = '' ) {
		if ( 'hide_notice' === $info_value ) {
			$this->hideNotice( $action_type );
			wp_send_json_success([
				'response_type' => 'success'
			]);
		} else {

			if ( 'review_notice' === $action_type ) {
				update_option( 'deafaultNoticeInterval', ( time() + intval( $info_value ) * 24 * 60 * 60 ) );
			}

			wp_send_json_success([
				'response_type' => 'success'
			]);
		}
	}
}