<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Frontend {
	/**
	 * Estados disponibles del evento.
	 *
	 * @var string
	 */
	const STATUS_UPCOMING = 'upcoming';
	const STATUS_ONGOING  = 'ongoing';
	const STATUS_FINISHED = 'finished';

	/**
	 * Registra assets del frontend.
	 *
	 * @return void
	 */
	public static function register_assets() {
		wp_register_style(
			'umag-eventos-frontend',
			UMAG_EVENTOS_URL . 'assets/css/frontend.css',
			array(),
			UMAG_EVENTOS_VERSION
		);

		wp_register_script(
			'umag-eventos-frontend',
			UMAG_EVENTOS_URL . 'assets/js/frontend.js',
			array(),
			UMAG_EVENTOS_VERSION,
			true
		);

		if ( self::should_enqueue_assets() ) {
			wp_enqueue_style( 'umag-eventos-frontend' );
			wp_enqueue_script( 'umag-eventos-frontend' );
		}
	}

	/**
	 * Determina si corresponde cargar estilos del plugin.
	 *
	 * @return bool
	 */
	private static function should_enqueue_assets() {
		if ( is_post_type_archive( 'umag_evento' ) || is_singular( 'umag_evento' ) ) {
			return true;
		}

		if ( is_singular() ) {
			$post = get_queried_object();

			if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'umag_eventos' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Ordena el archivo de eventos por fecha ascendente.
	 *
	 * @param WP_Query $query Consulta.
	 * @return void
	 */
	public static function sort_archive_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'umag_evento' ) ) {
			return;
		}

		$range = self::get_current_month_range();

		$query->set( 'meta_key', '_umag_evento_fecha' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', -1 );
		$query->set(
			'meta_query',
			array(
				array(
					'key'     => '_umag_evento_fecha',
					'value'   => array( $range['start'], $range['end'] ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				),
			)
		);
	}

	/**
	 * Carga una plantilla propia para el archivo de eventos.
	 *
	 * @param string $template Plantilla detectada por WordPress.
	 * @return string
	 */
	public static function load_archive_template( $template ) {
		if ( is_post_type_archive( 'umag_evento' ) ) {
			$custom_template = UMAG_EVENTOS_PATH . 'templates/archive-umag_evento.php';

			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		}

		return $template;
	}

	/**
	 * Encola assets cuando el shortcode los necesita.
	 *
	 * @return void
	 */
	public static function enqueue_shortcode_assets() {
		self::register_assets();
		wp_enqueue_style( 'umag-eventos-frontend' );
		wp_enqueue_script( 'umag-eventos-frontend' );
	}

	/**
	 * Fuerza assets del plugin dentro del preview/editor de Elementor.
	 *
	 * @return void
	 */
	public static function enqueue_elementor_assets() {
		self::register_assets();
		wp_enqueue_style( 'umag-eventos-frontend' );
		wp_enqueue_script( 'umag-eventos-frontend' );
	}

	/**
	 * Normaliza el modo de visualizacion.
	 *
	 * @param string $mode Valor recibido.
	 * @return string
	 */
	public static function sanitize_display_mode( $mode ) {
		return in_array( $mode, array( 'grid', 'carousel' ), true ) ? $mode : 'grid';
	}

	/**
	 * Normaliza la vista de tarjeta.
	 *
	 * @param string $view Valor recibido.
	 * @return string
	 */
	public static function sanitize_card_view( $view ) {
		return in_array( $view, array( 'listado', 'grande', 'resumido' ), true ) ? $view : 'listado';
	}

	/**
	 * Obtiene clases de contenedor.
	 *
	 * @param string $mode Modo de salida.
	 * @param string $view Vista elegida.
	 * @return string
	 */
	public static function get_wrapper_classes( $mode, $view ) {
		$mode    = self::sanitize_display_mode( $mode );
		$view    = self::sanitize_card_view( $view );
		$classes = array(
			'umag-events-shell-view',
			'umag-events-shell-view--' . $mode,
			'umag-events-shell-view--' . $view,
		);

		if ( 'grid' === $mode ) {
			$classes[] = 'umag-events-grid';
		} else {
			$classes[] = 'umag-events-carousel';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Devuelve la clase segun la cantidad visible.
	 *
	 * @param int $visible_items Cantidad visible.
	 * @return string
	 */
	public static function get_visible_items_class( $visible_items ) {
		$visible_items = max( 1, min( 6, absint( $visible_items ) ) );

		return 'umag-events-visible-' . $visible_items;
	}

	/**
	 * Clase para cantidad visible en tablet.
	 *
	 * @param int $visible_items Cantidad.
	 * @return string
	 */
	public static function get_tablet_visible_items_class( $visible_items ) {
		$visible_items = max( 1, min( 4, absint( $visible_items ) ) );

		return 'umag-events-tablet-visible-' . $visible_items;
	}

	/**
	 * Clase para cantidad visible en celular.
	 *
	 * @param int $visible_items Cantidad.
	 * @return string
	 */
	public static function get_mobile_visible_items_class( $visible_items ) {
		$visible_items = max( 1, min( 2, absint( $visible_items ) ) );

		return 'umag-events-mobile-visible-' . $visible_items;
	}

	/**
	 * Devuelve estilo inline de fondo para el contenedor.
	 *
	 * @return string
	 */
	public static function get_container_background_style() {
		$settings = UMAG_Eventos_Settings::get_settings();
		$mode     = isset( $settings['container_bg_mode'] ) ? sanitize_key( $settings['container_bg_mode'] ) : 'transparent';
		$color    = isset( $settings['container_bg_color'] ) ? trim( (string) $settings['container_bg_color'] ) : '#f5f5f5';
		$type     = isset( $settings['container_bg_gradient_type'] ) ? sanitize_key( $settings['container_bg_gradient_type'] ) : 'linear';
		$opacity  = isset( $settings['container_bg_gradient_opacity'] ) ? max( 0, min( 100, absint( $settings['container_bg_gradient_opacity'] ) ) ) : 35;
		$pos      = isset( $settings['container_bg_gradient_position'] ) ? sanitize_key( (string) $settings['container_bg_gradient_position'] ) : 'center';

		if ( 'transparent' === $mode ) {
			return 'background-color:transparent;';
		}

		if ( ! preg_match( '/^#([A-Fa-f0-9]{6})$/', $color, $matches ) ) {
			$matches = array( '', 'f5f5f5' );
		}

		$hex = strtolower( $matches[1] );

		if ( 'solid' === $mode ) {
			return sprintf( 'background-color:#%s;', $hex );
		}

		$rgba = self::hex_to_rgba( '#' . $hex, $opacity / 100 );

		if ( 'radial' === $type ) {
			return sprintf(
				'background-image:radial-gradient(circle at %1$s, %2$s 0%%, transparent 72%%); background-color:transparent;',
				esc_attr( self::get_gradient_css_position( $pos ) ),
				esc_attr( $rgba )
			);
		}

		return sprintf(
			'background-image:linear-gradient(%1$s, %2$s 0%%, transparent 70%%); background-color:transparent;',
			esc_attr( self::get_gradient_css_direction( $pos ) ),
			esc_attr( $rgba )
		);
	}

	/**
	 * Convierte color HEX a RGBA.
	 *
	 * @param string $hex   Color hexadecimal.
	 * @param float  $alpha Alpha entre 0 y 1.
	 * @return string
	 */
	private static function hex_to_rgba( $hex, $alpha ) {
		$hex = ltrim( trim( $hex ), '#' );

		if ( strlen( $hex ) !== 6 || ! ctype_xdigit( $hex ) ) {
			$hex = 'f5f5f5';
		}

		$alpha = max( 0, min( 1, (float) $alpha ) );
		$r     = hexdec( substr( $hex, 0, 2 ) );
		$g     = hexdec( substr( $hex, 2, 2 ) );
		$b     = hexdec( substr( $hex, 4, 2 ) );

		return sprintf( 'rgba(%d,%d,%d,%.3F)', $r, $g, $b, $alpha );
	}

	/**
	 * Ubicacion CSS para gradiente radial.
	 *
	 * @param string $position Posicion elegida.
	 * @return string
	 */
	private static function get_gradient_css_position( $position ) {
		$map = array(
			'center'       => 'center',
			'top'          => 'top center',
			'right'        => 'center right',
			'bottom'       => 'bottom center',
			'left'         => 'center left',
			'top-left'     => 'top left',
			'top-right'    => 'top right',
			'bottom-left'  => 'bottom left',
			'bottom-right' => 'bottom right',
		);

		return isset( $map[ $position ] ) ? $map[ $position ] : 'center';
	}

	/**
	 * Direccion CSS para gradiente lineal.
	 *
	 * @param string $position Posicion elegida.
	 * @return string
	 */
	private static function get_gradient_css_direction( $position ) {
		$map = array(
			'center'       => 'to right',
			'top'          => 'to bottom',
			'right'        => 'to left',
			'bottom'       => 'to top',
			'left'         => 'to right',
			'top-left'     => 'to bottom right',
			'top-right'    => 'to bottom left',
			'bottom-left'  => 'to top right',
			'bottom-right' => 'to top left',
		);

		return isset( $map[ $position ] ) ? $map[ $position ] : 'to right';
	}

	/**
	 * Obtiene atributos data del contenedor.
	 *
	 * @param string $mode Modo de salida.
	 * @return string
	 */
	public static function get_wrapper_data_attributes( $mode ) {
		if ( 'carousel' !== self::sanitize_display_mode( $mode ) ) {
			return '';
		}

		$settings = UMAG_Eventos_Settings::get_settings();
		$attrs    = array(
			'data-umag-carousel'           => 'true',
			'data-umag-carousel-autoplay'  => ! empty( $settings['carousel_autoplay'] ) ? 'true' : 'false',
			'data-umag-carousel-infinite'  => ! empty( $settings['carousel_infinite'] ) ? 'true' : 'false',
			'data-umag-carousel-delay'     => max( 1000, absint( $settings['carousel_delay'] ) ),
			'data-umag-carousel-pause'     => ! empty( $settings['carousel_pause_hover'] ) ? 'true' : 'false',
			'data-umag-carousel-speed'     => max( 150, absint( $settings['carousel_transition'] ) ),
			'data-umag-carousel-drag'      => ! empty( $settings['carousel_drag'] ) ? 'true' : 'false',
		);

		$parts = array();

		foreach ( $attrs as $name => $value ) {
			$parts[] = sprintf( '%1$s="%2$s"', esc_attr( $name ), esc_attr( (string) $value ) );
		}

		return implode( ' ', $parts );
	}

	/**
	 * Devuelve rango de fechas del mes actual segun la zona horaria del sitio.
	 *
	 * @return array{start:string,end:string}
	 */
	public static function get_current_month_range() {
		$now   = new DateTimeImmutable( 'now', wp_timezone() );
		$start = $now->modify( 'first day of this month' )->format( 'Y-m-d' );
		$end   = $now->modify( 'last day of this month' )->format( 'Y-m-d' );

		return array(
			'start' => $start,
			'end'   => $end,
		);
	}

	/**
	 * Devuelve la fecha del evento formateada.
	 *
	 * @param int $post_id ID del evento.
	 * @return string
	 */
	public static function get_formatted_date( $post_id ) {
		$fecha = get_post_meta( $post_id, '_umag_evento_fecha', true );

		if ( empty( $fecha ) ) {
			return '';
		}

		$timestamp = strtotime( $fecha );

		if ( false === $timestamp ) {
			return $fecha;
		}

		return wp_date( get_option( 'date_format' ), $timestamp );
	}

	/**
	 * Obtiene estado automatico del evento segun fecha/hora.
	 *
	 * @param int $post_id ID del evento.
	 * @return string
	 */
	public static function get_event_status( $post_id ) {
		$start_date = (string) get_post_meta( $post_id, '_umag_evento_fecha', true );
		$start_time = (string) get_post_meta( $post_id, '_umag_evento_hora', true );
		$end_date   = (string) get_post_meta( $post_id, '_umag_evento_fecha_fin', true );
		$end_time   = (string) get_post_meta( $post_id, '_umag_evento_hora_fin', true );

		if ( '' === $start_date ) {
			return self::STATUS_UPCOMING;
		}

		$tz         = wp_timezone();
		$start_time = '' !== $start_time ? $start_time : '00:00';

		try {
			$start = new DateTimeImmutable( $start_date . ' ' . $start_time, $tz );
		} catch ( Exception $e ) {
			return self::STATUS_UPCOMING;
		}

		if ( '' === $end_date ) {
			$end_date = $start_date;
		}

		if ( '' === $end_time ) {
			$end_time = '23:59';
		}

		try {
			$end = new DateTimeImmutable( $end_date . ' ' . $end_time, $tz );
		} catch ( Exception $e ) {
			$end = $start->setTime( 23, 59 );
		}

		$now = new DateTimeImmutable( 'now', $tz );

		if ( $now < $start ) {
			return self::STATUS_UPCOMING;
		}

		if ( $now > $end ) {
			return self::STATUS_FINISHED;
		}

		return self::STATUS_ONGOING;
	}

	/**
	 * Etiqueta legible del estado.
	 *
	 * @param string $status Estado interno.
	 * @return string
	 */
	public static function get_event_status_label( $status ) {
		if ( self::STATUS_ONGOING === $status ) {
			return __( 'En curso', 'umag-eventos' );
		}

		if ( self::STATUS_FINISHED === $status ) {
			return __( 'Finalizado', 'umag-eventos' );
		}

		return __( 'Proximo', 'umag-eventos' );
	}

	/**
	 * Devuelve una tarjeta HTML de evento.
	 *
	 * @param int    $post_id ID del evento.
	 * @param string $view    Vista elegida.
	 * @param string $mode    Modo de salida.
	 * @return string
	 */
	public static function get_event_card_markup( $post_id, $view = 'listado', $mode = 'grid' ) {
		$formatted_date = self::get_formatted_date( $post_id );
		$hora           = get_post_meta( $post_id, '_umag_evento_hora', true );
		$location       = UMAG_Eventos_Locations::get_primary_location( $post_id );
		$location_name  = $location ? $location->name : '';
		$address        = $location ? UMAG_Eventos_Locations::get_address( $location->term_id ) : '';
		$terms          = get_the_terms( $post_id, 'umag_evento_categoria' );
		$categories     = array();
		$view           = self::sanitize_card_view( $view );
		$mode           = self::sanitize_display_mode( $mode );
		$status         = self::get_event_status( $post_id );
		$status_label   = self::get_event_status_label( $status );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$categories = wp_list_pluck( $terms, 'name' );
		}

		ob_start();
		?>
		<article class="umag-event-card umag-event-card--<?php echo esc_attr( $view ); ?> <?php echo 'carousel' === $mode ? 'umag-event-card--carousel' : ''; ?>">
			<div class="umag-event-card__media">
				<?php if ( has_post_thumbnail( $post_id ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="umag-event-card__image-link">
						<?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'umag-event-card__image' ) ); ?>
					</a>
				<?php else : ?>
					<div class="umag-event-card__image umag-event-card__image--placeholder">
						<span><?php esc_html_e( 'Evento UMAG', 'umag-eventos' ); ?></span>
					</div>
				<?php endif; ?>
			</div>
			<div class="umag-event-card__content">
				<p class="umag-event-card__status umag-event-card__status--<?php echo esc_attr( $status ); ?>">
					<?php echo esc_html( $status_label ); ?>
				</p>
				<?php if ( ! empty( $categories ) ) : ?>
					<p class="umag-event-card__eyebrow"><?php echo esc_html( implode( ' | ', $categories ) ); ?></p>
				<?php endif; ?>
				<h2 class="umag-event-card__title">
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
				</h2>
				<div class="umag-event-card__meta">
					<?php if ( $formatted_date ) : ?>
						<span><?php echo esc_html( $formatted_date ); ?></span>
					<?php endif; ?>
					<?php if ( $hora ) : ?>
						<span><?php echo esc_html( $hora ); ?></span>
					<?php endif; ?>
					<?php if ( $location_name ) : ?>
						<span><?php echo esc_html( $location_name ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( $address ) : ?>
					<p class="umag-event-card__address"><?php echo esc_html( $address ); ?></p>
				<?php endif; ?>
				<?php if ( 'resumido' !== $view ) : ?>
					<div class="umag-event-card__excerpt">
						<?php echo wp_kses_post( wpautop( get_the_excerpt( $post_id ) ) ); ?>
					</div>
				<?php endif; ?>
				<div class="umag-event-card__footer">
					<a class="umag-event-card__button" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
						<?php esc_html_e( 'Ver detalles', 'umag-eventos' ); ?>
					</a>
				</div>
			</div>
		</article>
		<?php

		return ob_get_clean();
	}
}
