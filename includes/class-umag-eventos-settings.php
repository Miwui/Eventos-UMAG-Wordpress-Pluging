<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Settings {
	/**
	 * Nombre de la opcion global.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'umag_eventos_settings';

	/**
	 * Valores por defecto.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'events_per_page'     => 3,
			'events_per_page_tablet' => 2,
			'events_per_page_mobile' => 1,
			'display_mode'        => 'grid',
			'card_view'           => 'listado',
			'container_bg_mode'   => 'transparent',
			'container_bg_color'  => '#f5f5f5',
			'container_bg_gradient_type' => 'linear',
			'container_bg_gradient_opacity' => 35,
			'container_bg_gradient_position' => 'center',
			'carousel_autoplay'   => 1,
			'carousel_infinite'   => 1,
			'carousel_delay'      => 4000,
			'carousel_pause_hover'=> 1,
			'carousel_transition' => 900,
			'carousel_drag'       => 1,
		);
	}

	/**
	 * Devuelve los ajustes normalizados.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( self::OPTION_NAME, array() );

		return wp_parse_args( is_array( $settings ) ? $settings : array(), self::get_defaults() );
	}

	/**
	 * Registra submenu de configuracion.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_submenu_page(
			'edit.php?post_type=umag_evento',
			__( 'Configuracion de visualizacion', 'umag-eventos' ),
			__( 'Visualizacion', 'umag-eventos' ),
			'manage_options',
			'umag-eventos-settings',
			array( __CLASS__, 'render_page' )
		);

		add_submenu_page(
			'edit.php?post_type=umag_evento',
			__( 'Configuracion del carrusel', 'umag-eventos' ),
			__( 'Carrusel', 'umag-eventos' ),
			'manage_options',
			'umag-eventos-carousel-settings',
			array( __CLASS__, 'render_carousel_page' )
		);
	}

	/**
	 * Registra ajustes del plugin.
	 *
	 * @return void
	 */
	public static function register_settings() {
		add_filter( 'pre_update_option_' . self::OPTION_NAME, array( __CLASS__, 'flag_settings_update' ), 10, 2 );
		add_action( 'admin_notices', array( __CLASS__, 'render_settings_notice' ) );

		register_setting(
			'umag_eventos_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'umag_eventos_display_section',
			__( 'Como se mostraran los eventos', 'umag-eventos' ),
			array( __CLASS__, 'render_section_intro' ),
			'umag-eventos-settings'
		);

		add_settings_field(
			'events_per_page',
			__( 'Elementos visibles', 'umag-eventos' ),
			array( __CLASS__, 'render_events_per_page_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'events_per_page_tablet',
			__( 'Elementos visibles en tablet', 'umag-eventos' ),
			array( __CLASS__, 'render_events_per_page_tablet_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'events_per_page_mobile',
			__( 'Elementos visibles en celular', 'umag-eventos' ),
			array( __CLASS__, 'render_events_per_page_mobile_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'display_mode',
			__( 'Modo de presentacion', 'umag-eventos' ),
			array( __CLASS__, 'render_display_mode_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'card_view',
			__( 'Vista de tarjetas', 'umag-eventos' ),
			array( __CLASS__, 'render_card_view_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'container_bg_mode',
			__( 'Fondo del contenedor', 'umag-eventos' ),
			array( __CLASS__, 'render_container_bg_mode_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'container_bg_color',
			__( 'Color RGB del contenedor', 'umag-eventos' ),
			array( __CLASS__, 'render_container_bg_color_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'container_bg_gradient_type',
			__( 'Tipo de degradado', 'umag-eventos' ),
			array( __CLASS__, 'render_container_bg_gradient_type_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'container_bg_gradient_opacity',
			__( 'Transparencia (0 a 100)', 'umag-eventos' ),
			array( __CLASS__, 'render_container_bg_gradient_opacity_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_field(
			'container_bg_gradient_position',
			__( 'Ubicacion del degradado', 'umag-eventos' ),
			array( __CLASS__, 'render_container_bg_gradient_position_field' ),
			'umag-eventos-settings',
			'umag_eventos_display_section'
		);

		add_settings_section(
			'umag_eventos_carousel_section',
			__( 'Opciones globales del carrusel', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_section_intro' ),
			'umag-eventos-carousel-settings'
		);

		add_settings_field(
			'carousel_autoplay',
			__( 'Autoplay', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_autoplay_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);

		add_settings_field(
			'carousel_infinite',
			__( 'Loop infinito', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_infinite_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);

		add_settings_field(
			'carousel_delay',
			__( 'Tiempo en milisegundos', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_delay_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);

		add_settings_field(
			'carousel_transition',
			__( 'Duracion de desplazamiento', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_transition_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);

		add_settings_field(
			'carousel_pause_hover',
			__( 'Pausa al pasar el cursor', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_pause_hover_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);

		add_settings_field(
			'carousel_drag',
			__( 'Arrastre tactil y mouse', 'umag-eventos' ),
			array( __CLASS__, 'render_carousel_drag_field' ),
			'umag-eventos-carousel-settings',
			'umag_eventos_carousel_section'
		);
	}

	/**
	 * Sanea la configuracion.
	 *
	 * @param array $input Valores enviados.
	 * @return array
	 */
	public static function sanitize_settings( $input ) {
		$defaults = self::get_defaults();
		$current  = self::get_settings();
		$output   = wp_parse_args( is_array( $current ) ? $current : array(), $defaults );

		if ( isset( $input['events_per_page'] ) ) {
			$output['events_per_page'] = max( 1, absint( $input['events_per_page'] ) );
		}

		if ( isset( $input['events_per_page_tablet'] ) ) {
			$output['events_per_page_tablet'] = max( 1, min( 4, absint( $input['events_per_page_tablet'] ) ) );
		}

		if ( isset( $input['events_per_page_mobile'] ) ) {
			$output['events_per_page_mobile'] = max( 1, min( 2, absint( $input['events_per_page_mobile'] ) ) );
		}

		if ( isset( $input['display_mode'] ) ) {
			$display_mode = sanitize_key( $input['display_mode'] );
			$output['display_mode'] = in_array( $display_mode, array( 'grid', 'carousel' ), true ) ? $display_mode : $defaults['display_mode'];
		}

		if ( isset( $input['card_view'] ) ) {
			$card_view = sanitize_key( $input['card_view'] );
			$output['card_view'] = in_array( $card_view, array( 'listado', 'grande', 'resumido' ), true ) ? $card_view : $defaults['card_view'];
		}

		if ( isset( $input['container_bg_mode'] ) ) {
			$bg_mode = sanitize_key( $input['container_bg_mode'] );
			$output['container_bg_mode'] = in_array( $bg_mode, array( 'transparent', 'solid', 'gradient' ), true ) ? $bg_mode : $defaults['container_bg_mode'];
		}

		if ( isset( $input['container_bg_color'] ) ) {
			$hex = self::sanitize_hex_color_value( (string) $input['container_bg_color'] );
			$output['container_bg_color'] = '' !== $hex ? $hex : $defaults['container_bg_color'];
		}

		if ( isset( $input['container_bg_gradient_type'] ) ) {
			$type = sanitize_key( $input['container_bg_gradient_type'] );
			$output['container_bg_gradient_type'] = in_array( $type, array( 'linear', 'radial' ), true ) ? $type : $defaults['container_bg_gradient_type'];
		}

		if ( isset( $input['container_bg_gradient_opacity'] ) ) {
			$output['container_bg_gradient_opacity'] = max( 0, min( 100, absint( $input['container_bg_gradient_opacity'] ) ) );
		}

		if ( isset( $input['container_bg_gradient_position'] ) ) {
			$output['container_bg_gradient_position'] = self::sanitize_gradient_position( (string) $input['container_bg_gradient_position'] );
		}

		if ( isset( $input['carousel_autoplay'] ) ) {
			$output['carousel_autoplay'] = absint( $input['carousel_autoplay'] ) ? 1 : 0;
		}

		if ( isset( $input['carousel_infinite'] ) ) {
			$output['carousel_infinite'] = absint( $input['carousel_infinite'] ) ? 1 : 0;
		}

		if ( isset( $input['carousel_pause_hover'] ) ) {
			$output['carousel_pause_hover'] = absint( $input['carousel_pause_hover'] ) ? 1 : 0;
		}

		if ( isset( $input['carousel_drag'] ) ) {
			$output['carousel_drag'] = absint( $input['carousel_drag'] ) ? 1 : 0;
		}

		if ( isset( $input['carousel_delay'] ) ) {
			$output['carousel_delay'] = max( 1000, absint( $input['carousel_delay'] ) );
		}

		if ( isset( $input['carousel_transition'] ) ) {
			$output['carousel_transition'] = max( 150, absint( $input['carousel_transition'] ) );
		}

		return $output;
	}

	/**
	 * Introduccion de la seccion.
	 *
	 * @return void
	 */
	public static function render_section_intro() {
		echo '<p>' . esc_html__( 'Configura la forma en que se renderizan los eventos en el archivo publico y en el shortcode general.', 'umag-eventos' ) . '</p>';
	}

	/**
	 * Introduccion de la seccion de carrusel.
	 *
	 * @return void
	 */
	public static function render_carousel_section_intro() {
		echo '<p>' . esc_html__( 'Estas opciones son globales y controlan el comportamiento del carrusel en todo el sitio.', 'umag-eventos' ) . '</p>';
	}

	/**
	 * Render del campo eventos por pagina.
	 *
	 * @return void
	 */
	public static function render_events_per_page_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			min="1"
			max="6"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[events_per_page]"
			value="<?php echo esc_attr( $settings['events_per_page'] ); ?>"
			class="small-text"
		/>
		<p class="description"><?php esc_html_e( 'Cantidad de tarjetas que se muestran al mismo tiempo en la vista de eventos. Los eventos cargados se limitan al mes actual.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de elementos visibles en tablet.
	 *
	 * @return void
	 */
	public static function render_events_per_page_tablet_field() {
		$settings = self::get_settings();
		?>
		<label style="display:inline-flex; align-items:center; gap:6px;">
			<span class="dashicons dashicons-tablet" aria-hidden="true"></span>
			<input
				type="number"
				min="1"
				max="4"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[events_per_page_tablet]"
				value="<?php echo esc_attr( $settings['events_per_page_tablet'] ); ?>"
				class="small-text"
			/>
		</label>
		<p class="description"><?php esc_html_e( 'Recomendado: 2 tarjetas para tablet.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de elementos visibles en celular.
	 *
	 * @return void
	 */
	public static function render_events_per_page_mobile_field() {
		$settings = self::get_settings();
		?>
		<label style="display:inline-flex; align-items:center; gap:6px;">
			<span class="dashicons dashicons-smartphone" aria-hidden="true"></span>
			<input
				type="number"
				min="1"
				max="2"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[events_per_page_mobile]"
				value="<?php echo esc_attr( $settings['events_per_page_mobile'] ); ?>"
				class="small-text"
			/>
		</label>
		<p class="description"><?php esc_html_e( 'Recomendado: 1 tarjeta para celular.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render del campo modo de presentacion.
	 *
	 * @return void
	 */
	public static function render_display_mode_field() {
		$settings = self::get_settings();
		$options  = array(
			'grid'     => __( 'Grilla con paginacion', 'umag-eventos' ),
			'carousel' => __( 'Carrusel de eventos', 'umag-eventos' ),
		);

		foreach ( $options as $value => $label ) {
			?>
			<label style="display:block; margin-bottom:8px;">
				<input
					type="radio"
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>[display_mode]"
					value="<?php echo esc_attr( $value ); ?>"
					<?php checked( $settings['display_mode'], $value ); ?>
				/>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php
		}

		echo '<p class="description">' . esc_html__( 'El carrusel muestra controles laterales y desplazamiento horizontal. La grilla usa paginacion tradicional.', 'umag-eventos' ) . '</p>';
	}

	/**
	 * Render del campo de vista.
	 *
	 * @return void
	 */
	public static function render_card_view_field() {
		$settings = self::get_settings();
		$options  = array(
			'listado'  => __( 'Listado', 'umag-eventos' ),
			'grande'   => __( 'Grande', 'umag-eventos' ),
			'resumido' => __( 'Resumido', 'umag-eventos' ),
		);

		foreach ( $options as $value => $label ) {
			?>
			<label style="display:block; margin-bottom:8px;">
				<input
					type="radio"
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>[card_view]"
					value="<?php echo esc_attr( $value ); ?>"
					<?php checked( $settings['card_view'], $value ); ?>
				/>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php
		}

		echo '<p class="description">' . esc_html__( 'Listado equilibra imagen y texto, Grande prioriza impacto visual y Resumido muestra mas eventos en menos espacio.', 'umag-eventos' ) . '</p>';
	}

	/**
	 * Render del modo de fondo del contenedor.
	 *
	 * @return void
	 */
	public static function render_container_bg_mode_field() {
		$settings = self::get_settings();
		$options  = array(
			'transparent' => __( 'Transparente', 'umag-eventos' ),
			'solid'       => __( 'Color solido RGB', 'umag-eventos' ),
			'gradient'    => __( 'Degradado', 'umag-eventos' ),
		);

		foreach ( $options as $value => $label ) {
			?>
			<label style="display:block; margin-bottom:8px;">
				<input
					type="radio"
					name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_mode]"
					value="<?php echo esc_attr( $value ); ?>"
					<?php checked( $settings['container_bg_mode'], $value ); ?>
				/>
				<?php echo esc_html( $label ); ?>
			</label>
			<?php
		}
	}

	/**
	 * Render del color RGB del contenedor.
	 *
	 * @return void
	 */
	public static function render_container_bg_color_field() {
		$settings = self::get_settings();
		$value    = self::sanitize_hex_color_value( (string) $settings['container_bg_color'] );
		$value    = '' !== $value ? $value : '#f5f5f5';
		?>
		<div data-umag-bg-solid-fields>
			<input
				type="color"
				value="<?php echo esc_attr( $value ); ?>"
				oninput="this.nextElementSibling.value=this.value;"
				style="width:64px; height:36px; padding:2px; border:1px solid #c3c4c7; border-radius:4px; vertical-align:middle;"
			/>
			<input
				type="text"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_color]"
				value="<?php echo esc_attr( $value ); ?>"
				class="regular-text code"
				placeholder="#f5f5f5"
				pattern="^#([A-Fa-f0-9]{6})$"
				style="margin-left:8px;"
			/>
		</div>
		<p class="description"><?php esc_html_e( 'Formato requerido: hexadecimal de 6 digitos, por ejemplo #593d80.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de tipo de degradado.
	 *
	 * @return void
	 */
	public static function render_container_bg_gradient_type_field() {
		$settings = self::get_settings();
		$value    = in_array( $settings['container_bg_gradient_type'], array( 'linear', 'radial' ), true ) ? $settings['container_bg_gradient_type'] : 'linear';
		?>
		<div data-umag-bg-gradient-fields>
			<label style="display:block; margin-bottom:8px;">
				<input type="radio" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_gradient_type]" value="linear" <?php checked( $value, 'linear' ); ?> />
				<?php esc_html_e( 'Lineal', 'umag-eventos' ); ?>
			</label>
			<label style="display:block;">
				<input type="radio" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_gradient_type]" value="radial" <?php checked( $value, 'radial' ); ?> />
				<?php esc_html_e( 'Circular', 'umag-eventos' ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Render de transparencia del degradado.
	 *
	 * @return void
	 */
	public static function render_container_bg_gradient_opacity_field() {
		$settings = self::get_settings();
		$value    = max( 0, min( 100, absint( $settings['container_bg_gradient_opacity'] ) ) );
		?>
		<div data-umag-bg-gradient-fields>
			<input
				type="number"
				min="0"
				max="100"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_gradient_opacity]"
				value="<?php echo esc_attr( $value ); ?>"
				class="small-text"
			/>
		</div>
		<p class="description"><?php esc_html_e( '0 = totalmente transparente, 100 = color totalmente visible.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de ubicacion del degradado.
	 *
	 * @return void
	 */
	public static function render_container_bg_gradient_position_field() {
		$settings = self::get_settings();
		$current  = self::sanitize_gradient_position( (string) $settings['container_bg_gradient_position'] );
		$options  = self::get_gradient_position_options();
		?>
		<div data-umag-bg-gradient-fields>
			<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[container_bg_gradient_position]">
				<?php foreach ( $options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render del autoplay.
	 *
	 * @return void
	 */
	public static function render_carousel_autoplay_field() {
		$settings = self::get_settings();
		?>
		<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_autoplay]" value="0" />
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_autoplay]"
				value="1"
				<?php checked( $settings['carousel_autoplay'], 1 ); ?>
			/>
			<?php esc_html_e( 'Iniciar automaticamente al cargar la pagina', 'umag-eventos' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Recomendado para destacar eventos sin requerir interaccion inicial.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render del loop infinito.
	 *
	 * @return void
	 */
	public static function render_carousel_infinite_field() {
		$settings = self::get_settings();
		?>
		<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_infinite]" value="0" />
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_infinite]"
				value="1"
				<?php checked( $settings['carousel_infinite'], 1 ); ?>
			/>
			<?php esc_html_e( 'Repetir el carrusel de forma continua', 'umag-eventos' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Si lo desactivas, el carrusel se detendra al llegar al ultimo evento.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de la espera entre avances.
	 *
	 * @return void
	 */
	public static function render_carousel_delay_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			min="1000"
			step="100"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_delay]"
			value="<?php echo esc_attr( $settings['carousel_delay'] ); ?>"
			class="small-text"
		/>
		<p class="description"><?php esc_html_e( 'Tiempo en milisegundos antes de avanzar al siguiente evento.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de la duracion de la transicion.
	 *
	 * @return void
	 */
	public static function render_carousel_transition_field() {
		$settings = self::get_settings();
		?>
		<input
			type="number"
			min="150"
			step="50"
			name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_transition]"
			value="<?php echo esc_attr( $settings['carousel_transition'] ); ?>"
			class="small-text"
		/>
		<p class="description"><?php esc_html_e( 'Duracion de la animacion de desplazamiento en milisegundos.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render de pausa al hover.
	 *
	 * @return void
	 */
	public static function render_carousel_pause_hover_field() {
		$settings = self::get_settings();
		?>
		<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_pause_hover]" value="0" />
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_pause_hover]"
				value="1"
				<?php checked( $settings['carousel_pause_hover'], 1 ); ?>
			/>
			<?php esc_html_e( 'Pausar cuando el usuario pase el cursor encima', 'umag-eventos' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Mejora la lectura cuando el carrusel avanza automaticamente.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Render del arrastre.
	 *
	 * @return void
	 */
	public static function render_carousel_drag_field() {
		$settings = self::get_settings();
		?>
		<input type="hidden" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_drag]" value="0" />
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_NAME ); ?>[carousel_drag]"
				value="1"
				<?php checked( $settings['carousel_drag'], 1 ); ?>
			/>
			<?php esc_html_e( 'Permitir arrastrar con mouse o tactil', 'umag-eventos' ); ?>
		</label>
		<p class="description"><?php esc_html_e( 'Hace el carrusel mas natural en pantallas tactiles y equipos de escritorio.', 'umag-eventos' ); ?></p>
		<?php
	}

	/**
	 * Renderiza la pagina de ajustes.
	 *
	 * @return void
	 */
	public static function render_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Visualizacion de eventos', 'umag-eventos' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'umag_eventos_settings_group' );
				do_settings_sections( 'umag-eventos-settings' );
				submit_button( __( 'Guardar configuracion', 'umag-eventos' ) );
				?>
			</form>
		</div>
		<script>
			(function () {
				const modeInputs = document.querySelectorAll('input[name="<?php echo esc_js( self::OPTION_NAME ); ?>[container_bg_mode]"]');
				const solidBlocks = document.querySelectorAll('[data-umag-bg-solid-fields]');
				const gradientBlocks = document.querySelectorAll('[data-umag-bg-gradient-fields]');

				const updateState = () => {
					const checked = document.querySelector('input[name="<?php echo esc_js( self::OPTION_NAME ); ?>[container_bg_mode]"]:checked');
					const mode = checked ? checked.value : 'transparent';
					const solidActive = mode === 'solid';
					const gradientActive = mode === 'gradient';

					solidBlocks.forEach((block) => {
						block.style.opacity = solidActive ? '1' : '0.5';
						block.querySelectorAll('input, select, textarea').forEach((field) => {
							field.disabled = !solidActive;
						});
					});

					gradientBlocks.forEach((block) => {
						block.style.opacity = gradientActive ? '1' : '0.5';
						block.querySelectorAll('input, select, textarea').forEach((field) => {
							field.disabled = !gradientActive;
						});
					});
				};

				modeInputs.forEach((input) => input.addEventListener('change', updateState));
				updateState();
			})();
		</script>
		<?php
	}

	/**
	 * Renderiza la pagina de ajustes del carrusel.
	 *
	 * @return void
	 */
	public static function render_carousel_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Configuracion del carrusel', 'umag-eventos' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'umag_eventos_settings_group' );
				do_settings_sections( 'umag-eventos-carousel-settings' );
				submit_button( __( 'Guardar configuracion', 'umag-eventos' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Marca que se actualizo la configuracion para mostrar aviso.
	 *
	 * @param mixed $value     Nuevo valor.
	 * @param mixed $old_value Valor anterior.
	 * @return mixed
	 */
	public static function flag_settings_update( $value, $old_value ) {
		if ( is_admin() ) {
			set_transient( 'umag_eventos_settings_notice', 1, 30 );
		}

		return $value;
	}

	/**
	 * Renderiza aviso personalizado de guardado.
	 *
	 * @return void
	 */
	public static function render_settings_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! get_transient( 'umag_eventos_settings_notice' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( ! $screen || 'umag_evento_page_umag-eventos-settings' !== $screen->id && 'umag_evento_page_umag-eventos-carousel-settings' !== $screen->id ) {
			return;
		}

		delete_transient( 'umag_eventos_settings_notice' );
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Opciones actualizadas con exito', 'umag-eventos' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Sanitiza un color hexadecimal #rrggbb.
	 *
	 * @param string $value Valor recibido.
	 * @return string
	 */
	private static function sanitize_hex_color_value( $value ) {
		$value = trim( $value );

		if ( ! preg_match( '/^#([A-Fa-f0-9]{6})$/', $value, $matches ) ) {
			return '';
		}

		return '#' . strtolower( $matches[1] );
	}

	/**
	 * Opciones validas de ubicacion del degradado.
	 *
	 * @return array<string,string>
	 */
	private static function get_gradient_position_options() {
		return array(
			'center'       => __( 'Centro', 'umag-eventos' ),
			'top'          => __( 'Arriba', 'umag-eventos' ),
			'right'        => __( 'Derecha', 'umag-eventos' ),
			'bottom'       => __( 'Abajo', 'umag-eventos' ),
			'left'         => __( 'Izquierda', 'umag-eventos' ),
			'top-left'     => __( 'Arriba izquierda', 'umag-eventos' ),
			'top-right'    => __( 'Arriba derecha', 'umag-eventos' ),
			'bottom-left'  => __( 'Abajo izquierda', 'umag-eventos' ),
			'bottom-right' => __( 'Abajo derecha', 'umag-eventos' ),
		);
	}

	/**
	 * Sanitiza ubicacion del degradado.
	 *
	 * @param string $value Valor recibido.
	 * @return string
	 */
	private static function sanitize_gradient_position( $value ) {
		$value   = sanitize_key( $value );
		$options = self::get_gradient_position_options();

		return array_key_exists( $value, $options ) ? $value : 'center';
	}
}
