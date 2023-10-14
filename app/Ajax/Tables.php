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
 * Responsible for handling table operations.
 *
 * @since 2.12.15
 * @package ESTT
 */
class Tables {

	/**
	 * Class constructor.
	 *
	 * @since 2.12.15
	 */
	public function __construct() {
		add_action( 'wp_ajax_simpleform_sheet_create', [ $this, 'sheet_creation' ] );
		add_action( 'wp_ajax_nopriv_simpleform_sheet_create', [ $this, 'sheet_creation' ] );
		add_action( 'wp_ajax_simpleform_manage_tab_toggle', [ $this, 'tabNameToggle' ] );
		add_action( 'wp_ajax_simpleform_ud_table', [ $this, 'update_name' ] );

		add_action( 'wp_ajax_simpleform_create_table', [ $this, 'create' ] );
		add_action( 'wp_ajax_simpleform_edit_table', [ $this, 'edit' ] );
		add_action( 'wp_ajax_simpleform_delete_table', [ $this, 'delete' ] );
		add_action( 'wp_ajax_simpleform_get_tables', [ $this, 'get_all' ] );
		add_action( 'wp_ajax_simpleform_save_table', [ $this, 'save' ] );
		add_action( 'wp_ajax_simpleform_sheet_fetch', [ $this, 'get' ] );
		add_action( 'wp_ajax_nopriv_simpleform_sheet_fetch', [ $this, 'get' ] );
		add_action( 'wp_ajax_simpleform_get_table_preview', [ $this, 'get_table_preview' ] );
	}

	/**
	 * Save table by id.
	 */
	public function save() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : false;
		$settings = ! empty( $_POST['settings'] ) ? json_decode( wp_unslash( $_POST['settings'] ), true ) : false;
		$settings['table_settings'] = wp_json_encode( $settings['table_settings'] );

		if ( ! $id || ! $settings ) {
			wp_send_json_error([
				'message' => __( 'Invalid data to save.', '' )
			]);
		}

		$response = ESTT()->database->table->update( $id, $settings );

		wp_send_json_success([
			'message' => __( 'Table updated successfully.', '' ),
			'table_name'     => esc_attr( $settings['table_name'] ),
			'source_url'     => esc_url( $settings['source_url'] ),
			'table_settings' => json_decode( $settings['table_settings'], true )
		]);
	}

	/**
	 * Delete table by id.
	 *
	 * @param  int $id The table ID.
	 * @return int|false
	 */
	public function delete() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : false;
		$tables = ESTT()->database->table->get_all();

		if ( $id ) {
			$response = ESTT()->database->table->delete( $id );

			if ( $response ) {
				wp_send_json_success([
					'message'      => sprintf( __( '%s table deleted.', '' ), $response ),
					'tables'       => $tables,
					'tables_count' => count( ESTT()->database->table->get_all() )
				]);
			}

			wp_send_json_error([
				'message'      => sprintf( __( 'Failed to delete table with id %d' ), $id ),
				'tables'       => $tables,
				'tables_count' => count( ESTT()->database->table->get_all() )
			]);
		}


		wp_send_json_error([
			'message'      => sprintf( __( 'Invalid table to perform delete.' ), $id ),
			'tables'       => $tables,
			'tables_count' => count( ESTT()->database->table->get_all() )
		]);
	}

	/**
	 * Get all tables on ajax request.
	 *
	 * @since 3.0.0
	 */
	public function get_all() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$tables = ESTT()->database->table->get_all();

		wp_send_json_success([
			'tables'       => $tables,
			'tables_count' => count( $tables )
		]);
	}

	/**
	 * Create table on ajax request.
	 *
	 * @since 3.0.0
	 */
	public function create() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$sheet_url = isset( $_POST['sheet_url'] ) ? sanitize_text_field( $_POST['sheet_url'] ) : '';

		if ( empty( $sheet_url ) ) {
			wp_send_json_error([
				'message' => __( 'Empty or invalid google sheet url.', '' )
			]);
		}

		if ( ! ESTT()->helpers->is_pro_active() && ESTT()->database->table->has( $sheet_url ) ) {
			wp_send_json_error([
				'type'    => 'sheet_exists',
				'message' => esc_html__( 'This Google sheet already saved. Try creating a new one', 'sheetstowptable' )
			]);
		}

		$settings = ! empty( $_POST['settings'] ) ? json_decode( wp_unslash( $_POST['settings'] ), true ) : [];
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : __( 'Untitled', 'sheetstowptable' );

		if ( ! is_array( $settings ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid settings to save.', 'sheetstowptable' )
			]);
		}

		$gid = ESTT()->helpers->get_grid_id( $sheet_url );

		if ( false === $gid && ESTT()->helpers->is_pro_active() ) {
			wp_send_json_error([
				'message'       => __( 'Copy the Google sheet URL from browser URL bar that includes <i>gid</i> parameter', 'sheetstowptable' ),
				'response_type' => esc_html( 'invalid_request' )
			]);
		}

		$sheet_id = ESTT()->helpers->get_sheet_id( $sheet_url );
		$response = ESTT()->helpers->get_csv_data( $sheet_url, $sheet_id, $gid );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error([
				'message' => __( 'The spreadsheet is restricted. Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' ),
				'type'    => 'private_sheet'
			]);
		}

		$table = [
			'table_name'     => sanitize_text_field( $name ),
			'source_url'     => esc_url_raw( $sheet_url ),
			'source_type'    => 'spreadsheet',
			'table_settings' => wp_json_encode( $settings ),
		];

		$table_id = ESTT()->database->table->insert( $table );

		$context = ! empty( $_POST['context'] ) ? sanitize_text_field( $_POST['context'] ) : false;

		if ( 'wizard' === $context ) {
			update_option( 'simpleform_ran_setup_wizard', true );
		}

		if ( 'block' === $context ) {
			$this->get_plain( $table_id );
			die();
		}

		wp_send_json_success([
			'id'      => absint( $table_id ),
			'url'     => $sheet_url,
			'message' => esc_html__( 'Table created successfully', 'sheetstowptable' ),
		]);
	}

	/**
	 * Edit table on ajax request.
	 *
	 * @since 3.0.0
	 */
	public function edit() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$table_id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		if ( ! $table_id ) {
			wp_send_json_error([
				'message' => __( 'Invalid table to edit.', 'sheetstowptable' )
			]);
		}

		$table = ESTT()->database->table->get( $table_id );

		if ( ! $table ) {
			wp_send_json_error([
				'type'   => 'invalid_request',
				'output' => esc_html__( 'Request is invalid', 'sheetstowptable' )
			]);
		}

		$settings   = json_decode( $table['table_settings'], true );
		$settings   = null !== $settings ? $settings : unserialize( $table['table_settings'] ); // phpcs:ignore

		wp_send_json_success([
			'table_name'     => esc_attr( $table['table_name'] ),
			'source_url'     => esc_url( $table['source_url'] ),
			'table_settings' => $settings
		]);
	}

	/**
	 * Edit table on ajax request.
	 *
	 * @since 3.0.0
	 */
	public function get_table_preview() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ESTT-admin-app-nonce-action' ) ) {
			wp_send_json_error([
				'message' => __( 'Invalid nonce.', '' )
			]);
		}

		$settings   = ! empty( $_POST['table_settings'] ) && ! is_array( $_POST['table_settings'] ) ? json_decode( wp_unslash( $_POST['table_settings'] ), 1 ) : [];
		$sheet_url  = ! empty( $_POST['source_url'] ) ? esc_url_raw( $_POST['source_url'] ) : '';
		$table_name = ! empty( $_POST['table_name'] ) ? esc_attr( $_POST['table_name'] ) : '';
		$table_id = absint( $_POST['id'] );

		$sheet_id   = ESTT()->helpers->get_sheet_id( $sheet_url );
		$sheet_gid  = ESTT()->helpers->get_grid_id( $sheet_url );
		$styles     = [];

		if ( ESTT()->helpers->is_pro_active() ) {
			$table_data = SIMPLEFORMpro()->helpers->load_table_data( $sheet_url, $table_id );
			$response   = SIMPLEFORMpro()->helpers->generate_html( $table_name, $settings, $table_data );
		} else {
			$table_data = ESTT()->helpers->get_csv_data( $sheet_url, $sheet_id, $sheet_gid );
			$response = ESTT()->helpers->generate_html( $table_data, $settings, $table_name, false );
		}

		if ( empty( $response ) ) {
			wp_send_json_error([
				'type'   => 'invalid_request',
				'output' => esc_html__( 'Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' )
			]);
		}

		wp_send_json_success( [
			'html'     => $response,
			'settings' => $settings
		] );
	}

	/**
	 * Responsible for fetching tables.
	 *
	 * @since 2.12.15
	 */
	public function table_fetch() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tables_related_nonce_action' ) ) {
			wp_send_json_error([
				'message' => __( 'Action is invalid', 'sheetstowptable' )
			]);
		}

		$page_slug = sanitize_text_field( $_POST['page_slug'] );

		if ( empty( $page_slug ) ) {
			wp_send_json_error([
				'message' => __( 'Action is invalid', 'sheetstowptable' )
			]);
		}

		$fetched_tables = ESTT()->database->get_all();
		$tables_html    = $this->table_html( $fetched_tables );

		wp_send_json_success([
			'message' => __( 'Tables fetched successfully.', 'sheetstowptable' ),
			'output'  => $tables_html,
			'no_data' => ! $fetched_tables
		]);
	}

	/**
	 * Populates table html.
	 *
	 * @param array $fetched_tables Fetched tables from db.
	 * @since 2.12.15
	 */
	public function table_html( array $fetched_tables ) {
		$table = '<table id="manage_tables" class="ui celled table">
			<thead>
				<tr>
					<th class="text-center">
						<input data-show="false" type="checkbox" name="manage_tables_main_checkbox"  id="manage_tables_checkbox">
					</th>
					<th class="text-center">' . esc_html__( 'Table ID', 'sheetstowptable' ) . '</th>
					<th class="text-center">' . esc_html__( 'Type', 'sheetstowptable' ) . '</th>
					<th class="text-center">' . esc_html__( 'Shortcode', 'sheetstowptable' ) . '</th>
					<th class="text-center">' . esc_html__( 'Table Name', 'sheetstowptable' ) . '</th>
					<th class="text-center">' . esc_html__( 'Delete', 'sheetstowptable' ) . '</th>
				</tr>
			</thead>
		<tbody>';

		foreach ( $fetched_tables as $table_data ) {
			$table .= '<tr>';
				$table .= '<td class="text-center">';
					$table .= '<input type="checkbox" value="' . esc_attr( $table_data->id ) . '" name="manage_tables_checkbox" class="manage_tables_checkbox">';
				$table .= '</td>';
				$table .= '<td class="text-center">' . esc_attr( $table_data->id ) . '</td>';
				$table .= '<td class="text-center">';
					/* translators: %s: The table type. */
					$table .= ESTT()->helpers->get_table_type( $table_data->source_type );
				$table .= '</td>';
				$table .= '<td class="text-center" style="display: flex; justify-content: center; align-items: center; height: 35px;">';
						$table .= '<input type="hidden" class="table_copy_sortcode" value="[simpleform_table id=' . esc_attr( $table_data->id ) . ']">';
						$table .= '<span class="simpleform_sortcode_copy" style="display: flex; align-items: center; white-space: nowrap; margin-right: 12px">[simpleform_table id=' . esc_attr( $table_data->id ) . ']</span>';
						$table .= '<i class="fas fa-copy simpleform_sortcode_copy" style="font-size: 20px;color: #b7b8ba; cursor: copy"></i>';
				$table .= '</td>';
				$table .= '<td class="text-center">';
				$table .= '<div style="line-height: 38px;">';
					$table .= '<div class="ui input table_name_hidden">';
						$table .= '<input type="text" class="table_name_hidden_input" value="' . esc_attr( $table_data->table_name ) . '" />';
					$table .= '</div>';

					$table .= '<a style="margin-right: 5px; padding: 5px 15px;white-space: nowrap;"
					class="table_name" href="' . esc_url( admin_url( 'admin.php?page=gswpts-dashboard&subpage=create-table&id=' . esc_attr( $table_data->id ) . '' ) ) . '">';
						/* translators: %s: The table type. */
						$table .= esc_html( $table_data->table_name );
					$table .= '</a>';
					$table .= '<button type="button" value="edit" class="copyToken ui right icon button simpleform_edit_table ml-1" id="' . esc_attr( $table_data->id ) . '" style="width: 50px;height: 38px;">';
						$table .= '<img src="' . simpleform_BASE_URL . 'assets/public/icons/rename.svg" width="24px" height="15px" alt="rename-icon"/>';
					$table .= '</button>';

					$table .= '</div>';
				$table .= '</td>';
				$table .= '<td class="text-center">';
					$table .= '<button data-id="' . esc_attr( $table_data->id ) . '" id="table-' . esc_attr( $table_data->id ) . '" class="negative ui button simpleform_table_delete_btn">';
						$table .= esc_html__( 'Delete', 'sheetstowptable' );
						$table .= '<i class="fas fa-trash"></i>';
				$table .= '</button>';
				$table .= '</td>';
			$table .= '</tr>';
		}
			$table .= '</tbody>';
		$table .= '</table>';

		return $table;
	}

	/**
	 * Handles tab name toggle.
	 *
	 * @return void
	 */
	public function tabNameToggle() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'simpleform_tabs_nonce' ) || ! isset( $_POST['show_name'] ) ) {
			wp_send_json_error([
				'response_type' => 'invalid_action',
				'output'        => __( 'Action is invalid', 'sheetstowptable' )
			]);
		}

		$id       = sanitize_text_field( $_POST['tabID'] );
		$name     = rest_sanitize_boolean( $_POST['show_name'] );
		$response = ESTT()->database->update_tab_name_toggle( $id, $name );

		if ( $response ) {
			wp_send_json_success([
				'response_type' => 'success',
				'output'        => __( 'Tab updated successfully', 'sheetstowptable' )
			]);
		} else {
			wp_send_json_error([
				'response_type' => 'error',
				'output'        => __( 'Tab could not be updated. Try again', 'sheetstowptable' )
			]);
		}
	}

	/**
	 * Handle sheet fetching.
	 *
	 * @since 2.12.15
	 */
	public function get() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'simpleform_sheet_nonce_action' ) ) {
			wp_send_json_error([
				'message' => __( 'Action is invalid', 'sheetstowptable' )
			]);
		}

		$id = absint( $_POST['id'] );

		if ( ! $id ) {
			wp_send_json_error([
				'type'    => 'invalid_request',
				'message' => __( 'Request is invalid', 'sheetstowptable' )
			]);
		}

		$table = ESTT()->database->table->get( $id );

		if ( ! $table ) {
			wp_send_json_error([
				'type'    => 'no_table_found',
				'message' => esc_html__( 'No table found.', 'sheetstowptable' )
			]);
		}

		$from_block = isset( $_POST['fromGutenBlock'] ) ? wp_validate_boolean( $_POST['fromGutenBlock'] ) : false;
		$url        = esc_url( $table['source_url'] );
		$name       = esc_attr( $table['table_name'] );
		$sheet_id   = ESTT()->helpers->get_sheet_id( $table['source_url'] );
		$sheet_gid  = ESTT()->helpers->get_grid_id( $table['source_url'] );
		$settings   = json_decode( $table['table_settings'], true );
		$settings   = null !== $settings ? $settings : unserialize( $table['table_settings'] ); // phpcs:ignore
		$styles     = [];

		if ( ESTT()->helpers->is_pro_active() ) {
			$table_data = SIMPLEFORMpro()->helpers->load_table_data( $url, $id );
			$response   = SIMPLEFORMpro()->helpers->generate_html( $name, $settings, $table_data );
		} else {
			$response = ESTT()->helpers->get_csv_data( $table['source_url'], $sheet_id, $sheet_gid );
			$response = ESTT()->helpers->generate_html( $response, $settings, $name, $from_block );
		}

		if ( empty( $response ) ) {
			wp_send_json_error([
				'type'   => 'invalid_request',
				'output' => esc_html__( 'Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' )
			]);
		}

		wp_send_json_success([
			'output'         => $response,
			'table_settings' => $settings,
			'name'           => $name,
			'source_url'     => $url,
			'type'           => 'success'
		]);
	}

	/**
	 * Get sheet fetching in plain values.
	 *
	 * @param int $id The table id.
	 * @since 2.12.15
	 */
	public function get_plain( int $id ) {
		if ( ! $id ) {
			wp_send_json_error([
				'type'    => 'invalid_request',
				'message' => __( 'Request is invalid', 'sheetstowptable' )
			]);
		}

		$table = ESTT()->database->table->get( $id );

		if ( ! $table ) {
			wp_send_json_error([
				'type'    => 'no_table_found',
				'message' => esc_html__( 'No table found.', 'sheetstowptable' )
			]);
		}

		$url        = esc_url( $table['source_url'] );
		$name       = esc_attr( $table['table_name'] );
		$sheet_id   = ESTT()->helpers->get_sheet_id( $table['source_url'] );
		$sheet_gid  = ESTT()->helpers->get_grid_id( $table['source_url'] );
		$settings   = json_decode( $table['table_settings'], true );
		$settings   = null !== $settings ? $settings : unserialize( $table['table_settings'] ); // phpcs:ignore
		$styles     = [];

		if ( ESTT()->helpers->is_pro_active() ) {
			$table_data = SIMPLEFORMpro()->helpers->load_table_data( $url, $id );
			$response   = SIMPLEFORMpro()->helpers->generate_html( $name, $settings, $table_data );
		} else {
			$response = ESTT()->helpers->get_csv_data( $table['source_url'], $sheet_id, $sheet_gid );
			$response = ESTT()->helpers->generate_html( $response, $settings, $name );
		}

		if ( empty( $response ) ) {
			wp_send_json_error([
				'type'   => 'invalid_request',
				'output' => esc_html__( 'Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' )
			]);
		}

		wp_send_json_success([
			'id'             => $id,
			'output'         => $response,
			'table_settings' => $settings,
			'name'           => $name,
			'source_url'     => $url,
			'type'           => 'success'
		]);
	}

	/**
	 * Handles sheet creation.
	 *
	 * @return mixed
	 */
	public function sheet_creation() {
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'simpleform_sheet_creation_nonce' ) ) {
			wp_send_json_error([
				'message' => __( 'Action is invalid', 'sheetstowptable' )
			]);
		}

		if ( isset( $_POST['gutenberg_req'] ) && sanitize_text_field( $_POST['gutenberg_req'] ) ) {
			$this->table_creation_for_gutenberg();
		} else {
			parse_str( $_POST['form_data'], $parsed_data );

			$parsed_data = array_map( 'sanitize_text_field', $parsed_data );
			$sheet_url   = sanitize_text_field( $parsed_data['file_input'] );
			$settings    = ! empty( $_POST['table_settings'] ) ? json_decode( wp_unslash( $_POST['table_settings'] ), true ) : [];
			$name        = isset( $_POST['table_name'] ) ? sanitize_text_field( $_POST['table_name'] ) : __( 'Untitled', 'sheetstowptable' );

			if ( ! is_array( $settings ) ) {
				wp_send_json_error([
					'message' => __( 'Invalid settings to save.', 'sheetstowptable' )
				]);
			}

			if ( empty( $sheet_url ) ) {
				wp_send_json_error([
					'message' => __( 'Form field is empty. Please fill out the field', 'sheetstowptable' )
				]);
			}

			if ( ! empty( $_POST['type'] ) && 'fetch' === sanitize_text_field( $_POST['type'] ) ) {
				$this->generate_sheet_html( $sheet_url, $settings, $name, false );
			}

			if ( 'save' === sanitize_text_field( $_POST['type'] ) || 'saved' === sanitize_text_field( $_POST['type'] ) ) {
				$this->save_table( $sheet_url, $name, $settings );
			}

			if ( 'save_changes' === sanitize_text_field( $_POST['type'] ) ) {
				$this->update_changes( absint( $_POST['id'] ), $settings );
			}
		}
	}

	/**
	 * Handles sheet html.
	 *
	 * @param string $url The sheet url.
	 */
	public static function generate_table_html_for_gt( string $url, $settings, $name ) {
		$gid = ESTT()->helpers->get_grid_id( $url );

		if ( false === $gid && ESTT()->helpers->is_pro_active() ) {
			wp_send_json_error([
				'message'       => __( 'Copy the Google sheet URL from browser URL bar that includes <i>gid</i> parameter', 'sheetstowptable' ),
				'response_type' => esc_html( 'invalid_request' )
			]);
		}

		$sheet_id = ESTT()->helpers->get_sheet_id( $url );
		$response = ESTT()->helpers->get_csv_data( $url, $sheet_id, $gid );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error([
				'message' => __( 'The spreadsheet is restricted. Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' ),
				'type'    => 'private_sheet'
			]);
		}

		if ( ESTT()->helpers->is_pro_active() ) {
			$with_style  = wp_validate_boolean( $settings['importStyles'] ?? false );
			$table_style = $with_style ? 'default-style' : ( ! empty( $settings['table_style'] ) ? 'simpleform_' . $settings['table_style'] : '' );
			$images_data = json_decode( SIMPLEFORMpro()->helpers->get_images_data( $sheet_id, $gid ), true );
			$response    = SIMPLEFORMpro()->helpers->generate_html( $name, $settings, $response, $images_data, true );
		} else {
			$response = ESTT()->helpers->generate_html( $response, $settings, $name );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Handles sheet html.
	 *
	 * @param string $url The sheet url.
	 */
	public static function generate_sheet_html( string $url, $settings, $name, $from_block ) {
		$gid = ESTT()->helpers->get_grid_id( $url );

		if ( false === $gid && ESTT()->helpers->is_pro_active() ) {
			wp_send_json_error([
				'message'       => __( 'Copy the Google sheet URL from browser URL bar that includes <i>gid</i> parameter', 'sheetstowptable' ),
				'response_type' => esc_html( 'invalid_request' )
			]);
		}

		$sheet_id = ESTT()->helpers->get_sheet_id( $url );
		$response = ESTT()->helpers->get_csv_data( $url, $sheet_id, $gid );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error([
				'message' => __( 'The spreadsheet is restricted. Please make it public by clicking on share button at the top of spreadsheet', 'sheetstowptable' ),
				'type'    => 'private_sheet'
			]);
		}

		if ( ESTT()->helpers->is_pro_active() ) {
			$images_data = json_decode( SIMPLEFORMpro()->helpers->get_images_data( $sheet_id, $gid ), true );
			$response    = SIMPLEFORMpro()->helpers->generate_html( $response, [], 'untitled', [], $images_data, $from_block );
		} else {
			$response = ESTT()->helpers->generate_html( $response, $settings, $name, $from_block );
		}

		wp_send_json_success( $response );
	}

	/**
	 * Handle saving table.
	 *
	 * @param string $url The parsed data to save.
	 * @param string $table_name  The table name.
	 * @param array  $settings    The table settings to save.
	 */
	public function save_table( string $url, string $table_name, array $settings ) {
		if ( ! ESTT()->helpers->is_pro_active() && ESTT()->database->has( $url ) ) {
			wp_send_json_error([
				'type'   => 'sheet_exists',
				'output' => esc_html__( 'This Google sheet already saved. Try creating a new one', 'sheetstowptable' )
			]);
		}

		$settings = $this->migrate_settings( $settings );

		$data = [
			'table_name'     => sanitize_text_field( $table_name ),
			'source_url'     => esc_url_raw( $url ),
			'source_type'    => 'spreadsheet',
			'table_settings' => wp_json_encode( $settings ),
		];

		$response = ESTT()->database->table->insert( $data );

		wp_send_json_success([
			'type'   => 'saved',
			'id'     => absint( $response ),
			'url'    => $url,
			'output' => esc_html__( 'Table saved successfully', 'sheetstowptable' )
		]);
	}

	/**
	 * Handles update changes.
	 *
	 * @param int   $table_id The table id.
	 * @param array $settings Settings to update.
	 */
	public function update_changes( int $table_id, array $settings ) {
		$settings = $this->migrate_settings( $settings );
		$response = ESTT()->database->update( $table_id, $settings );

		wp_send_json_success([
			'type'   => 'updated',
			'output' => esc_html__( 'Table changes updated successfully', 'sheetstowptable' )
		]);
	}

	/**
	 * Retrieves table settings.
	 *
	 * @param  array $table_settings The table settings.
	 * @return array
	 */
	public static function migrate_settings( array $table_settings ) {
		$settings = [
			'table_title'           => isset( $table_settings['table_title'] ) ? wp_validate_boolean( $table_settings['table_title'] ) : false,
			'default_rows_per_page' => isset( $table_settings['defaultRowsPerPage'] ) ? absint( $table_settings['defaultRowsPerPage'] ) : 10,
			'show_info_block'       => isset( $table_settings['showInfoBlock'] ) ? wp_validate_boolean( $table_settings['showInfoBlock'] ) : false,
			'show_x_entries'        => isset( $table_settings['showXEntries'] ) ? wp_validate_boolean( $table_settings['showXEntries'] ) : false,
			'swap_filter_inputs'    => isset( $table_settings['swapFilterInputs'] ) ? wp_validate_boolean( $table_settings['swapFilterInputs'] ) : false,
			'swap_bottom_options'   => isset( $table_settings['swapBottomOptions'] ) ? wp_validate_boolean( $table_settings['swapBottomOptions'] ) : false,
			'allow_sorting'         => isset( $table_settings['allowSorting'] ) ? wp_validate_boolean( $table_settings['allowSorting'] ) : false,
			'search_bar'            => isset( $table_settings['searchBar'] ) ? wp_validate_boolean( $table_settings['searchBar'] ) : false,
		];

		return apply_filters( 'simpleform_table_settings', $settings, $table_settings );
	}

	/**
	 * Table creation for gutenberg.
	 *
	 * @since 2.12.15
	 * @phpcs:disable WordPress.Security.NonceVerification
	 */
	public function table_creation_for_gutenberg() {
		$url = isset( $_POST['file_input'] ) ? sanitize_text_field( $_POST['file_input'] ) : '';
		$action = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'fetch';

		if ( ! $url && 'fetch' === $action ) {
			wp_send_json_error([
				'response_type' => 'empty_field',
				'output'        => __( 'Form field is empty. Please fill out the field', 'sheetstowptable' )
			]);
		}

		$table_id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$name     = ! empty( $_POST['table_name'] ) ? sanitize_text_field( $_POST['table_name'] ) : '';
		$settings = ! empty( $_POST['table_settings'] ) && is_array( $_POST['table_settings'] ) ? $_POST['table_settings'] : [];
		$action   = sanitize_text_field( $_POST['type'] );
		$from_block = isset( $_POST['fromGutenBlock'] ) ? wp_validate_boolean( $_POST['fromGutenBlock'] ) : false;

		switch ( $action ) {
			case 'fetch':
				$this->generate_table_html_for_gt( $url, $settings, $name, true );
				break;

			case 'save':
			case 'saved':
				$this->save_table( $url, $name, $settings );
				break;

			case 'save_changes':
				$this->update_changes( $table_id, $settings );
				break;
		}
	}

	/**
	 * Performs delete operations on given ids.
	 *
	 * @param int[] $ids The given int ids.
	 */
	public function delete_all( $ids ) {
		foreach ( $ids as $id ) {
			$response = $this->delete( $id );

			if ( ! $response ) {
				wp_send_json_error([
					'type'   => 'invalid_request',
					'output' => __( 'Request is invalid', 'sheetstowptable' )
				]);

				break;
			}
		}

		wp_send_json_success([
			'output' => __( 'Selected tables deleted successfully', 'sheetstowptable' )
		]);
	}

	/**
	 * Performs updates on tables and tabs.
	 *
	 * @param string $name    The name to update.
	 * @param int    $id      The id where to update.
	 */
	public function update_name( $name, $id ) {
		global $wpdb;

		$table  = $wpdb->prefix . 'simple_form_tables';
		$data   = [ 'table_name' => $name ];
		$output = __( 'Table name updated successfully', 'sheetstowptable' );

		$response = $wpdb->update(
			$table,
			$data,
			[ 'id' => $id ],
			[ '%s' ],
			[ '%d' ]
		);

		if ( $response ) {
			wp_send_json_success([
				'output' => $output,
				'type'   => 'updated'
			]);
		}

		wp_send_json_success([
			'output' => __( 'Could not update the data.', 'sheetstowptable' ),
			'type'   => 'invalid_action'
		]);
	}
}