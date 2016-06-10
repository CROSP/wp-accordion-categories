<?php
// Block direct requests
if ( !defined('ABSPATH')) {
	die('-1');
}
// Add the field to the Add New Category page
add_action( 'category_add_form_fields', 'pt_taxonomy_add_new_meta_field', 10, 2 );
 
function pt_taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[cat_icon]"><?php _e( 'Category Font Awesome Icon', 'pt' ); ?></label>
        <input type="text" name="term_meta[cat_icon]" id="term_meta[cat_icon]" value="">
        <p class="description"><?php printf(__( 'Choose your category icon from <a href="%s" target="_blank">Font Awesome Icons</a>. For example: <b>fa-wordpress</b>','pt' ), 'http://fontawesome.io/icons/'); ?></p>
    </div>
<?php
}