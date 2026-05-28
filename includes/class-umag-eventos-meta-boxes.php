<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Meta_Boxes {
	/**
	 * Registra metaboxes del evento.
	 *
	 * @return void
	 */
	public static function register() {
		add_meta_box(
			'umag-evento-detalles',
			__( 'Detalles del evento', 'umag-eventos' ),
			array( __CLASS__, 'render' ),
			'umag_evento',
			'normal',
			'high'
		);

		add_meta_box(
			'umag-evento-shortcodes',
			__( 'Shortcodes del listado', 'umag-eventos' ),
			array( __CLASS__, 'render_shortcode_help' ),
			'umag_evento',
			'side',
			'default'
		);
	}

	/**
	 * Renderiza los campos principales del evento.
	 *
	 * @param WP_Post $post Post actual.
	 * @return void
	 */
	public static function render( $post ) {
		wp_nonce_field( 'umag_evento_save_details', 'umag_evento_nonce' );

		$fecha_inicio = get_post_meta( $post->ID, '_umag_evento_fecha', true );
		$hora_inicio  = get_post_meta( $post->ID, '_umag_evento_hora', true );
		$fecha_fin    = get_post_meta( $post->ID, '_umag_evento_fecha_fin', true );
		$hora_fin     = get_post_meta( $post->ID, '_umag_evento_hora_fin', true );
		?>
		<p>
			<label for="umag_evento_fecha"><strong><?php esc_html_e( 'Fecha de inicio', 'umag-eventos' ); ?></strong></label><br />
			<input type="date" id="umag_evento_fecha" name="umag_evento_fecha" value="<?php echo esc_attr( $fecha_inicio ); ?>" />
		</p>
		<p>
			<label for="umag_evento_hora"><strong><?php esc_html_e( 'Hora de inicio', 'umag-eventos' ); ?></strong></label><br />
			<input type="time" id="umag_evento_hora" name="umag_evento_hora" value="<?php echo esc_attr( $hora_inicio ); ?>" />
		</p>
		<p>
			<label for="umag_evento_fecha_fin"><strong><?php esc_html_e( 'Fecha de finalizacion', 'umag-eventos' ); ?></strong></label><br />
			<input type="date" id="umag_evento_fecha_fin" name="umag_evento_fecha_fin" value="<?php echo esc_attr( $fecha_fin ); ?>" />
		</p>
		<p>
			<label for="umag_evento_hora_fin"><strong><?php esc_html_e( 'Hora de finalizacion', 'umag-eventos' ); ?></strong></label><br />
			<input type="time" id="umag_evento_hora_fin" name="umag_evento_hora_fin" value="<?php echo esc_attr( $hora_fin ); ?>" />
		</p>
		<p>
			<strong><?php esc_html_e( 'Ubicacion y direccion', 'umag-eventos' ); ?></strong><br />
			<?php esc_html_e( 'Selecciona la ubicacion desde la caja lateral "Ubicaciones". La direccion se administra al crear o editar cada ubicacion para reutilizarla en otros eventos.', 'umag-eventos' ); ?>
		</p>
		<?php
	}

	/**
	 * Muestra ayuda rapida de shortcodes dentro del editor.
	 *
	 * @param WP_Post $post Post actual.
	 * @return void
	 */
	public static function render_shortcode_help( $post ) {
		$categories = wp_get_post_terms( $post->ID, 'umag_evento_categoria', array( 'fields' => 'slugs' ) );
		$locations  = wp_get_post_terms( $post->ID, 'umag_evento_ubicacion', array( 'fields' => 'slugs' ) );
		?>
		<p><?php esc_html_e( 'Usa estos shortcodes para insertar listados de eventos en cualquier pagina o entrada.', 'umag-eventos' ); ?></p>
		<p>
			<strong><?php esc_html_e( 'General', 'umag-eventos' ); ?></strong><br />
			<input type="text" class="widefat code" readonly onfocus="this.select();" value="[umag_eventos]" />
		</p>
		<p>
			<strong><?php esc_html_e( 'Con limite', 'umag-eventos' ); ?></strong><br />
			<input type="text" class="widefat code" readonly onfocus="this.select();" value="[umag_eventos cantidad=&quot;6&quot;]" />
		</p>
		<p>
			<strong><?php esc_html_e( 'En carrusel', 'umag-eventos' ); ?></strong><br />
			<input type="text" class="widefat code" readonly onfocus="this.select();" value="[umag_eventos modo=&quot;carousel&quot;]" />
		</p>
		<p>
			<strong><?php esc_html_e( 'Vista resumida', 'umag-eventos' ); ?></strong><br />
			<input type="text" class="widefat code" readonly onfocus="this.select();" value="[umag_eventos vista=&quot;resumido&quot;]" />
		</p>
		<?php if ( ! empty( $categories ) ) : ?>
			<p>
				<strong><?php esc_html_e( 'Por categoria del evento actual', 'umag-eventos' ); ?></strong><br />
				<input
					type="text"
					class="widefat code"
					readonly
					onfocus="this.select();"
					value="<?php echo esc_attr( '[umag_eventos categoria="' . $categories[0] . '"]' ); ?>"
				/>
			</p>
		<?php endif; ?>
		<?php if ( ! empty( $locations ) ) : ?>
			<p>
				<strong><?php esc_html_e( 'Por ubicacion del evento actual', 'umag-eventos' ); ?></strong><br />
				<input
					type="text"
					class="widefat code"
					readonly
					onfocus="this.select();"
					value="<?php echo esc_attr( '[umag_eventos ubicacion="' . $locations[0] . '"]' ); ?>"
				/>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Guarda los metadatos del evento.
	 *
	 * @param int $post_id ID del post.
	 * @return void
	 */
	public static function save( $post_id ) {
		if ( ! isset( $_POST['umag_evento_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['umag_evento_nonce'] ) ), 'umag_evento_save_details' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array(
			'_umag_evento_fecha'     => isset( $_POST['umag_evento_fecha'] ) ? sanitize_text_field( wp_unslash( $_POST['umag_evento_fecha'] ) ) : '',
			'_umag_evento_hora'      => isset( $_POST['umag_evento_hora'] ) ? sanitize_text_field( wp_unslash( $_POST['umag_evento_hora'] ) ) : '',
			'_umag_evento_fecha_fin' => isset( $_POST['umag_evento_fecha_fin'] ) ? sanitize_text_field( wp_unslash( $_POST['umag_evento_fecha_fin'] ) ) : '',
			'_umag_evento_hora_fin'  => isset( $_POST['umag_evento_hora_fin'] ) ? sanitize_text_field( wp_unslash( $_POST['umag_evento_hora_fin'] ) ) : '',
		);

		foreach ( $fields as $meta_key => $value ) {
			if ( '' === $value ) {
				delete_post_meta( $post_id, $meta_key );
				continue;
			}

			update_post_meta( $post_id, $meta_key, $value );
		}

		delete_post_meta( $post_id, '_umag_evento_lugar' );
		delete_post_meta( $post_id, '_umag_evento_cupo' );
	}
}
