<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Admin {
	/**
	 * Inicializa mejoras del panel administrador.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'manage_umag_evento_posts_columns', array( __CLASS__, 'set_columns' ) );
		add_action( 'manage_umag_evento_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_filter( 'manage_edit-umag_evento_sortable_columns', array( __CLASS__, 'set_sortable_columns' ) );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'render_shortcode_hint' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_umag_branding' ) );
		add_action( 'admin_head', array( __CLASS__, 'render_umag_branding_styles' ) );
	}

	/**
	 * Define las columnas visibles en el listado de eventos.
	 *
	 * @param array $columns Columnas actuales.
	 * @return array
	 */
	public static function set_columns( $columns ) {
		return array(
			'cb'                 => isset( $columns['cb'] ) ? $columns['cb'] : '',
			'title'              => __( 'Nombre del evento', 'umag-eventos' ),
			'umag_status'        => __( 'Estado', 'umag-eventos' ),
			'umag_categories'    => __( 'Categorias', 'umag-eventos' ),
			'umag_locations'     => __( 'Ubicacion', 'umag-eventos' ),
			'umag_event_shortcode' => __( 'Shortcode', 'umag-eventos' ),
			'date'               => __( 'Fecha de publicacion', 'umag-eventos' ),
		);
	}

	/**
	 * Renderiza el contenido de columnas personalizadas.
	 *
	 * @param string $column  Nombre de la columna.
	 * @param int    $post_id ID del post.
	 * @return void
	 */
	public static function render_column( $column, $post_id ) {
		if ( 'umag_categories' === $column ) {
			echo wp_kses_post( self::get_taxonomy_terms_list( $post_id, 'umag_evento_categoria' ) );
			return;
		}

		if ( 'umag_status' === $column ) {
			$status = UMAG_Eventos_Frontend::get_event_status( $post_id );
			$label  = UMAG_Eventos_Frontend::get_event_status_label( $status );
			printf(
				'<span class="umag-event-status umag-event-status--%1$s">%2$s</span>',
				esc_attr( $status ),
				esc_html( $label )
			);
			return;
		}

		if ( 'umag_locations' === $column ) {
			echo wp_kses_post( self::get_taxonomy_terms_list( $post_id, 'umag_evento_ubicacion' ) );
			return;
		}

		if ( 'umag_event_shortcode' === $column ) {
			$shortcode = '[umag_eventos]';
			?>
			<input
				type="text"
				class="widefat code"
				readonly
				onfocus="this.select();"
				value="<?php echo esc_attr( $shortcode ); ?>"
			/>
			<?php
		}
	}

	/**
	 * Define columnas ordenables.
	 *
	 * @param array $columns Columnas actuales.
	 * @return array
	 */
	public static function set_sortable_columns( $columns ) {
		$columns['title'] = 'title';

		return $columns;
	}

	/**
	 * Muestra una ayuda rapida de shortcode en el listado admin.
	 *
	 * @return void
	 */
	public static function render_shortcode_hint() {
		global $typenow;

		if ( 'umag_evento' !== $typenow ) {
			return;
		}

		?>
		<div class="alignleft actions" style="padding: 4px 0 0 8px;">
			<span style="display:inline-block; margin-right:8px; font-weight:600;">
				<?php esc_html_e( 'Shortcode rapido:', 'umag-eventos' ); ?>
			</span>
			<input
				type="text"
				class="code"
				readonly
				onfocus="this.select();"
				value="[umag_eventos]"
				style="min-width:180px;"
			/>
		</div>
		<?php
	}

	/**
	 * Genera una lista de terminos enlazados para el admin.
	 *
	 * @param int    $post_id  ID del evento.
	 * @param string $taxonomy Taxonomia.
	 * @return string
	 */
	private static function get_taxonomy_terms_list( $post_id, $taxonomy ) {
		$terms = get_the_terms( $post_id, $taxonomy );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return esc_html__( 'Sin asignar', 'umag-eventos' );
		}

		$links = array();

		foreach ( $terms as $term ) {
			$url = add_query_arg(
				array(
					'post_type' => 'umag_evento',
					$taxonomy   => $term->slug,
				),
				admin_url( 'edit.php' )
			);

			$links[] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $url ),
				esc_html( $term->name )
			);
		}

		return implode( ', ', $links );
	}

	/**
	 * Muestra logo institucional en cabecera del panel de eventos.
	 *
	 * @return void
	 */
	public static function render_umag_branding() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen ) {
			return;
		}

		$allowed = array(
			'edit-umag_evento',
			'umag_evento',
		);

		if ( ! in_array( $screen->id, $allowed, true ) ) {
			return;
		}

		$logo_url = UMAG_EVENTOS_URL . 'assets/images/umag-logo-menu.png';
		?>
		<div class="notice umag-eventos-branding-notice">
			<div class="umag-eventos-branding-inner">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Logo UMAG', 'umag-eventos' ); ?>" />
			</div>
		</div>
		<?php
	}

	/**
	 * Estilos del bloque institucional.
	 *
	 * @return void
	 */
	public static function render_umag_branding_styles() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen ) {
			return;
		}

		$allowed = array(
			'edit-umag_evento',
			'umag_evento',
		);

		if ( ! in_array( $screen->id, $allowed, true ) ) {
			return;
		}
		?>
		<style>
			.umag-eventos-branding-notice {
				background: transparent;
				border: 0;
				box-shadow: none;
				margin: 6px 0 12px;
				padding: 0;
			}
			.umag-eventos-branding-notice .umag-eventos-branding-inner {
				align-items: center;
				display: inline-flex;
				gap: 10px;
			}
			.umag-eventos-branding-notice img {
				display: block;
				height: auto;
				max-height: 48px;
				width: auto;
			}
		</style>
		<?php
	}
}
