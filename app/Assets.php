<?php
/**
 * Responsible for enqueuing assets.
 *
 * @since 2.12.15
 * @package ESTT
 */

namespace ESTT;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Responsible for enqueuing assets.
 *
 * @since 2.12.15
 * @package ESTT
 */
class Assets {

	/**
	 * Class constructor.
	 *
	 * @since 2.12.15
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_filter( 'the_content', [ $this, 'load_assets_for_shortcode' ] );
	}

	/**
	 * Enqueue backend files.
	 *
	 * @since 2.12.15
	 */
	public function admin_scripts() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_simpleform-dashboard' === $current_screen->id ) {
			// We don't want any plugin adding notices to our screens. Let's clear them out here.
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable', false, array('jquery') );        
			wp_enqueue_script( 'jquery-ui-tabs', false, array('jquery') );

			$this->formTableScripts();

			$dependencies = require_once SIMPLEFORM_BASE_PATH . 'react/build/index.asset.php';
			$dependencies['dependencies'][] = 'wp-util';

			wp_enqueue_style(
				'ESTT-admin',
				SIMPLEFORM_BASE_URL . 'assets/admin.css',
				'',
				'1.0.0',
				'all'
			);


			if ( ! ESTT()->helpers->is_pro_active() ) {
			
			}

			wp_enqueue_style(
				'ESTT-app',
				SIMPLEFORM_BASE_URL . 'react/build/index.css',
				'',
				'1.0.0',
				'all'
			);

			wp_enqueue_script(
				'ESTT-app',
				SIMPLEFORM_BASE_URL . 'react/build/index.js',
				$dependencies['dependencies'],
				'1.0.0',
				true
			);

			$icons = apply_filters( 'export_buttons_logo_backend', false );

			$localize = [
				'nonce'            => wp_create_nonce( 'ESTT-admin-app-nonce-action' ),
				'icons'            => $icons,
				'tables'           => ESTT()->database->table->get_all(),
				'pro'              => [
					'installed'   => ESTT()->helpers->check_pro_plugin_exists(),
					'active'      => ESTT()->helpers->is_pro_active(),
					'license'     => function_exists( 'SIMPLEFORMpro' ) ? wp_validate_boolean( SIMPLEFORMpro()->license_status ) : false,
					'license_url' => esc_url( admin_url( 'admin.php?page=sheets_to_wp_table_live_sync_pro_settings' ) )
				],
				'ran_setup_wizard' => wp_validate_boolean( get_option( 'SIMPLEFORM_ran_setup_wizard', false ) )
			];

			if ( ESTT()->helpers->is_pro_active() && ESTT()->helpers->is_latest_version() ) {
				$localize['tabs'] = SIMPLEFORMpro()->database->tab->get_all();
			}

			wp_localize_script(
				'ESTT-app',
				'SIMPLEFORM_APP',
				$localize
			);

			wp_enqueue_script(
				'ESTT-admin-js',
				SIMPLEFORM_BASE_URL . 'assets/public/scripts/backend/admin.min.js',
				[ 'jquery' ],
				SIMPLEFORM_VERSION,
				true
			);

		}
	}

	/**
	 * Load assets for shortcode
	 *
	 * @param  mixed $content The page content.
	 * @return mixed
	 */
	public function load_assets_for_shortcode( $content ) {

		// Check if the page contains the desired shortcode.
		$shortcode = 'simpleform_table';

		if ( has_shortcode( $content, $shortcode ) ) {
			$this->frontend_scripts();
		}

		return $content;
	}

	/**
	 * Enqueue frontend files.
	 *
	 * @since 2.12.15
	 */
	public function frontend_scripts() {
		wp_enqueue_script( 'jquery' );

		wp_enqueue_style(
			'simpleform-frontend-css',
			SIMPLEFORM_BASE_URL . 'assets/public/styles/frontend.min.css',
			[],
			SIMPLEFORM_VERSION,
			'all'
		);

		if ( ! ESTT()->helpers->is_pro_active() ) {
			
		}


		wp_enqueue_script(
			'simpleform-frontend-js',
			SIMPLEFORM_BASE_URL . 'assets/public/scripts/frontend/frontend.min.js',
			[ 'jquery', 'jquery-ui-draggable' ],
			SIMPLEFORM_VERSION,
			true
		);

		$iconsURLs = apply_filters( 'export_buttons_logo_frontend', false );

		wp_localize_script('simpleform-frontend-js', 'front_end_data', [
			'admin_ajax'           => esc_url( admin_url( 'admin-ajax.php' ) ),
			'asynchronous_loading' => get_option( 'asynchronous_loading' ) === 'on' ? 'on' : 'off',
			'isProActive'          => ESTT()->helpers->is_pro_active(),
			'iconsURL'             => $iconsURLs,
			'nonce'                => wp_create_nonce( 'simpleform_sheet_nonce_action' )
		]);
	}


	/**
	 * Enqueue data tables scripts.
	 *
	 * @since 2.12.15
	 */
	public function formTableScripts() {

		wp_enqueue_script(
			'simpleform-form-builder-js',
			SIMPLEFORM_BASE_URL . 'assets/public/library/form-builder.js',
			[ 'jquery' ],
			SIMPLEFORM_VERSION,
			true
		);	

		wp_enqueue_script(
			'simpleform-jquery-dataTable-js',
			SIMPLEFORM_BASE_URL . 'assets/public/library/jquery.datatables.min.js',
			[ 'jquery' ],
			SIMPLEFORM_VERSION,
			true
		);	

		wp_enqueue_script(
			'simpleform-sweet-alert-js',
			SIMPLEFORM_BASE_URL . 'assets/public/library/sweetalert2@11.js',
			[ 'jquery' ],
			SIMPLEFORM_VERSION,
			true
		);

		
		wp_enqueue_style(
			'simpleform-jquery-dataTables',
			SIMPLEFORM_BASE_URL . 'assets/public/library/jquery.dataTables.min.css',
			[],
			SIMPLEFORM_VERSION,
			'all'
		);
		
	}


	/**
	 * Enqueue gutenberg files.
	 *
	 * @since 2.12.15
	 */
	public function gutenbergFiles() {
		wp_enqueue_style(
			'simpleform-gutenberg-css',
			SIMPLEFORM_BASE_URL . 'assets/public/styles/gutenberg.min.css',
			[],
			SIMPLEFORM_VERSION,
			'all'
		);

		register_block_type(
			'simpleform/google-sheets-to-wp-tables',
			[
				'description'   => __( 'Display Google Spreadsheet data to WordPress table in just a few clicks
				and keep the data always synced. Organize and display all your spreadsheet data in your WordPress quickly and effortlessly.', 'sheetstowptable' ),
				'title'         => __( 'Sheets To WP Table Live Sync', 'sheetstowptable' ),
				'editor_script' => 'simpleform-gutenberg',
				'editor_style'  => 'simpleform-gutenberg-css'
			]
		);

		wp_localize_script(
			'simpleform-gutenberg',
			'simpleform_gutenberg_block',
			[
				'admin_ajax'       => esc_url( admin_url( 'admin-ajax.php' ) ),
				'table_details'    => ESTT()->database->table->get_all(),
				'isProActive'      => ESTT()->helpers->is_pro_active(),
				'nonce'  => wp_create_nonce( 'ESTT-admin-app-nonce-action' ),
				'fetch_nonce'     => wp_create_nonce( 'simpleform_sheet_nonce_action' ),
			]
		);
	}

}