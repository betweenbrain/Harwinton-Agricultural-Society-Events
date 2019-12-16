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
		$args = array(
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
		);
		register_post_type( 'activity', $args );
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
 * Render field when creating and editing taxonomy.
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

add_action( 'location_edit_form', 'add_script', 10, 1 );
add_action( 'location_add_form', 'add_script', 10, 1 );

function add_script( $term ) {
	$latLng = property_exists( $term, 'term_id' ) ? get_term_meta( $term->term_id, 'latLng', true ) : '';
	?>
	<style>
		#map {
			height: 400px;
			width: 400px
		}
	</style>
	<script>
		const input = document.getElementById('latLng');
		const latLng = '<?php echo $latLng; ?>';

		function initMap() {
			const map = new google.maps.Map(document.getElementById('map'), {
				center: { lat: 41.763031, lng: -73.044465 },
				zoom: 17.75
			});
			// TODO: Refactor methods into one.

			/** 
			* Render saved marker
			*/ 
			if(latLng){
				const cords = latLng.replace('(', '').replace(')', '').split(',');
				const marker = new google.maps.Marker({
					position: new google.maps.LatLng(cords[0], cords[1]),
					map: map,
				});

				google.maps.event.addListener(marker, 'click', function () {
					marker.setMap(null);
					input.setAttribute('value', '');
				});
			}

			google.maps.event.addListener(map, 'click', function (event) {
				// TODO: Unset other markers.

				/** 
				* Add a new marker
				*/
				const marker = new google.maps.Marker({
					position: event.latLng,
					map: map,
				});

				// Update hidden field
				input.setAttribute('value', event.latLng);

				// Remove existing marker if clicked
				google.maps.event.addListener(marker, 'click', function () {
					marker.setMap(null);
					input.setAttribute('value', '');
				});
			});
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=[API KEY GOES HERE]&callback=initMap"
		async defer></script>

	<?php
}

/**
 * Save latLang when created
 */
add_action( 'created_location', 'save_latLang', 10, 1 );

function save_latLang( $term_id ) {
	if ( isset( $_POST['latLng'] ) ) {
		add_term_meta( $term_id, 'latLng', $_POST['latLng'], true );
	}
}

/**
 * Save latLang when editing
 */
add_action( 'edited_location', 'update_latLng', 10, 1 );

function update_latLng( $term_id ) {
	if ( isset( $_POST['latLng'] ) ) {
		update_term_meta( $term_id, 'latLng', $_POST['latLng'] );
	} else {
		update_term_meta( $term_id, 'latLng', '' );
	}
}
