<?php
/*
Plugin Name: Guesty Calendar Widgets
Plugin URI: 
Description: Display guesty calendar widgets
Author: Braudy Pedrosa
Version: 2.1.2
Author URI: http://buildupbookings.com/
*/ 

// avoid direct access
if ( !function_exists('add_filter') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if(!defined('GUESTY_VERSION')){
	define('GUESTY_VERSION', "1.0"); 
}
if(!defined('GUESTY_DIR')){
	define('GUESTY_DIR', plugin_dir_path( __FILE__ )); 
}
if(!defined('GUESTY_URL')){
	define('GUESTY_URL', plugin_dir_url( __FILE__ )); 
}

// include ACF as required plugin
if(!class_exists('ACF')) {
  define( 'GUESTY_ACF_PATH', GUESTY_DIR . '/includes/acf/' );
  define( 'GUESTY_ACF_URL', GUESTY_URL . '/includes/acf/' );
  
  include_once(GUESTY_ACF_PATH.'acf.php' );
  include_once(GUESTY_DIR.'/includes/libraries/acf-photo-gallery/navz-photo-gallery.php');
}

// include FacetWP as required plugin
if(!class_exists('FacetWP')) {
  include_once(GUESTY_DIR.'/includes/facetwp/facetwp/index.php');
  include_once(GUESTY_DIR.'/includes/facetwp/facetwp-map-facet/facetwp-map-facet.php');
}


include_once(GUESTY_DIR.'functions.php');
include_once(GUESTY_DIR.'shortcodes.php');
include_once(GUESTY_DIR.'custom-urls.php');

// include custom template loader
require GUESTY_DIR . '/includes/class-gamajo-template-loader.php';
require GUESTY_DIR . '/includes/class-bookerville-template-loader.php';


add_filter('acf/settings/url', 'acf_settings_url');
function acf_settings_url( $url ) {
    return GUESTY_ACF_URL;
}

// save fields as JSON
add_filter('acf/settings/save_json', '_guesty_acf_json_save_point');
function _guesty_acf_json_save_point( $path ) {
    $path = GUESTY_DIR . '/acf-json';
    return $path;
}

// load fields as JSON and sync support
add_filter('acf/settings/load_json', '_guesty_acf_json_load_point');
function _guesty_acf_json_load_point( $paths ) {
    unset($paths[0]);
    $paths[] = GUESTY_DIR . '/acf-json';
    return $paths;
}

// add read only field to textarea fields
add_action('acf/render_field_settings/type=textarea', 'add_readonly_and_disabled_to_text_field');
function add_readonly_and_disabled_to_text_field($field) {
    acf_render_field_setting( $field, array(
      'label'      => __('Read Only?','acf'),
      'instructions'  => '',
      'type'      => 'radio',
      'name'      => 'readonly',
      'choices'    => array(
        1        => __("Yes",'acf'),
        0        => __("No",'acf'),
      ),
      'layout'  =>  'horizontal',
    ));
    acf_render_field_setting( $field, array(
      'label'      => __('Disabled?','acf'),
      'instructions'  => '',
      'type'      => 'radio',
      'name'      => 'disabled',
      'choices'    => array(
        1        => __("Yes",'acf'),
        0        => __("No",'acf'),
      ),
      'layout'  =>  'horizontal',
    ));
  }
  


  // load template
function _guesty_widgets_load_template(){
    $page = isset($_GET['page']) ? $_GET['page'] : "";
    include_once(GUESTY_DIR.'/settings.php');
}

function _guesty_widgets_register_menu(){
	add_menu_page( 
		__( 'Guesty Widgets', 'textdomain' ),
		'Guesty Widgets',
		'manage_options',
		'widgets_widgets',
		'_guesty_widgets_load_template',
		'dashicons-store',
	); 
}
add_action( 'admin_menu', '_guesty_widgets_register_menu' );


// initialize custom listing post type
function _guesty_post_type() {
	register_post_type( 'guesty_listings',
	
		array('labels' => array(
				'name' => __('Guesty Listings', 'jointswp'), /* This is the Title of the Group */
				'singular_name' => __('Guesty Listing', 'jointswp'), /* This is the individual type */
				'all_items' => __('All Guesty Listings', 'jointswp'), /* the all items menu item */
				'add_new' => __('Add New Guesty Listing', 'jointswp'), /* The add new menu item */
				'add_new_item' => __('Add New Guesty Listing', 'jointswp'), /* Add New Display Title */
				'edit' => __( 'Edit Listing', 'jointswp' ), /* Edit Dialog */
				'edit_item' => __('Edit Listing', 'jointswp'), /* Edit Display Title */
				'new_item' => __('New Guesty Listing', 'jointswp'), /* New Display Title */
				'view_item' => __('View Guesty Listing', 'jointswp'), /* View Display Title */
				'search_items' => __('Search', 'jointswp'), /* Search Custom Type Title */
				'not_found' =>  __('Nothing found in the Database.', 'jointswp'), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'jointswp'), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-store', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'rewrite'	=> array( 'slug' => 'property', 'with_front' => true ), /* you can specify its url slug */
			'has_archive' => 'properties', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields'),
			'taxonomies' => array('post_tag')	

		) /* end of options */

	); /* end of register post type */

}
add_action( 'init', '_guesty_post_type');
  
//create a custom taxonomy for listing post type
function _guesty_taxonomy() {
  
  $labels = array(
    'name' => _x( 'Amenities', 'taxonomy general name' ),
    'singular_name' => _x( 'Amenity', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Amenities' ),
    'all_items' => __( 'All Amenities' ),
    'parent_item' => __( 'Parent Amenity' ),
    'parent_item_colon' => __( 'Parent Amenity:' ),
    'edit_item' => __( 'Edit Amenity' ), 
    'update_item' => __( 'Update Amenity' ),
    'add_new_item' => __( 'Add New Amenity' ),
    'new_item_name' => __( 'New Amenity Name' ),
    'menu_name' => __( '	Amenities' ),
  );    
  
// Now register the taxonomy

register_taxonomy('amenities', array('guesty_listings'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'amenities' ),
  ));
  
}

add_action( 'init', '_guesty_taxonomy');
flush_rewrite_rules();


function _guesty_property_single_page_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'guesty_listings' ) {
        if ( file_exists( GUESTY_DIR . '/views/single-property.php' ) ) {
            return GUESTY_DIR . '/views/single-property.php';
        }
    }

    return $single;

}
add_filter('single_template', '_guesty_property_single_page_template');

function _guesty_widgets_enqueue_scripts(){
	wp_enqueue_script( 'gc-datepicker-script', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array('jquery'));
	wp_enqueue_style( 'gc-datepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css' );
  wp_enqueue_style('gl-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css' );
  wp_enqueue_style('gl-slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
  wp_enqueue_style('gl-slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
  wp_enqueue_script('gl-slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'));
	wp_enqueue_script( 'gc-script', GUESTY_URL.'js/custom.js', array('jquery'), '1.0' );
	wp_enqueue_style( 'gc-style', GUESTY_URL.'css/style.css', '1.0' );
}

add_action('wp_enqueue_scripts', '_guesty_widgets_enqueue_scripts');


// defaults
// Secret : I0ttZOeFnPRXY2F4rg5QR2DRrVbLioPFPQ2UFR5izs_N0J2K92b6hGk2_wdZQfTo
// ID: 0oacs1pg2l7bvKhwh5d7