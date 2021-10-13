<?php

add_shortcode( 'carlist', 'carlist_shortcode' );
function carlist_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'fuel'         => '',
        'manufacturer' => '',
        'color'        => '',
        'showfilters'  => 0
    ), $atts, 'carlist' );

    wp_enqueue_style( 'carlist', CARLIST_URL . '/assets/css/main.css' );
    if ( $atts['showfilters'] ){
    	wp_enqueue_script( 'carlist', CARLIST_URL . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true );
    	wp_localize_script( 'carlist', 'carlist',
	        array( 
	            'ajaxurl' => admin_url( 'admin-ajax.php' ),
	            'nonce'   => wp_create_nonce("carlistajaxfilter"),
	        )
	    );
    }

    ob_start();

    ?>

    <div class="carlist-container">
    	<?php if ( $atts['showfilters'] ): ?>
    		<div class="carlist-overlay"></div>
    		<div class="carlist-filters">
    			<?php

    			global $wpdb;
    			$fuel_values         = carlist_get_fuel();
    			$manufacturer_values = $wpdb->get_col( "SELECT DISTINCT(meta_value) FROM {$wpdb->postmeta} WHERE meta_key='carlist_manufacturer'" );
    			$color_values        = $wpdb->get_col( "SELECT DISTINCT(meta_value) FROM {$wpdb->postmeta} WHERE meta_key='carlist_color'" );

    			?>

    			<select class="carlist-filter" data-key="fuel">
    				<option value="none"><?php esc_html_e( 'Select fuel', 'carslisting' ) ?></option>
    				<option value="all"><?php esc_html_e( 'All', 'carslisting' ) ?></option>
    				<?php foreach ( $fuel_values as $fuel_value ) { ?>
    					<option <?php selected( $fuel_value, $atts['fuel'] ) ?>><?php echo esc_html( $fuel_value ) ?></option>
    				<?php } ?>
    			</select>

    			<select class="carlist-filter" data-key="manufacturer">
    				<option value="none"><?php esc_html_e( 'Select brand', 'carslisting' ) ?></option>
    				<option value="all"><?php esc_html_e( 'All', 'carslisting' ) ?></option>
    				<?php foreach ( $manufacturer_values as $manufacturer_value ) { ?>
    					<option <?php selected( $manufacturer_value, $atts['manufacturer'] ) ?>><?php echo esc_html( $manufacturer_value ) ?></option>
    				<?php } ?>
    			</select>

    			<select class="carlist-filter" data-key="color">
    				<option value="none"><?php esc_html_e( 'Select color', 'carslisting' ) ?></option>
    				<option value="all"><?php esc_html_e( 'All', 'carslisting' ) ?></option>
    				<?php foreach ( $color_values as $color_value ) { ?>
    					<option <?php selected( $color_value, $atts['color'] ) ?>><?php echo esc_html( $color_value ) ?></option>
    				<?php } ?>
    			</select>

    		</div>
    	<?php endif ?>
    	<div class="carlist-content">
    		<?php

    		$args = array(
    			'post_type'      => 'car',
    			'post_status'    => 'publish',
    			'posts_per_page' => -1,
    			'meta_query'     => array(),
    		);

    		if ( '' != $atts['color'] && 'none' != $atts['color'] && 'all' != $atts['color'] ) {
				$args['meta_query'][] = array(
					'key'     => 'carlist_color',
		            'value'   => $atts['color'],
		            'compare' => '=',
				);
			}

			if ( '' != $atts['manufacturer'] && 'none' != $atts['manufacturer'] && 'all' != $atts['manufacturer'] ) {
				$args['meta_query'][] = array(
					'key'     => 'carlist_manufacturer',
		            'value'   => $atts['manufacturer'],
		            'compare' => '=',
				);
			}

			if ( '' != $atts['fuel'] && 'none' != $atts['fuel'] && 'all' != $atts['fuel'] ) {
				$args['meta_query'][] = array(
					'key'     => 'carlist_fuel',
		            'value'   => $atts['fuel'],
		            'compare' => '=',
				);
			}

    		$cars_query = new WP_Query( $args );
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
			}
			wp_reset_postdata();

    		?>
    	</div>
    </div>


    <?php

    $shortcode_output = ob_get_contents();
	ob_end_clean();
	return $shortcode_output;
 
}