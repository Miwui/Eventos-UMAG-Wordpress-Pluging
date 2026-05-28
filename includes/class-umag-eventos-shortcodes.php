<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Shortcodes {
	/**
	 * Registra shortcodes del plugin.
	 *
	 * @return void
	 */
	public static function register() {
		add_shortcode( 'umag_eventos', array( __CLASS__, 'render_eventos' ) );
	}

	/**
	 * Muestra un listado simple de eventos.
	 *
	 * @param array $atts Atributos del shortcode.
	 * @return string
	 */
	public static function render_eventos( $atts ) {
		UMAG_Eventos_Frontend::enqueue_shortcode_assets();

		$atts = shortcode_atts(
			array(
				'cantidad' => '',
				'categoria' => '',
				'ubicacion' => '',
				'modo'      => '',
				'vista'     => '',
			),
			$atts,
			'umag_eventos'
		);

		$settings = UMAG_Eventos_Settings::get_settings();
		$mode     = UMAG_Eventos_Frontend::sanitize_display_mode( $atts['modo'] ? $atts['modo'] : $settings['display_mode'] );
		$view     = UMAG_Eventos_Frontend::sanitize_card_view( $atts['vista'] ? $atts['vista'] : $settings['card_view'] );
		$visible  = '' !== $atts['cantidad'] ? absint( $atts['cantidad'] ) : absint( $settings['events_per_page'] );
		$tablet   = isset( $settings['events_per_page_tablet'] ) ? absint( $settings['events_per_page_tablet'] ) : 2;
		$mobile   = isset( $settings['events_per_page_mobile'] ) ? absint( $settings['events_per_page_mobile'] ) : 1;
		$range    = UMAG_Eventos_Frontend::get_current_month_range();

		$tax_query = array();

		if ( ! empty( $atts['categoria'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'umag_evento_categoria',
				'field'    => 'slug',
				'terms'    => sanitize_title( $atts['categoria'] ),
			);
		}

		if ( ! empty( $atts['ubicacion'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'umag_evento_ubicacion',
				'field'    => 'slug',
				'terms'    => sanitize_title( $atts['ubicacion'] ),
			);
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'umag_evento',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_key'       => '_umag_evento_fecha',
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'tax_query'      => $tax_query,
				'meta_query'     => array(
					array(
						'key'     => '_umag_evento_fecha',
						'value'   => array( $range['start'], $range['end'] ),
						'compare' => 'BETWEEN',
						'type'    => 'DATE',
					),
				),
			)
		);

		if ( ! $query->have_posts() ) {
			return '<div class="umag-events-empty"><p>' . esc_html__( 'No hay eventos disponibles.', 'umag-eventos' ) . '</p></div>';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( UMAG_Eventos_Frontend::get_wrapper_classes( $mode, $view ) . ' ' . UMAG_Eventos_Frontend::get_visible_items_class( $visible ) . ' ' . UMAG_Eventos_Frontend::get_tablet_visible_items_class( $tablet ) . ' ' . UMAG_Eventos_Frontend::get_mobile_visible_items_class( $mobile ) ); ?>" style="<?php echo esc_attr( UMAG_Eventos_Frontend::get_container_background_style() ); ?>" <?php echo wp_kses_data( UMAG_Eventos_Frontend::get_wrapper_data_attributes( $mode ) ); ?>>
			<?php if ( 'carousel' === $mode ) : ?>
				<div class="umag-events-carousel__track">
			<?php endif; ?>
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php echo UMAG_Eventos_Frontend::get_event_card_markup( get_the_ID(), $view, $mode ); ?>
			<?php endwhile; ?>
			<?php if ( 'carousel' === $mode ) : ?>
				</div>
				<button type="button" class="umag-events-carousel__control umag-events-carousel__control--prev" aria-label="<?php esc_attr_e( 'Eventos anteriores', 'umag-eventos' ); ?>">&larr;</button>
				<button type="button" class="umag-events-carousel__control umag-events-carousel__control--next" aria-label="<?php esc_attr_e( 'Eventos siguientes', 'umag-eventos' ); ?>">&rarr;</button>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();

		return ob_get_clean();
	}
}
