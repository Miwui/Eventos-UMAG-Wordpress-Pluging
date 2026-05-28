<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Locations {
	/**
	 * Meta key usada para guardar la direccion de la ubicacion.
	 *
	 * @var string
	 */
	const ADDRESS_META_KEY = 'umag_evento_direccion';

	/**
	 * Inicializa hooks de la taxonomia de ubicaciones.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'umag_evento_ubicacion_add_form_fields', array( __CLASS__, 'render_add_fields' ) );
		add_action( 'umag_evento_ubicacion_edit_form_fields', array( __CLASS__, 'render_edit_fields' ) );
		add_action( 'created_umag_evento_ubicacion', array( __CLASS__, 'save_term_meta' ) );
		add_action( 'edited_umag_evento_ubicacion', array( __CLASS__, 'save_term_meta' ) );
		add_filter( 'manage_edit-umag_evento_ubicacion_columns', array( __CLASS__, 'set_columns' ) );
		add_filter( 'manage_umag_evento_ubicacion_custom_column', array( __CLASS__, 'render_column' ), 10, 3 );
	}

	/**
	 * Renderiza el campo direccion al crear una ubicacion.
	 *
	 * @return void
	 */
	public static function render_add_fields() {
		?>
		<div class="form-field term-address-wrap">
			<label for="umag_evento_direccion"><?php esc_html_e( 'Direccion', 'umag-eventos' ); ?></label>
			<input type="text" name="umag_evento_direccion" id="umag_evento_direccion" value="" />
			<p><?php esc_html_e( 'Guarda la direccion completa para reutilizar esta ubicacion en futuros eventos.', 'umag-eventos' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Renderiza el campo direccion al editar una ubicacion.
	 *
	 * @param WP_Term $term Termino actual.
	 * @return void
	 */
	public static function render_edit_fields( $term ) {
		$address = get_term_meta( $term->term_id, self::ADDRESS_META_KEY, true );
		?>
		<tr class="form-field term-address-wrap">
			<th scope="row">
				<label for="umag_evento_direccion"><?php esc_html_e( 'Direccion', 'umag-eventos' ); ?></label>
			</th>
			<td>
				<input type="text" name="umag_evento_direccion" id="umag_evento_direccion" value="<?php echo esc_attr( $address ); ?>" class="regular-text" />
				<p class="description"><?php esc_html_e( 'Esta direccion quedara disponible cada vez que uses esta ubicacion.', 'umag-eventos' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Guarda la direccion asociada a la ubicacion.
	 *
	 * @param int $term_id ID del termino.
	 * @return void
	 */
	public static function save_term_meta( $term_id ) {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		$address = isset( $_POST['umag_evento_direccion'] ) ? sanitize_text_field( wp_unslash( $_POST['umag_evento_direccion'] ) ) : '';

		if ( '' === $address ) {
			delete_term_meta( $term_id, self::ADDRESS_META_KEY );
			return;
		}

		update_term_meta( $term_id, self::ADDRESS_META_KEY, $address );
	}

	/**
	 * Agrega columna de direccion en el listado de ubicaciones.
	 *
	 * @param array $columns Columnas actuales.
	 * @return array
	 */
	public static function set_columns( $columns ) {
		$updated = array();

		foreach ( $columns as $key => $label ) {
			$updated[ $key ] = $label;

			if ( 'name' === $key ) {
				$updated['umag_evento_direccion'] = __( 'Direccion', 'umag-eventos' );
			}
		}

		return $updated;
	}

	/**
	 * Renderiza el contenido de la columna direccion.
	 *
	 * @param string $content  Contenido actual.
	 * @param string $column   Columna actual.
	 * @param int    $term_id  ID del termino.
	 * @return string
	 */
	public static function render_column( $content, $column, $term_id ) {
		if ( 'umag_evento_direccion' !== $column ) {
			return $content;
		}

		$address = get_term_meta( $term_id, self::ADDRESS_META_KEY, true );

		if ( empty( $address ) ) {
			return esc_html__( 'Sin direccion', 'umag-eventos' );
		}

		return esc_html( $address );
	}

	/**
	 * Obtiene la ubicacion principal de un evento.
	 *
	 * @param int $post_id ID del evento.
	 * @return WP_Term|null
	 */
	public static function get_primary_location( $post_id ) {
		$terms = get_the_terms( $post_id, 'umag_evento_ubicacion' );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return null;
		}

		return reset( $terms );
	}

	/**
	 * Obtiene la direccion guardada para una ubicacion.
	 *
	 * @param int $term_id ID del termino.
	 * @return string
	 */
	public static function get_address( $term_id ) {
		return (string) get_term_meta( $term_id, self::ADDRESS_META_KEY, true );
	}
}
