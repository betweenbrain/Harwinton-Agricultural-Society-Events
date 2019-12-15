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
 * hasEvents Google Maps API Key:
 * AIzaSyAMTbtiwfB3H78Es2uoY1O93_3LsqzkgIc
 */
/*
function acf_init() {
    acf_update_setting('google_api_key', 'AIzaSyAMTbtiwfB3H78Es2uoY1O93_3LsqzkgIc');
}
add_action('acf/init', 'acf_init');
*/

function my_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyAMTbtiwfB3H78Es2uoY1O93_3LsqzkgIc';
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');