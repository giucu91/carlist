<?php

// Register Custom Post Type
function carlist_posttype() {

	$labels = array(
		'name'                  => _x( 'Cars', 'Post Type General Name', 'carslisting' ),
		'singular_name'         => _x( 'Car', 'Post Type Singular Name', 'carslisting' ),
		'menu_name'             => __( 'Cars', 'carslisting' ),
		'name_admin_bar'        => __( 'Car', 'carslisting' ),
		'archives'              => __( 'Item Archives', 'carslisting' ),
		'attributes'            => __( 'Item Attributes', 'carslisting' ),
		'parent_item_colon'     => __( 'Parent Item:', 'carslisting' ),
		'all_items'             => __( 'All Items', 'carslisting' ),
		'add_new_item'          => __( 'Add New Item', 'carslisting' ),
		'add_new'               => __( 'Add New', 'carslisting' ),
		'new_item'              => __( 'New Item', 'carslisting' ),
		'edit_item'             => __( 'Edit Item', 'carslisting' ),
		'update_item'           => __( 'Update Item', 'carslisting' ),
		'view_item'             => __( 'View Item', 'carslisting' ),
		'view_items'            => __( 'View Items', 'carslisting' ),
		'search_items'          => __( 'Search Item', 'carslisting' ),
		'not_found'             => __( 'Not found', 'carslisting' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'carslisting' ),
		'featured_image'        => __( 'Featured Image', 'carslisting' ),
		'set_featured_image'    => __( 'Set featured image', 'carslisting' ),
		'remove_featured_image' => __( 'Remove featured image', 'carslisting' ),
		'use_featured_image'    => __( 'Use as featured image', 'carslisting' ),
		'insert_into_item'      => __( 'Insert into item', 'carslisting' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'carslisting' ),
		'items_list'            => __( 'Items list', 'carslisting' ),
		'items_list_navigation' => __( 'Items list navigation', 'carslisting' ),
		'filter_items_list'     => __( 'Filter items list', 'carslisting' ),
	);
	$args = array(
		'label'                 => __( 'Car', 'carslisting' ),
		'description'           => __( 'Post Type Description', 'carslisting' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-car',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => false,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'car', $args );

}
add_action( 'init', 'carlist_posttype', 0 );