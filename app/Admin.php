<?php
/**
 * Responsible for managing plugin admin area.
 *
 * @since 2.12.15
 * @package ESTT
 */

namespace ESTT;

// If direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Responsible for registering admin menus.
 *
 * @since 2.12.15
 * @package ESTT
 */
class Admin {

	/**
	 * Class constructor.
	 *
	 * @since 2.12.15
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menus' ] );
	}

	/**
	 * Registers admin menus.
	 *
	 * @since 2.12.15
	 */
	public function admin_menus() {
		add_menu_page(
			__( 'Simple Form', 'simpleform' ),
			__( 'Simple Form', 'simpleform' ),
			'manage_options',
			'simpleform-dashboard',
			[ $this, 'dashboardPage' ],
			// SIMPLEFORM_BASE_URL . 'assets/public/images/logo_20_20.svg'
			'dashicons-welcome-widgets-menus'
		);

		if ( current_user_can( 'manage_options' ) ) {
			global $submenu;

			$submenu['simpleform-dashboard'][] = [ __( 'Dashboard', 'simpleform-dashboard' ), 'manage_options', 'admin.php?page=simpleform-dashboard#/' ]; // phpcs:ignore

			$submenu['simpleform-dashboard'][] = [ __( 'Create Form', 'simpleform-dashboard' ), 'manage_options', 'admin.php?page=simpleform-dashboard#/create-form' ]; // phpcs:ignore
		
			$submenu['simpleform-dashboard'][] = [ __( 'Leads', 'simpleform-dashboard' ), 'manage_options', 'admin.php?page=simpleform-dashboard#/Leads' ]; // phpcs:ignore
			
			$submenu['simpleform-dashboard'][] = [ __( 'Settings', 'simpleform-dashboard' ), 'manage_options', 'admin.php?page=simpleform-dashboard#/settings' ]; // phpcs:ignore

			$submenu['simpleform-dashboard'][] = [ __( 'Documentation', 'simpleform-dashboard' ), 'manage_options', 'admin.php?page=simpleform-dashboard#/doc' ]; // phpcs:ignore
		}

		if ( ! ESTT()->helpers->check_pro_plugin_exists() || ! ESTT()->helpers->is_pro_active() ) {
			add_submenu_page(
				'simpleform-dashboard',
				__( 'Simple Form Pro', 'simpleform' ),
				__( '<span style="color: #ff3b00; font-weight: 900; font-size: 14px; letter-spacing: 1.2px"><svg style="width: 16px; height:16px; position: relative; top: 2px;" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M15.3204 9.07646C14.8792 8.09972 14.2378 7.22304 13.4363 6.50129L12.775 5.90444C12.7525 5.88473 12.7255 5.87074 12.6963 5.8637C12.667 5.85667 12.6365 5.8568 12.6073 5.86408C12.5782 5.87136 12.5512 5.88558 12.529 5.90548C12.5067 5.92537 12.4897 5.95035 12.4795 5.97821L12.1841 6.81201C12 7.33509 11.6614 7.86935 11.1818 8.39466C11.15 8.42819 11.1136 8.43713 11.0886 8.43937C11.0636 8.4416 11.025 8.43713 10.9909 8.40584C10.9591 8.37901 10.9432 8.33878 10.9454 8.29854C11.0295 6.95284 10.6204 5.43501 9.72499 3.78305C8.98408 2.41052 7.95454 1.33977 6.66817 0.593153L5.72954 0.0499534C5.60681 -0.021579 5.44999 0.0723072 5.45681 0.213137L5.50681 1.28612C5.5409 2.01933 5.45454 2.66759 5.24999 3.20632C4.99999 3.86576 4.6409 4.47826 4.18181 5.02816C3.86232 5.41033 3.5002 5.75601 3.10227 6.05868C2.14387 6.78332 1.36457 7.71182 0.822726 8.77468C0.282213 9.8468 0.000659456 11.0272 0 12.2239C0 13.279 0.211363 14.3006 0.629545 15.264C1.03333 16.1916 1.61604 17.0335 2.34545 17.7431C3.08182 18.4584 3.93636 19.0217 4.88863 19.4129C5.87499 19.8197 6.92045 20.0254 7.99999 20.0254C9.07954 20.0254 10.125 19.8197 11.1114 19.4151C12.0613 19.0262 12.9251 18.4591 13.6545 17.7453C14.3909 17.03 14.9682 16.1939 15.3704 15.2662C15.788 14.3054 16.0022 13.271 16 12.2261C16 11.1352 15.7727 10.0757 15.3204 9.07646Z" fill="url(#paint0_linear_2876_1706)"/>
				<defs>
				<linearGradient id="paint0_linear_2876_1706" x1="8" y1="20.0254" x2="6" y2="-1.47461" gradientUnits="userSpaceOnUse">
				<stop stop-color="#F53B3B"/>
				<stop offset="1" stop-color="#F5683B"/>
				</linearGradient>
				</defs>
				</svg>
 				GET PRO</span>', 'simpleform' ),
				'manage_options',
				'https://wpxpertise.dev/simple-form-pricing/'
			);

			// Open the link in a new tab.
			add_action('admin_footer', function() {
				echo "<script>
					jQuery(document).ready(function($) {
						$('#toplevel_page_simpleform-dashboard .wp-submenu a[href=\"https://wpxpertise.dev/simple-form-pricing\"]').attr('target', '_blank');
					});
				</script>";
			});
		}
	}

	/**
	 * Displays admin page.
	 *
	 * @return void
	 */
	public static function dashboardPage() {
		echo '<div id="simpleform-app-root"></div>';
	}

	
	
}