<?php

/* Add meta boxes on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'carlist_add_post_meta_box' );

/* Create one or more meta boxes to be displayed on the post editor screen. */
function carlist_add_post_meta_box() {
	add_meta_box( 'carlistspecification', esc_html__( 'Car specification', 'carslisting' ), 'carlistspecification_metabox', 'car', 'normal', 'default' );
}

function carlist_get_fuel(){
	return apply_filters( 'carlist_get_fuel', array( 'Benzina', 'Diesel', 'Electric', 'Hibrid' ) );
}

/* Display the post meta box. */
function carlistspecification_metabox( $post ) { 

	wp_nonce_field( basename( __FILE__ ), 'carlistspecificationnonce' );
	$fuel_values = carlist_get_fuel();

	$fuel         = get_post_meta( $post->ID, 'carlist_fuel', true );
	$manufacturer = get_post_meta( $post->ID, 'carlist_manufacturer', true );
	$color        = get_post_meta( $post->ID, 'carlist_color', true );

	?>
	<style type="text/css">
		.carlistgrid {
			display: flex;
		}
		.carlistgrid label {
			width: 150px;
		}
		.carlistgrid input,
		.carlistgrid select {
			flex-grow: 1;
		}
	</style>
	<p class="carlistgrid">
		<label for="carlist-fuel"><?php esc_html_e( "Fuel", 'carslisting' ); ?></label>
		<select id="carlist-fuel" name="carlist_fuel">
			<option>None</option>
			<?php

			foreach ( $fuel_values as $fuel_value ) {
				echo '<option ' . selected( $fuel, $fuel_value, false ) . '>' . esc_html( $fuel_value ) . '</option>';
			}

			?>
		</select>
	</p>

	<p class="carlistgrid">
		<label for="carlist-manufacturer"><?php esc_html_e( "Manufacturer", 'carslisting' ); ?></label>
		<input type="text" id="carlist-manufacturer" name="carlist_manufacturer" value="<?php echo esc_attr( $manufacturer ) ?>">
	</p>

	<p class="carlistgrid">
		<label for="carlist-color"><?php esc_html_e( "Color", 'carslisting' ); ?></label>
		<input type="text" id="carlist-color" name="carlist_color" value="<?php echo esc_attr( $color ) ?>">
	</p>
<?php }


/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'carlist_save_custom_fields', 10, 2 );

/* Save the meta box’s post metadata. */
function carlist_save_custom_fields( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['carlistspecificationnonce'] ) || !wp_verify_nonce( $_POST['carlistspecificationnonce'], basename( __FILE__ ) ) ){
		return $post_id;
	}

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ){
		return $post_id;
	}

	if ( isset( $_POST['carlist_fuel'] ) ) {
		$values = carlist_get_fuel();
		if ( in_array( $_POST['carlist_fuel'], $values ) ) {
			update_post_meta( $post_id, 'carlist_fuel', $_POST['carlist_fuel'] );
		}else{
			delete_post_meta( $post_id, 'carlist_fuel' );
		}
	}else{
		delete_post_meta( $post_id, 'carlist_fuel' );
	}
    
	if ( isset( $_POST['carlist_manufacturer'] ) ) {
		update_post_meta( $post_id, 'carlist_manufacturer', sanitize_text_field($_POST['carlist_manufacturer']) );
	}else{
		delete_post_meta( $post_id, 'carlist_manufacturer' );
	}

	if ( isset( $_POST['carlist_color'] ) ) {
		update_post_meta( $post_id, 'carlist_color', sanitize_text_field($_POST['carlist_color']) );
	}else{
		delete_post_meta( $post_id, 'carlist_color' );
	}

}

