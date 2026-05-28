<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UMAG_Eventos_Post_Type {
	/**
	 * Registra el Custom Post Type y taxonomias.
	 *
	 * @return void
	 */
	public static function register() {
		self::register_post_type();
		self::register_taxonomies();
	}

	/**
	 * Registra el tipo de contenido evento.
	 *
	 * @return void
	 */
	private static function register_post_type() {
		$labels = array(
			'name'               => __( 'Eventos UMAG', 'umag-eventos' ),
			'singular_name'      => __( 'Evento', 'umag-eventos' ),
			'menu_name'          => __( 'Eventos UMAG', 'umag-eventos' ),
			'name_admin_bar'     => __( 'Evento', 'umag-eventos' ),
			'add_new'            => __( 'Agregar nuevo', 'umag-eventos' ),
			'add_new_item'       => __( 'Agregar nuevo evento', 'umag-eventos' ),
			'new_item'           => __( 'Nuevo evento', 'umag-eventos' ),
			'edit_item'          => __( 'Editar evento', 'umag-eventos' ),
			'view_item'          => __( 'Ver evento', 'umag-eventos' ),
			'all_items'          => __( 'Todos los eventos UMAG', 'umag-eventos' ),
			'search_items'       => __( 'Buscar eventos', 'umag-eventos' ),
			'not_found'          => __( 'No se encontraron eventos.', 'umag-eventos' ),
			'not_found_in_trash' => __( 'No hay eventos en la papelera.', 'umag-eventos' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-calendar-alt',
			'has_archive'     => true,
			'rewrite'         => array( 'slug' => 'eventos' ),
			'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'menu_position'   => 20,
			'capability_type' => 'post',
		);

		register_post_type( 'umag_evento', $args );
	}

	/**
	 * Registra taxonomias base para categorizar eventos.
	 *
	 * @return void
	 */
	private static function register_taxonomies() {
		$category_labels = array(
			'name'          => __( 'Categorias de evento', 'umag-eventos' ),
			'singular_name' => __( 'Categoria de evento', 'umag-eventos' ),
			'search_items'  => __( 'Buscar categorias', 'umag-eventos' ),
			'all_items'     => __( 'Todas las categorias', 'umag-eventos' ),
			'edit_item'     => __( 'Editar categoria', 'umag-eventos' ),
			'update_item'   => __( 'Actualizar categoria', 'umag-eventos' ),
			'add_new_item'  => __( 'Agregar nueva categoria', 'umag-eventos' ),
			'menu_name'     => __( 'Categorias', 'umag-eventos' ),
		);

		register_taxonomy(
			'umag_evento_categoria',
			'umag_evento',
			array(
				'labels'       => $category_labels,
				'hierarchical' => true,
				'public'       => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'categoria-evento' ),
			)
		);

		$location_labels = array(
			'name'          => __( 'Ubicaciones', 'umag-eventos' ),
			'singular_name' => __( 'Ubicacion', 'umag-eventos' ),
			'search_items'  => __( 'Buscar ubicaciones', 'umag-eventos' ),
			'all_items'     => __( 'Todas las ubicaciones', 'umag-eventos' ),
			'edit_item'     => __( 'Editar ubicacion', 'umag-eventos' ),
			'update_item'   => __( 'Actualizar ubicacion', 'umag-eventos' ),
			'add_new_item'  => __( 'Agregar nueva ubicacion', 'umag-eventos' ),
			'menu_name'     => __( 'Ubicaciones', 'umag-eventos' ),
		);

		register_taxonomy(
			'umag_evento_ubicacion',
			'umag_evento',
			array(
				'labels'       => $location_labels,
				'hierarchical' => true,
				'public'       => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => 'ubicacion-evento' ),
			)
		);
	}
}
