<?php
/**
 * Plugin Name: Harwinton Agricultural Society Events
 * Description: A custom event management plugin developed for the Harwinton Agricultural Society.
 * Author: Matt Thomas
 * Version: 0.0.1
 * Text Domain: hasEvents
 */

 /**
  * Register activity custom post type.
  */
add_action(
	'init', function () {
		register_post_type(
			'activity', array(
				'has_archive'       => true,
				'labels'            => array(
					'add_new'      => __( 'Add Activity', 'hasEvents' ),
					'add_new_item' => __( 'New Activity', 'hasEvents' ),
					'all_items'    => __( 'Activities', 'hasEvents' ),
					'edit_item'    => __( 'Edit Activity', 'hasEvents' ),
					'name'         => __( 'Events', 'hasEvents' ),
				),
				'public'            => true,
				'show_in_nav_menus' => true,
				'supports'          => array(
					'editor',
					'excerpt',
					'revisions',
					'title',
					'thumbnail',
				),
				'taxonomies'        => array(
					'events',
					'locations',
				),
			)
		);
	}
);

/**
 * Adds meta boxes to activites.
 */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box(
			'details', // $id
			'Activity Dates', // $title
			'show_meta_boxes', // $callback
			'activity', // $screen
			'normal', // $context
			'high' // $priority
		);
	}
);

/**
 * Add fields to custom meta box.
 */
function show_meta_boxes() {
	?>
	<input type="hidden" name="events_meta_box_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>">
	<p>
		<label for="begin">Begins
			<input type="datetime-local" name="begin" id="begin" class="regular-text" value="<?php echo get_value( 'begin' ); ?>">
		</label>
	</p>
	<p>
		<label for="end">Ends
			<input type="datetime-local" name="end" id="end" class="regular-text" value="<?php echo get_value( 'end' ); ?>">
		</label>
	</p>
	<?php
}

/**
 * Helper to properly return the meta value without throwing errors when not set.
 */
function get_value( $field ) {
	global $post;
	$meta = get_post_meta( $post->ID );
	return ( is_array( $meta ) && isset( $meta[ $field ] ) ) ? $meta[ $field ][0] : null;
}

/**
 * Enable saving of custom fields.
 */
add_action(
	'save_post', function ( $post_id ) {
		// verify nonce
		if ( array_key_exists( 'events_meta_box_nonce', $_POST ) && ! wp_verify_nonce( $_POST['events_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// check permissions
		if ( array_key_exists( 'post_type', $_POST ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if ( array_key_exists( 'begin', $_POST ) && array_key_exists( 'end', $_POST ) ) {
			$fields = array( 'begin', 'end' );

			foreach ( $fields as $field ) {
				$old = get_post_meta( $post_id, $field, true );
				$new = $_POST[ $field ];

				if ( $new && $new !== $old ) {
					update_post_meta( $post_id, $field, $new );
				} elseif ( '' === $new && $old ) {
					delete_post_meta( $post_id, $field, $old );
				}
			}
		}
	}
);

/**
 * Register Location taxonomy.
 */
add_action(
	'init',
	function () {
		$labels = array(
			'name'                       => __( 'Locations', 'hasEvents' ),
			'singular_name'              => __( 'Location', 'hasEvents' ),
			'search_items'               => __( 'Search Locations', 'hasEvents' ),
			'popular_items'              => __( 'Popular Locations', 'hasEvents' ),
			'all_items'                  => __( 'All Locations', 'hasEvents' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Location', 'hasEvents' ),
			'update_item'                => __( 'Update Location', 'hasEvents' ),
			'add_new_item'               => __( 'Add New Location', 'hasEvents' ),
			'new_item_name'              => __( 'New Location Name', 'hasEvents' ),
			'separate_items_with_commas' => __( 'Separate locations with commas', 'hasEvents' ),
			'add_or_remove_items'        => __( 'Add or remove locations', 'hasEvents' ),
			'choose_from_most_used'      => __( 'Choose from the most used locations', 'hasEvents' ),
			'menu_name'                  => __( 'Locations', 'hasEvents' ),
		);

		register_taxonomy(
			'location', 'activity', array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				// 'rewrite'               => array( 'slug' => 'program/location' ),
			)
		);
	},
	0
);

/**
 * Register Event taxonomy.
 */
add_action(
	'init',
	function () {
		$labels = array(
			'name'                       => __( 'Events', 'hasEvents' ),
			'singular_name'              => __( 'Event', 'hasEvents' ),
			'search_items'               => __( 'Search Events', 'hasEvents' ),
			'popular_items'              => __( 'Popular Events', 'hasEvents' ),
			'all_items'                  => __( 'All Events', 'hasEvents' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Event', 'hasEvents' ),
			'update_item'                => __( 'Update Event', 'hasEvents' ),
			'add_new_item'               => __( 'Add New Event', 'hasEvents' ),
			'new_item_name'              => __( 'New Event Name', 'hasEvents' ),
			'separate_items_with_commas' => __( 'Separate events with commas', 'hasEvents' ),
			'add_or_remove_items'        => __( 'Add or remove events', 'hasEvents' ),
			'choose_from_most_used'      => __( 'Choose from the most used events', 'hasEvents' ),
			'menu_name'                  => __( 'Events', 'hasEvents' ),
		);

		register_taxonomy(
			'event', 'activity', array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				// 'rewrite'               => array( 'slug' => 'program/event' ),
			)
		);
	},
	0
);

/**
 * Add Google Maps API key settings field.
 */
add_action(
	'admin_init', function() {
		// Register the setting.
		register_setting(
			'general',
			'google_maps_api_key',
			array(
				'string',
				'Google MAPS API Key',
			)
		);

		// Add setting field to admin general settings.
		add_settings_field(
			'google_maps_api_key',
			'Google MAPS API Key',
			'google_maps_api_key_callback',
			'general'
		);
	}
);

/**
 * Callback to render the settings field.
 */
function google_maps_api_key_callback() {
	$key = get_option( 'google_maps_api_key' );
	?>
	<input type="text" name="google_maps_api_key" value="<?php echo isset( $key ) ? esc_attr( $key ) : ''; ?>">
	<?php
}

/**
 * Adds hidden Google Map latitude and longitude taxonomy field.
 *
 * https://developer.wordpress.org/reference/hooks/taxonomy_add_form_fields/
 */
add_action( 'location_add_form_fields', 'render_map', 10, 1 );
add_action( 'location_edit_form_fields', 'render_map', 10, 1 );

function render_map( $term ) {
	?>
	<div class="form-field term-location-wrap">
		<div id="map"></div>
		<label for="latLng">Location</label>
		<input type="hidden" name="latLng" id="latLng" value="<?php echo property_exists( $term, 'term_id' ) ? get_term_meta( $term->term_id, 'latLng', true ) : ''; ?>" >
		<p>Click the map to set the exact location.</p>
	</div>
	<?php
}

/**
 * Render Google Map in admin for choosing location.
 */
add_action( 'location_edit_form', 'add_script', 10, 1 );
add_action( 'location_add_form', 'add_script', 10, 1 );

function add_script( $term ) {
	?>
	<style>
		#map {
			height: 400px;
			width: 400px
		}
	</style>
	<script>
		const input = document.getElementById('latLng');
		const markers = [];
		const savedValue = '<?php echo property_exists( $term, 'term_id' ) ? get_term_meta( $term->term_id, 'latLng', true ) : ''; ?>';

		function initMap() {
			const map = new google.maps.Map(document.getElementById('map'), {
				center: { lat: 41.763031, lng: -73.044465 },
				zoom: 17.75
			});

			/* Render saved marker */ 
			if(savedValue){
				const cords = savedValue.replace('(', '').replace(')', '').split(',');
				addMarker(new google.maps.LatLng(cords[0], cords[1]), map);
			}

			/* Add new marker when clicking the map */
			google.maps.event.addListener(map, 'click', function (event) {
				input.setAttribute('value', event.latLng);
				addMarker(event.latLng, map);
			});
		}

		function addMarker(position, map){
			// Ensures only one marker exists on the map.
			if(markers.length){
				markers.forEach(marker => {
					marker.setMap(null);
				});
			}

			const marker = new google.maps.Marker({
				position,
				map
			});

			markers.push(marker);

			// Remove existing marker if clicked
			google.maps.event.addListener(marker, 'click', function () {
				marker.setMap(null);
				input.setAttribute('value', '');
			});
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option( 'google_maps_api_key' ); ?>&callback=initMap"async defer></script>
	<?php
}

/**
 * Save latLang when creating location.
 */
add_action( 'created_location', 'save_latLang', 10, 1 );

function save_latLang( $term_id ) {
	if ( isset( $_POST['latLng'] ) ) {
		add_term_meta( $term_id, 'latLng', $_POST['latLng'], true );
	}
}

/**
 * Save latLang when editing location.
 */
add_action( 'edited_location', 'update_latLng', 10, 1 );

function update_latLng( $term_id ) {
	if ( isset( $_POST['latLng'] ) ) {
		update_term_meta( $term_id, 'latLng', $_POST['latLng'] );
	} else {
		update_term_meta( $term_id, 'latLng', '' );
	}
}
