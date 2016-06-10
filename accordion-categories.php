<?php 
/*
Plugin Name: Accordion Categories
Version: 1.0
Plugin URI: http://crosp.net
Description: This plugin provides widget for displaying categories hierarchy, like default, but with collapsing feature. You can also specify settings for displaying categories directly in widget. This widget was created to simplify displaying category hierarchy in sidebar
Author: Alexander Molochko
Author URI: http://crosp.net/about
*/

// Block direct requests
if ( !defined('ABSPATH')) {
	die('-1');
}
require_once("class-accordion-categories-widget.php");
require_once("inc/category_add_form_fields.php");
require_once("inc/category_edit_form_fields.php");
require_once("inc/save_field_value.php");
// Registering widget with hook method	
add_action( 'widgets_init', function(){
     register_widget( 'Accordion_Categories_Widget' );
});	

