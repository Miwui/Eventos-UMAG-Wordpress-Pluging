<?php
/**
 * Plantilla de archivo para eventos.
 *
 * @package umag-eventos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

UMAG_Eventos_Frontend::register_assets();
wp_enqueue_style( 'umag-eventos-frontend' );
wp_enqueue_script( 'umag-eventos-frontend' );

$settings     = UMAG_Eventos_Settings::get_settings();
$display_mode = UMAG_Eventos_Frontend::sanitize_display_mode( $settings['display_mode'] );
$card_view    = UMAG_Eventos_Frontend::sanitize_card_view( $settings['card_view'] );
$visible      = max( 1, absint( $settings['events_per_page'] ) );
$tablet       = isset( $settings['events_per_page_tablet'] ) ? absint( $settings['events_per_page_tablet'] ) : 2;
$mobile       = isset( $settings['events_per_page_mobile'] ) ? absint( $settings['events_per_page_mobile'] ) : 1;

get_header();
?>
<main class="umag-events-archive">
	<section class="umag-events-hero">
		<div class="umag-events-hero__inner">
			<p class="umag-events-hero__eyebrow"><?php esc_html_e( 'Agenda universitaria', 'umag-eventos' ); ?></p>
			<h1 class="umag-events-hero__title"><?php post_type_archive_title(); ?></h1>
			<p class="umag-events-hero__description">
				<?php esc_html_e( 'Descubre charlas, talleres, encuentros y actividades abiertas de la Universidad de Magallanes.', 'umag-eventos' ); ?>
			</p>
		</div>
	</section>

	<section class="umag-events-shell">
		<?php if ( have_posts() ) : ?>
			<div class="<?php echo esc_attr( UMAG_Eventos_Frontend::get_wrapper_classes( $display_mode, $card_view ) . ' ' . UMAG_Eventos_Frontend::get_visible_items_class( $visible ) . ' ' . UMAG_Eventos_Frontend::get_tablet_visible_items_class( $tablet ) . ' ' . UMAG_Eventos_Frontend::get_mobile_visible_items_class( $mobile ) ); ?>" style="<?php echo esc_attr( UMAG_Eventos_Frontend::get_container_background_style() ); ?>" <?php echo wp_kses_data( UMAG_Eventos_Frontend::get_wrapper_data_attributes( $display_mode ) ); ?>>
				<?php if ( 'carousel' === $display_mode ) : ?>
					<div class="umag-events-carousel__track">
				<?php endif; ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php echo UMAG_Eventos_Frontend::get_event_card_markup( get_the_ID(), $card_view, $display_mode ); ?>
				<?php endwhile; ?>
				<?php if ( 'carousel' === $display_mode ) : ?>
					</div>
					<button type="button" class="umag-events-carousel__control umag-events-carousel__control--prev" aria-label="<?php esc_attr_e( 'Eventos anteriores', 'umag-eventos' ); ?>">&larr;</button>
					<button type="button" class="umag-events-carousel__control umag-events-carousel__control--next" aria-label="<?php esc_attr_e( 'Eventos siguientes', 'umag-eventos' ); ?>">&rarr;</button>
				<?php endif; ?>
			</div>

		<?php else : ?>
			<div class="umag-events-empty">
				<h2><?php esc_html_e( 'Todavia no hay eventos publicados', 'umag-eventos' ); ?></h2>
				<p><?php esc_html_e( 'Cuando agregues nuevos eventos desde el panel, apareceran aqui automaticamente.', 'umag-eventos' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
