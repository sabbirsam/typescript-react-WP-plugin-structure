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
class Settings {

	public function __construct() {
		add_action( 'wp_ajax_simpleform_get_settings', [ $this, 'get' ] );
		add_action( 'wp_ajax_simpleform_save_settings', [ $this, 'save' ] );
	}

	public function get() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'simpleform-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		wp_send_json_success([
			'async' => get_option( 'asynchronous_loading', false ),
			'css'   => get_option( 'css_code_value' )
		]);
	}

	public function save() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'simpleform-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$settings = ! empty( $_POST['settings'] ) ? json_decode( wp_unslash( $_POST['settings'] ), true ) : false;

		update_option( 'asynchronous_loading', sanitize_text_field( $settings['async_loading'] ) );
		update_option( 'css_code_value', sanitize_text_field( $settings['css_code_value'] ) );

		wp_send_json_success([
			'message' => __( 'Settings saved successfully.', '' ),
			'async' => get_option( 'asynchronous_loading', false ),
			'css'   => get_option( 'css_code_value' )
		]);
	}
}