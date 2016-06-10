<?php
// Block direct requests
if ( !defined('ABSPATH')) {
	die('-1');
}
class Accordion_Categories_Widget extends WP_Widget {
	/** 
	 *	Constants 
	**/
	// Settings
	const INVALID_CATEGORY_ID = -1;
	const SHOW_POST_COUNT = 'show_post_count';
	const TITLE = 'title';
	const USE_BOOTSTRAP_STYLE = 'use_bootstrap_style';
	const SHOW_EMPTY_CATEGORIES = 'show_empty_categories';
	// CSS Classes
	const CLASS_WIDGET_MAIN = 'accordion-categories-widget';
	// Parent child item, item which has children and has parent category at the same time
	const CLASS_CHILD_PARENT_ITEM = 'cat-children-parent';
	// Parent item that has no parent itself but has children level 0
	const CLASS_TOP_CHILD_PARENT_ITEM = 'cat-top-children-parent';
	// First parent level 0 top level
	const CLASS_PARENT_TOP_ITEM = 'cat-parent-empty';
	const CLASS_CATEGORY_LIST = 'cat-list';
	const CLASS_IS_ITEM_EXPANDED = 'expanded';
	const CLASS_INVISIBLE_LIST = 'invisible-list';
	const CLASS_VISIBLE_LIST = 'visible-list';
	// Item which is the last child item which has parent
	const CLASS_CHILD_ITEM = 'cat-child';
	const CLASS_EXPAND_INDICATOR = 'expand-indicator';
	const CLASS_ITEM_ICON_CONTAINER = 'item-icon-container';
	const CLASS_ITEM_REFERENCE = 'item-reference';
	// Common class for each category will set as id
	const CATEGORY_ID = 'cat-id-%d';
	// Patterns to replace
	const PATTERN_CHILD_CLASS = '%child-class';
	const PATTERN_LIST_CLASS = '%list-class';
	const PATTERN_DROPDOWN_MENU = '%dropdown';
	// Font Awesome Icons
	const ICON_DEFAULT = 'fa-tasks';
	const ICON_EXPAND = 'fa-chevron-down';
	/** 
	 *	Variables
	**/
	// Settings
	public $term_type = 'category';
	public $title;
	public $show_post_count = true;
	public $show_empty_categories = true;
	public $use_bootstrap_style = true;
	public $current_category_id = self::INVALID_CATEGORY_ID;
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Accordion_Categories_Widget', // Base ID
			__('Accordion Categories Menu', 'text_domain'), // Name
			array('description' => __( 'This widget allows to display categories hierarchy in an accordion style', 'text_domain' ),) // Args
		);
		
	}
	/** 
	 * Init widget (styles, scripts)
	 * Enqueue plugin style-file
	 */
	public function init_widget($instance) {
	    wp_register_style( 'accordion-category-style', plugins_url('accordion-category-style.css', __FILE__) );
	    wp_enqueue_style( 'accordion-category-style' );
	    // Register the script like this for a plugin:
	    wp_enqueue_script('jquery');
    	wp_register_script( 'accordion-category-script', plugins_url( 'accordion-categories.js', __FILE__ ) );
    	wp_enqueue_script( 'accordion-category-script' );
    	$this->parse_instance($instance);
	}
	/**
	 * Traverse over terms, in this case categories
	 *
	 * @param $parent id of parent category
	 * @param $current_level level in order to keep depth
	 * @param $previouse_result This is a bit tricky, this argument is passed in order to replace style of <li> tag * in case this is last child category item (that doesn't have any child categories and it is not the top level * category 
	 */ 
	public function traverse_term_tree($parent = 0,$current_level = 0,&$prevous_result = '') 
	{
	   	$result = '';
		$args = array(
			'hide_empty' => ($this->show_empty_categories ? '0':'1'),
	        'orderby'     => 'name',
	        'order'       => 'ASC',
	        'taxonomy'    => $this->term_type,
	        'pad_counts'  => 1
	    );
    	//I'll leave it to you to check for error objects etc.
    	$categories = get_categories($args);
    	$next = wp_list_filter($categories,array('parent'=>$parent));
	    if ($next) {
	    	// Increase current depth level
	    	$current_level++;
	    	// Create new list
	        $result .= '<ul class="'. self::CLASS_CATEGORY_LIST . ' ' . self::PATTERN_LIST_CLASS . ($this->use_bootstrap_style ? ' list-group' : '')
	        .'">';
	        foreach ($next as $cat) {
	        	// Form default output of list item
	        	// Formatting li (list item)
	        	// Retrieve category icon
	        	$cat_data 	= get_option("taxonomy_$cat->term_id");
	        	// 1. Assign unique id 
	            $result .= 
	            '<li id="'. sprintf(self::CATEGORY_ID,$cat->term_id);
	            $result .= '" class="';
	            // 2. Assign pattern to replace later
	            $result .= self::PATTERN_CHILD_CLASS . ($this->use_bootstrap_style ? ' list-group-item'
	            . ($cat->term_id == $this->current_category_id ? ' active' : '') :'') .'">';
	            // 3. Show post count if necessary
            	$result .= '<div class="'. self::CLASS_ITEM_ICON_CONTAINER .'">';
	            $result .= ($this->show_post_count && $this->use_bootstrap_style ? 
	            	sprintf('<span class="badge">%d</span>',number_format_i18n($cat->count)) : '');
	            $result .= self::PATTERN_DROPDOWN_MENU;
	            $result .= '</div>';
	        	// 4. Show link
	            $result .= '<a class="' . self::CLASS_ITEM_REFERENCE . '" href="' . get_term_link($cat->slug, $cat->taxonomy);
	            // 5. Assign link title
	            $result .= '" title="' . sprintf(__("View all posts in %s"), $cat->name) . '" ' . '>';
	            $result .= '<i class="fa ' . (isset($cat_data['cat_icon']) ? $cat_data['cat_icon'] : self::ICON_DEFAULT) . ' fa-fw" aria-hidden="true"></i>&nbsp; ';
	            // 6. Show category name
	            $result .= $cat->name; 
	            // 7. Show post count if neccessary (without bootstrap style)
	            $result .= ($this->show_post_count && !$this->use_bootstrap_style ? ' ' 
	            	. number_format_i18n($cat->count) . ' ' : '') . '</a>';
	           	$result .= '<div class="extra-space-block"></div>';
	            // Set recusive result to empty string as default
	            $recursive_result = '';
	            // Check if this is not top level category
	            if($cat->term_id !== 0) {
	            	// Call function recursively
	            	$recursive_result = self::traverse_term_tree($cat->term_id,$current_level,$result);
	            }
	            // Append recursive result to existing one
	            $result .= $recursive_result;
	            // !IMPORTANT if while recursive call pattern was not replaced (not last child)
	            // We need to replace it by ourselves first case is when this is top level category 
	            // (recursive result is empty) and second is when recursive result is not empty in this case 
	            // we found category that has children
	            $result =  str_replace(self::PATTERN_CHILD_CLASS,!empty($recursive_result) ? ($current_level > 1 ? 
	            	self::CLASS_CHILD_PARENT_ITEM : self::CLASS_TOP_CHILD_PARENT_ITEM) : self::CLASS_PARENT_TOP_ITEM , $result);
	            $result = str_replace(self::PATTERN_DROPDOWN_MENU,(!empty($recursive_result) ? '<i class="fa '. 
	            	self::CLASS_EXPAND_INDICATOR . ' '. self::ICON_EXPAND .'"></i>' : ''),
	            		$result);
	            $result = str_replace(self::PATTERN_LIST_CLASS,$current_level > 1 ? 
	            	self::CLASS_INVISIBLE_LIST : self::CLASS_VISIBLE_LIST, $result);
	        }

	        // Close tags
	        $result .= '</li>';
	        $result .= '</ul>';
	    }
	    else {
			// Detect if this is last child
			// That means that we need to replace pattern with specific class CLASS_CHILD_ITEM
	        $last_child = $current_level > 1;
	        if($last_child && !empty($prevous_result)) {
	        	// Modify previous result, so this will affect string, because of passing by reference
	      	  	$prevous_result = str_replace(self::PATTERN_CHILD_CLASS, self::CLASS_CHILD_ITEM, $prevous_result);
	        	$prevous_result = str_replace(self::PATTERN_LIST_CLASS, self::CLASS_INVISIBLE_LIST, 
	        		$prevous_result);
	        }
	    }
	    return $result;
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */  
	public function widget( $args, $instance ) {
		if ( is_active_widget(false, false, $this->id_base,true) ) {
			$this->init_widget($instance);
		}
		if (is_category()) {
    		$category = get_category(get_query_var('cat'));
    		$this->current_category_id = $category->cat_ID;
		}
		// Put all output inside aside semantic tag
		echo '<aside id="'. $args['widget_id'] .'" class="' . self::CLASS_WIDGET_MAIN . '">'."\n";
		if ( $this->title ) {
			echo $args['before_title'] . $this->title . $args['after_title'];
		}
		// Output main part categories menu
		echo self::traverse_term_tree();
		// Close aside tag
		echo '</aside>'."\n";
	}
	private function parse_instance($instance) {
		$this->title = sanitize_text_field( isset($instance[self::TITLE]) ? $instance[self::TITLE] : ''  );
		$this->show_post_count = isset($instance[self::SHOW_POST_COUNT]) ? (bool) $instance[self::SHOW_POST_COUNT] :true;
		$this->show_empty_categories = isset( $instance[self::SHOW_EMPTY_CATEGORIES] ) ? (bool) $instance[self::SHOW_EMPTY_CATEGORIES] : true;
		$this->use_bootstrap_style = isset( $instance[self::USE_BOOTSTRAP_STYLE] ) ? (bool) $instance[self::USE_BOOTSTRAP_STYLE] : true;
	}
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$this->parse_instance($instance);
		?>
		<p><label for="<?php echo $this->get_field_id(self::TITLE); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id(self::TITLE); ?>" name="<?php echo $this->get_field_name(self::TITLE); ?>" type="text" value="<?php echo esc_attr( $this->title ); ?>" /></p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id(self::SHOW_EMPTY_CATEGORIES); ?>" name="<?php echo $this->get_field_name(self::SHOW_EMPTY_CATEGORIES); ?>"<?php checked($this->show_empty_categories ); ?> />
		<label for="<?php echo $this->get_field_id(self::SHOW_EMPTY_CATEGORIES); ?>"><?php _e( 'Display empty categories' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id(self::SHOW_POST_COUNT); ?>" name="<?php echo $this->get_field_name(self::SHOW_POST_COUNT); ?>"<?php checked($this->show_post_count); ?> />
		<label for="<?php echo $this->get_field_id(self::SHOW_POST_COUNT); ?>"><?php _e( 'Show post counts' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id(self::USE_BOOTSTRAP_STYLE); ?>" name="<?php echo $this->get_field_name(self::USE_BOOTSTRAP_STYLE); ?>"<?php checked($this->use_bootstrap_style); ?> />
		<label for="<?php echo $this->get_field_id(self::USE_BOOTSTRAP_STYLE); ?>"><?php _e( 'Use bootstrap style' ); ?></label><br />

		<?php
	}
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[self::TITLE] = sanitize_text_field( $new_instance[self::TITLE] );
		$instance[self::SHOW_POST_COUNT] = !empty($new_instance[self::SHOW_POST_COUNT]) ? 1 : 0;
		$instance[self::SHOW_EMPTY_CATEGORIES] = !empty($new_instance[self::SHOW_EMPTY_CATEGORIES]) ? 1 : 0;
		$instance[self::USE_BOOTSTRAP_STYLE] = !empty($new_instance[self::USE_BOOTSTRAP_STYLE]) ? 1 : 0;
		return $instance;
	}
}
?>