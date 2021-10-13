<?php
/*
Plugin Name: Cars listing
Version: 1.0.0
Author: George Ciobanu
Text Domain: carslisting
Domain Path: /languages
*/

define( "CARLIST_URL", plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', 'run_carlist', 90 );
function run_carlist() {

	load_plugin_textdomain( 'carslisting', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Post Type code
	require_once 'includes/posttype.php';

	// Metabox code
	require_once 'includes/metabox.php';

	// Shortcode
	require_once 'includes/shortcode.php';

}

add_action( "wp_ajax_carlist_filter", "carlist_filter");
add_action( "wp_ajax_nopriv_carlist_filter", "carlist_filter");
function carlist_filter(){

	if ( ! wp_verify_nonce( $_REQUEST['nonce'], "carlistajaxfilter")) {
		wp_send_json_error();
		die();
	}

	$fuel         = $_REQUEST['fuel'];
	$manufacturer = $_REQUEST['manufacturer'];
	$color        = $_REQUEST['color'];

	$args = array(
		'post_type'      => 'car',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => array(),
	);

	if ( '' != $color && 'none' != $color && 'all' != $color ) {
		$args['meta_query'][] = array(
			'key'     => 'carlist_color',
            'value'   => sanitize_text_field( $color ),
            'compare' => '=',
		);
	}

	if ( '' != $manufacturer && 'none' != $manufacturer && 'all' != $manufacturer ) {
		$args['meta_query'][] = array(
			'key'     => 'carlist_manufacturer',
            'value'   => sanitize_text_field( $manufacturer ),
            'compare' => '=',
		);
	}

	if ( '' != $fuel && 'none' != $fuel && 'all' != $fuel ) {
		$args['meta_query'][] = array(
			'key'     => 'carlist_fuel',
            'value'   => sanitize_text_field( $fuel ),
            'compare' => '=',
		);
	}

	$cars_query = new WP_Query( $args );

	ob_start();

	if ( $cars_query->have_posts() ) {
	    while ( $cars_query->have_posts() ) {
	        $cars_query->the_post();
	        $fuel         = get_post_meta( get_the_ID(), 'carlist_fuel', true );
			$manufacturer = get_post_meta( get_the_ID(), 'carlist_manufacturer', true );
			$color        = get_post_meta( get_the_ID(), 'carlist_color', true );
			?>

			<div class="carlist-item">
				<div class="carlist-title"><?php the_title(); ?></div>
				<div class="clarlist-info">
					<div><?php echo esc_html( $manufacturer ); ?></div>
					<div><?php echo esc_html( $fuel ); ?></div>
					<div><?php echo esc_html( $color ); ?></div>
				</div>
			</div>

			<?php
	    }
	}else{
		echo '<p>No cars was found</p>';
	}

	$cars_markup = ob_get_contents();
	ob_end_clean();
	wp_reset_postdata();

	wp_send_json_success( $cars_markup );
	die();


}

function carlist_activation() {

	$demo = get_option( 'carlistdemo' );
	if ( $demo ) {
		return;	
	}

	$cars = array(
		array( 'VW Scirroco', 'VW', 'Benzina', 'grey' ),
		array( 'Audi A5', 'Audi', 'Benzina', 'green' ),
		array( 'Audi A6', 'Audi', 'Diesel', 'white' ),
		array( 'Dacia Logan', 'Dacia', 'Diesel', 'red' ),
		array( 'Dacia Sandero', 'Dacia', 'Diesel', 'red' ),
		array( 'Dacia Spring', 'Dacia', 'Electric', 'white' ),
		array( 'VW ID4', 'VW', 'Electric', 'green' ),
		array( 'Audi eTron', 'Audi', 'Electric', 'grey' ),
		array( 'VW Passat', 'VW', 'Benzina', 'red' ),
		array( 'Dacia Duster', 'Dacia', 'Diesel', 'red' ),
	);

	foreach ( $cars as $car ) {

		$car_id = wp_insert_post( array(
			'post_author' => 1,
			'post_title'  => $car[0],
			'post_status' => 'publish',
			'post_type'   => 'car',
		));

		update_post_meta( $car_id, 'carlist_fuel', $car[2] );
		update_post_meta( $car_id, 'carlist_manufacturer', $car[1] );
		update_post_meta( $car_id, 'carlist_color', $car[3] );

	}

	// insert page without filter
	wp_insert_post(array(
		'post_author' => 1,
		'post_title'  => 'Pagina fara filtre',
		'post_content' => '[carlist]',
		'post_status' => 'publish',
		'post_type'   => 'page',
	));

	// insert page with filter
	wp_insert_post(array(
		'post_author' => 1,
		'post_title'  => 'Pagina cu filtre',
		'post_content' => '[carlist showfilters="1"]',
		'post_status' => 'publish',
		'post_type'   => 'page',
	));

	// insert page with preselected filter
	wp_insert_post(array(
		'post_author' => 1,
		'post_title'  => 'Pagina cu filtre preselectate',
		'post_content' => '[carlist manufacturer="Dacia" fuel="Diesel" color="red" showfilters="1"]',
		'post_status' => 'publish',
		'post_type'   => 'page',
	));

	add_option( 'carlistdemo', 1, '', false );

}
register_activation_hook( __FILE__, 'carlist_activation' );