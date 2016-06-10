<?php
// Block direct requests
if ( !defined('ABSPATH')) {
    die('-1');
}
// Add the field to the Edit Category page
add_action( 'category_edit_form_fields', 'pt_taxonomy_edit_meta_field', 10, 2 );
 
function pt_taxonomy_edit_meta_field($term) {
 
    // put the term ID into a variable
    $t_id = $term->term_id;
 
    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option( "taxonomy_$t_id" ); ?>
    <tr class="form-field">
    <th scope="row" valign="top"><label for="term_meta[cat_icon]"><?php _e( 'Category Font Awesome Icon', 'pt' ); ?></label></th>
        <td>
            <input type="text" name="term_meta[cat_icon]" id="term_meta[cat_icon]" value="<?php echo esc_attr( $term_meta['cat_icon'] ) ? esc_attr( $term_meta['cat_icon'] ) : ''; ?>">
            <p class="description"><?php printf(__( 'Choose your category icon from <a href="%s" target="_blank">Font Awesome Icons</a>. For example: <b>fa-wordpress</b>','pt' ), 'http://fontawesome.io/icons/'); ?></p>
        </td>
    </tr>
<?php
}