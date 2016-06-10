<?php
// Block direct requests
if ( !defined('ABSPATH')) {
    die('-1');
}
// Save extra taxonomy fields callback function.
add_action( 'edited_category', 'pt_save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_category', 'pt_save_taxonomy_custom_meta', 10, 2 );
 
function pt_save_taxonomy_custom_meta( $term_id ) {
    var_dump($term_id);
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option( "taxonomy_$t_id", $term_meta );
    }
}